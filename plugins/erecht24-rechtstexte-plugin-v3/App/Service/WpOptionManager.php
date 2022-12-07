<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service;

use eRecht24\LegalTexts\App\Helper;

class WpOptionManager
{
    const WP_OPTION_API_CLIENT = 'erecht24_api_client_settings';
    const WP_OPTION_API_KEY = 'erecht24_api_key_settings';
    const WP_OPTION_GOOGLE_ANALYTICS = 'erecht24_google_analytics_settings';
    const WP_OPTION_IMPRINT = 'erecht24_imprint_settings';
    const WP_OPTION_PRIVACY_POLICY = 'erecht24_privacy_policy_settings';
    const WP_OPTION_SOCIAL_MEDIA = 'erecht24_privacy_policy_social_media_settings';
    const WP_OPTION_WP_SETTINGS = 'erecht24_wp_settings';

    const ALL_OPTIONS = [
        self::WP_OPTION_API_KEY => [
            'api_key' => ''
        ],
        self::WP_OPTION_API_CLIENT => [
            'secret'    => '',
            'client_id' => 0
        ],
        self::WP_OPTION_GOOGLE_ANALYTICS => [
            'google_analytics_key'     => '',
            'google_analytics_enabled' => false,
            'google_analytics_opt_out' => false,
            'google_analytics_usercentrics' => false,
        ],
        self::WP_OPTION_IMPRINT => [
            'document_de_remote'             => '',
            'document_en_remote'             => '',
            'document_de_local'              => '',
            'document_en_local'              => '',
            'document_de_last_update_local'  => '',
            'document_en_last_update_local'  => '',
            'document_de_last_update_remote' => '',
            'document_en_last_update_remote' => '',
            'document_source'                => false
        ],
        self::WP_OPTION_PRIVACY_POLICY => [
            'document_de_remote'             => '',
            'document_en_remote'             => '',
            'document_de_local'              => '',
            'document_en_local'              => '',
            'document_de_last_update_local'  => '',
            'document_en_last_update_local'  => '',
            'document_de_last_update_remote' => '',
            'document_en_last_update_remote' => '',
            'document_source'                => false
        ],
        self::WP_OPTION_SOCIAL_MEDIA => [
            'document_de_remote'             => '',
            'document_en_remote'             => '',
            'document_de_local'              => '',
            'document_en_local'              => '',
            'document_de_last_update_local'  => '',
            'document_en_last_update_local'  => '',
            'document_de_last_update_remote' => '',
            'document_en_last_update_remote' => '',
            'document_source'                => false
        ],
        self::WP_OPTION_WP_SETTINGS => [
            'push_uri' => '',
        ],
    ];

    private $optionKey;
    private $option;
    private $databaseValue;

    /**
     * WpOptionManager constructor.
     * @param string $wpOption
     * @throws \Exception
     */
    public function __construct(
        string $wpOption
    ) {
        if (!array_key_exists($wpOption, self::ALL_OPTIONS)) {
            throw new \Exception('Unsupported wp_option specified. Abort.');
        }

        $this->optionKey = $wpOption;
        $this->databaseValue = null;
        $this->option = [];
    }

    /**
     * Function provides current wp option
     * @return array|null
     */
    public function getOption(): array
    {
        $oldOption = $this->getFromDatabase();
        foreach ($this->getDefaults() as $key => $default) {
            // default value
            $value = $default;

            // old value
            if (array_key_exists($key, $oldOption))
                $value = $oldOption[$key];

            // new value
            if (array_key_exists($key, $this->option))
                $value = $this->option[$key];

            $this->option[$key] = $value;
        }

        return $this->option;
    }

    /**
     * Function provides suboption
     * @param string $key
     * @return false|mixed
     */
    public function getSubOption(
        string $key
    ) {
        return $this->getOption()[$key] ?? false;
    }

    /**
     * Function updates wp option in database
     */
    public function save() : void
    {
        $newOption = $this->getOption();
        if ($newOption === $this->getFromDatabase())
            return;

        update_option(
            $this->optionKey,
            $newOption
        );

        // update class cache
        $this->databaseValue = $newOption;
    }

    /**
     * Function resets wp option in database
     */
    public function reset() : void
    {
        update_option(
            $this->optionKey,
            $this->getDefaults()
        );
    }

	/**
	 * Function saves changes to be made
	 * @param array $option
	 * @param bool $sanitize
	 * @return $this
	 */
    public function setOption(
        array $option,
		bool $sanitize = true
    ) : WpOptionManager
    {
    	$defaults = $this->getDefaults();
        foreach ($option as $key => $value) {
            if (array_key_exists($key, $defaults)) {
            	if ($sanitize)
            		$value = Helper::sanitizeByType($value, gettype($defaults[$key]));

                $this->option[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Function provides database option and sub-option values
     * @return array|null
     */
    private function getFromDatabase() : array
    {
        if (is_null($this->databaseValue)) {
            $fromDatabase = get_option($this->optionKey, []) ?? [];
            // if data is invalid, WP's get_option function returns false. In this case we return an empty array
            $this->databaseValue = (!is_array($fromDatabase)) ? [] : $fromDatabase;
        }

        return $this->databaseValue;
    }

    /**
     * Function provides default option and sub-option values
     * @return array
     */
    private function getDefaults() : array
    {
        return self::ALL_OPTIONS[$this->optionKey];
    }
}