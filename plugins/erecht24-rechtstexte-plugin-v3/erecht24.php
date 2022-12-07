<?php
/**
 * @link              https://www.e-recht24.de/
 * @since             2.0.0
 * @package           eRecht24
 *
 * @wordpress-plugin
 * Plugin Name:       eRecht24 legal texts for WordPress
 * Plugin URI:        https://www.e-recht24.de/
 * Description:       This plugin allows the easy integration of imprint and privacy policy of eRecht24.
 * Version:           3.3.6
 * Author:            eRecht24, Weslink GmbH
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       erecht24
 * Domain Path:       /languages
 * Requires PHP:      7.1
 */

namespace eRecht24\LegalTexts;

use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\Inc\Core\Activator;
use eRecht24\LegalTexts\Inc\Core\Deactivator;
use eRecht24\LegalTexts\Inc\Core\Init;
use eRecht24\LegalTexts\Inc\Core\Updater;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
define( __NAMESPACE__ . '\PLUGIN_NAME_DIR',  plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoload Classes
 */
require_once( PLUGIN_NAME_DIR . 'vendor/autoload.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */
$activator = new Activator();
register_activation_hook(__FILE__, [$activator, 'execute']);

/**
 * Register Update Hooks
 */
$updater = new Updater();
add_action('upgrader_process_complete', [$updater, 'execute']);

/**
 * Register a Hook if new sub page is created & initialized
 */
add_action('wp_initialize_site', [$activator, 'handleSiteCreation']);

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */
$deactivator = new Deactivator();
register_deactivation_hook(__FILE__, [$deactivator, 'execute']);

/**
 * The code that runs during plugin uninstallation.
 * This action is documented inc/core/class-uninstall.php
 */
register_uninstall_hook( __FILE__, array( NS . 'Inc\Core\Uninstall', 'uninstall' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0.0
 */
class eRecht24 {

	/**
	 * The instance of the plugin.
	 *
	 * @since    1.0.0
	 * @var      Init $init Instance of the plugin.
	 */
	private static $init;
	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null === self::$init ) {
			self::$init = new Init();
			self::$init->run();
		}

		return self::$init;
	}
}

$min_php = '7.1.0';
// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
    eRecht24::init();
}

// Initiate the plugin update checker
Helper::includeUpdateCheckerFiles();

$updateChecker = new \Puc_v4p11_Vcs_PluginUpdateChecker(
    new \Puc_v4p11_Vcs_GitLabApi('https://git.muensmedia.de/erecht24/rechtstexte-plugin/erecht24-rechtstexte-plugin', null, 'rechtstexte-plugin'),
    __FILE__,
    'erecht24'
);

$updateChecker->setAuthentication('n96yn4PFAaw7w8e6Y4q1');
$updateChecker->setBranch('master');

// Add icons for Dashboard > Updates screen.
$updateChecker->addResultFilter(function ($info, $response = null) {
    $info->icons = array(
        '1x' => plugins_url('assets/icon-128x128.png', __FILE__),
        '2x' => plugins_url('assets/icon-256x256.png', __FILE__),
    );
    return $info;
});


