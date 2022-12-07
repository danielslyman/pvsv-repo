<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\Inc\Core;

use eRecht24\LegalTexts\App\Api\Client as ApiClient;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;
use WP_Site;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @author     eRecht24
 **/
class Activator {

    /**
     * Plugin Activator
     *
     * @param $networkWide
     */
    public function execute($networkWide) : void
    {
        $this->validateActivation();

        $this->updateDatabase($networkWide);

        $this->handleOldReleases();
    }

    /**
     * Function adds base settings for newly created site
     * @param $id
     */
    public function handleSiteCreation($id) : void
    {
        if (is_int($id)) {
            $this->createBaseSettings($id);
            return;
        }

        if ($id instanceof WP_Site) {
            $this->createBaseSettings($id->id);
            return;
        }
    }

    /**
     * Update Database for affected websites
     * @param $networkWide
     */
    private function updateDatabase($networkWide) : void
    {
        foreach (Helper::getAffectedWebsiteIds($networkWide) as $id) {
            $this->createBaseSettings($id);
            $this->activateApiClient($id);
        }
    }

    /**
     * Add / update all needed wp_options for given website
     * @param int $id
     */
    private function createBaseSettings(
        int $id
    ) : void
    {
        if (is_multisite()) {
            switch_to_blog($id);
        }
        foreach ( array_keys(WpOptionManager::ALL_OPTIONS) as $option ) {
            try {
                $wpOptionManager = new WpOptionManager($option);
                $wpOptionManager->save();
            } catch (\Exception $exception) {
                Helper::erecht24_log_error($exception->getMessage());
            }
        }
        if (is_multisite()) {
            restore_current_blog();
        }
    }

    /**
     * Function registers api client remotely for given website
     * @todo handle response
     * @param int $id
     */
    private function activateApiClient(
        int $id
    ) : void
    {
        if (is_multisite()) {
            switch_to_blog($id);
        }

        $apiKey = Helper::getApiKey();
        if (!$apiKey)
            return;

        $apiClient = new ApiClient($apiKey);
        $apiClient->addClient();

        if (is_multisite()) {
            restore_current_blog();
        }
    }

    /**
     * Check if activation Script can run
     */
    private function validateActivation()
    {
        // Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
        $min_php = '7.1.0';
        if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
        }
    }

    /**
     * Function handles old releases
     */
    private function handleOldReleases() : void
    {
        // Deactivate and uninstall old plugin
        $oldPlugin = plugin_basename( trim( 'wl-erecht24/wl-erecht24.php' ) );
        if ( is_plugin_active( $oldPlugin ) ) {
            deactivate_plugins( $oldPlugin );
            delete_option( 'privacy_text' );
            delete_option( 'google_tracking_id' );
            delete_option( 'google_script' );
            delete_option( 'google_optout_script' );
            delete_option( 'imprint_text' );
        }
    }
}
