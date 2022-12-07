<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App;

use eRecht24\LegalTexts\App\Service\WpOptionManager;
use const eRecht24\LegalTexts\PLUGIN_NAME_DIR;


class Helper
{
    const PLUGIN_NAME = 'wl-erecht24';
    const PLUGIN_VERSION = '3.3.6';
    const PLUGIN_TEXT_DOMAIN = 'erecht24';

    const API_HOST_URL = 'https://api.e-recht24.de';

    const REST_NAMESPACE = 'erecht24/v1';

	/**
	 * @var int Counts log entries per request
	 */
    static $logCounter = 1;

    /**
     * Log an exception to wp_options and delete exceptions older than 14 days
     * @param string $error
     * @todo delete via cron
     */
    public static function erecht24_log_error($error, ?array $apiResponse = null) : void
    {
	    $error_log =  json_decode(get_option('erecht24_error_log', '{}'), true);

	    $key = date("Y-m-d H:i:s - ", time()) .  self::$logCounter;
        $error_log[$key] = (string) $error;

        // add api response in case we have an api response
        if ($apiResponse !== null)
            $error_log[$key] .= " - ".json_encode($apiResponse);

	    self::$logCounter++;

        $error_log = array_slice($error_log, -70, 70, true);
        update_option('erecht24_error_log', json_encode($error_log, JSON_UNESCAPED_UNICODE));
    }

	/**
	 * Function provides best fitting api message
	 * @param array|null $apiResponse
	 * @param string $default
	 * @param string $suffix
	 * @return string
	 */
    public static function getBestFittingApiErrorMessage(
        ?array $apiResponse = null,
        string $default = '',
		string $suffix = ''
    ) : string
    {
        if ( substr( get_locale(), 0, 3 ) === 'de_'
            && $apiResponse
            && array_key_exists('message_de', $apiResponse)) {
            $error_message = $apiResponse['message_de'];
        }
        elseif ($apiResponse && array_key_exists('message', $apiResponse)) {
            $error_message = $apiResponse['message'];
        }
        elseif ($default) {
            $error_message = $default;
        } else {
            $error_message = __(
                'An Error occurred, Please try again later. If the error persists contact the admin.',
                self::PLUGIN_TEXT_DOMAIN
            );
        }

        if ($suffix) {
        	$error_message .= sprintf(' - %s', $suffix);
        }

        self::erecht24_log_error($error_message, $apiResponse);

        return $error_message;
    }

    /**
     * Function provides api key from database
     */
    public static function getApiKey() : string
    {
        $wpOptionManager = new WpOptionManager(WpOptionManager::WP_OPTION_API_KEY);
        return strval($wpOptionManager->getSubOption('api_key'));
    }

    /**
     * Function provides secret from database
     */
    public static function getSecret() : string
    {
        $wpOptionManager = new WpOptionManager(WpOptionManager::WP_OPTION_API_CLIENT);
        return strval($wpOptionManager->getSubOption('secret'));
    }

    /**
     * Function provides client id from database
     */
    public static function getClientId() : ?int
    {
        $wpOptionManager = new WpOptionManager(WpOptionManager::WP_OPTION_API_CLIENT);
        return intval($wpOptionManager->getSubOption('client_id'));
    }

