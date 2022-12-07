<?php

namespace eRecht24\LegalTexts\Inc\Core;

use eRecht24\LegalTexts\App\Helper;

/**
 * Fired during plugin uninstallation
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @author    eRecht24
 **/
class Uninstall {

	/**
	 *
	 * @since    2.0.0
	 */
	public static function uninstall() {
	    foreach (Helper::getAffectedWebsiteIds(true) as $id) {
            if (is_multisite()) {
                switch_to_blog($id);
            }
            delete_option( 'erecht24_error' );
            delete_option( 'erecht24_api_key_settings' );
            delete_option( 'erecht24_api_client_settings' );
            delete_option( 'erecht24_google_analytics_settings' );
            delete_option( 'erecht24_imprint_settings' );
            delete_option( 'erecht24_privacy_policy_settings' );
            delete_option( 'erecht24_privacy_policy_social_media_settings' );
            delete_option( 'external_updates-erecht24' );
            delete_option( 'imprint_text' );
            delete_option( 'privacy_text' );
            delete_option( 'google_tracking_id' );
            delete_option( 'google_script' );
            delete_option( 'google_optout_script' );
            if (is_multisite()) {
                restore_current_blog();
            }
        }
	}
}
