<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\Inc\Core;

use eRecht24\LegalTexts\App\Api\Client as ApiClient;
use eRecht24\LegalTexts\App\Cron\TrimErrorLog;
use eRecht24\LegalTexts\App\Helper;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 *
 * @author    eRecht24
 **/
class Deactivator
{
    /**
     * Plugin Deactivator
     * @param $networkWide
     */
	public function execute($networkWide) : void
    {
	    foreach (Helper::getAffectedWebsiteIds($networkWide) as $id) {
            if (is_multisite()) {
                switch_to_blog($id);
            }
            $this->deleteApiClient();
            self::unregisterCron();
            if (is_multisite()) {
                restore_current_blog();
            }
        }
    }

    /**
     * Function deletes old api client remotely
     * @todo handle response
     */
    private function deleteApiClient() : void
    {
        $apiKey = Helper::getApiKey();
        $clientId = Helper::getClientId();
        if (!$apiKey || !$clientId)
            return;

        $apiClient = new ApiClient($apiKey);
        $apiClient->deleteClient($clientId);
    }

	/**
	 * Function unregisters cron
	 */
	public static function unregisterCron() : void
	{
		$timestamp = wp_next_scheduled( TrimErrorLog::CRON_ACTION_IDENTIFIER );
		wp_unschedule_event( $timestamp, TrimErrorLog::CRON_ACTION_IDENTIFIER );
	}
}