    /**
     * Make camelcase from underscore
     * @todo remove asap
     * @param string $string
     * @return string
     */
    public static function getCamelCase(
        string $string
    ) : string
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        $str[0] = strtolower($str[0]);
        return $str;
    }

    /**
     * Function provides corresponding wp option depending on document type
     * @param string $documentType
     * @return string|null
     */
    public static function getWpOptionByDocumentType(
        string $documentType
    ) : ?string
    {
        switch (strtolower($documentType)) {
            case 'imprint':
                return WpOptionManager::WP_OPTION_IMPRINT;
            case 'privacy_policy':
            case 'privacypolicy':
                return WpOptionManager::WP_OPTION_PRIVACY_POLICY;
            case 'privacypolicysocialmedia':
            case 'privacy_policy_social_media':
                return WpOptionManager::WP_OPTION_SOCIAL_MEDIA;
        }

        return null;
    }

    /**
     * Function provides translated document type
     * @param string $documentType
     * @return string|null
     */
    public static function getTranslatedDocumentType(
        string $documentType
    ) : ?string
    {
        switch ($documentType) {
            case 'imprint':
                return __('Imprint', self::PLUGIN_TEXT_DOMAIN);
            case 'privacy_policy':
            case 'privacyPolicy':
	            return __('Privacy policy', self::PLUGIN_TEXT_DOMAIN);

            case 'privacyPolicySocialMedia':
            case 'privacy_policy_social_media':
	            return __('Privacy policy for social media profile', self::PLUGIN_TEXT_DOMAIN);
        }

        return $documentType;
    }

    /**
     * Function provides collection of website ids
     * @param $network_wide
     * @return array
     */
    public static function getAffectedWebsiteIds($network_wide) : array
    {
        $websiteIds = [];

        if (is_multisite() && $network_wide) {
            foreach (get_sites() as $site) {
                array_push($websiteIds, (int) $site->id);
            }
        } else {
            array_push($websiteIds, (int) get_current_blog_id());
        }

        return $websiteIds;
    }

	/**
	 * Sanitize value by type
	 * @param $value
	 * @param string $type
	 * @return bool|int|string
	 */
	public static function sanitizeByType(
		$value,
		string $type
	) {
    	switch ($type) {
		    case 'integer':
		    	return intval($value);
		    case 'boolean':
		    	return boolval($value);
		    case 'string':
			    return sanitize_text_field(strval($value));
		    default:
			    return '';
	    }
	}

	public static function includeUpdateCheckerFiles() {
		$requiredClasses = [
			'\Puc_v4p11_Factory' => 'Puc/v4p11/Factory.php',
			'\Puc_v4p11_Metadata' => 'Puc/v4p11/Metadata.php',
			'\Puc_v4p11_InstalledPackage' => 'Puc/v4p11/InstalledPackage.php',
			'\Puc_v4p11_Update' => 'Puc/v4p11/Update.php',
			'\Puc_v4p11_UpdateChecker' => 'Puc/v4p11/UpdateChecker.php',
			'\Puc_v4p11_Autoloader' => 'Puc/v4p11/Autoloader.php',
			'\Puc_v4p11_OAuthSignature' => 'Puc/v4p11/OAuthSignature.php',
			'\Puc_v4p11_Scheduler' => 'Puc/v4p11/Scheduler.php',
			'\Puc_v4p11_StateStore' => 'Puc/v4p11/StateStore.php',
			'\Puc_v4p11_UpgraderStatus' => 'Puc/v4p11/UpgraderStatus.php',
			'\Puc_v4p11_Utils' => 'Puc/v4p11/Utils.php',
			'\Puc_v4p11_Plugin_Info' => 'Puc/v4p11/Plugin/Info.php',
			'\Puc_v4p11_Plugin_Package' => 'Puc/v4p11/Plugin/Package.php',
			'\Puc_v4p11_Plugin_Ui' => 'Puc/v4p11/Plugin/Ui.php',
			'\Puc_v4p11_Plugin_Update' => 'Puc/v4p11/Plugin/Update.php',
			'\Puc_v4p11_Plugin_UpdateChecker' => 'Puc/v4p11/Plugin/UpdateChecker.php',
			'\Puc_v4p11_Theme_Package' => 'Puc/v4p11/Theme/Package.php',
			'\Puc_v4p11_Theme_Update' => 'Puc/v4p11/Theme/Update.php',
			'\Puc_v4p11_Theme_UpdateChecker' => 'Puc/v4p11/Theme/UpdateChecker.php',
			'\Puc_v4p11_Vcs_Api' => 'Puc/v4p11/Vcs/Api.php',
			'\Puc_v4p11_Vcs_BaseChecker' => 'Puc/v4p11/Vcs/BaseChecker.php',
			'\Puc_v4p11_Vcs_GitLabApi' => 'Puc/v4p11/Vcs/GitLabApi.php',
			'\Puc_v4p11_Vcs_PluginUpdateChecker' => 'Puc/v4p11/Vcs/PluginUpdateChecker.php',
			'\Puc_v4p11_Vcs_Reference' => 'Puc/v4p11/Vcs/Reference.php',
			'\Puc_v4p11_Vcs_ThemeUpdateChecker' => 'Puc/v4p11/Vcs/ThemeUpdateChecker.php',
		];

		foreach ($requiredClasses as $className => $phpfile) {
			if (!class_exists($className))
				require_once(PLUGIN_NAME_DIR . 'vendor/yahnis-elsts/plugin-update-checker/' . $phpfile);
		}
	}
}
