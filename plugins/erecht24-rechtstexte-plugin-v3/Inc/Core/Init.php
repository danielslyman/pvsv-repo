<?php

namespace eRecht24\LegalTexts\Inc\Core;

use eRecht24\LegalTexts\App\Controller\Rest\Admin\AjaxUpdateSubOption;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\CheckApiKeyController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\GetDebugInformationController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\ImportAllDocumentsController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\ImportSingleDocumentController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\RefreshApiConnectionController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\RunStatusChecksController;
use eRecht24\LegalTexts\App\Controller\Rest\PushController;
use eRecht24\LegalTexts\App\Controller\Rest\Admin\SyncLocalTextController;

use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Hooks\Actions\UpdateOption;
use eRecht24\LegalTexts\Inc\admin\Admin;
use eRecht24\LegalTexts\Inc\admin\Elementor;
use eRecht24\LegalTexts\Inc\frontend\Frontend;

use const eRecht24\LegalTexts\PLUGIN_BASENAME;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since      2.0.0
 *
 * @author     eRecht24
 */
class Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The text domain of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $plugin_text_domain;

	/**
	 * Initialize and define the core functionality of the plugin.
	 */
	public function __construct()
    {
		$this->plugin_name = Helper::PLUGIN_NAME;
		$this->version = Helper::PLUGIN_VERSION;
		$this->plugin_basename = PLUGIN_BASENAME;
		$this->plugin_text_domain = Helper::PLUGIN_TEXT_DOMAIN;
        $this->loader = new Loader();

		$this->initialize();
    }

    /**
     * Init.
     */
    private function initialize() :void
    {
        $this->set_locale();
	    
        if (is_admin()) {
            $this->define_admin_hooks();
        } else {
            $this->define_frontend_hooks();
        }

        $this->define_global_hooks();

        $this->define_3rd_party_hooks();

        $this->define_rest_routes();

//        $this->defineCronJobs();
    }

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Internationalization_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {
		$plugin_i18n = new Internationalization_I18n( $this->plugin_text_domain );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin functionality of the plugin.
	 */
	private function define_admin_hooks() : void
    {
		$plugin_admin = new Admin ($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices');
		$this->loader->add_action('wp_ajax_dismiss_api_message', $plugin_admin, 'dismiss_api_message');
        $this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'add_additional_action_link' );

        // @todo check if needed
        add_action('wp_ajax_permanentHideShortcodeWarnings', function () {
            $currentUserID = wp_get_current_user()->get("ID");
            update_option("permanentHideShortcodeWarning-UserID-".$currentUserID,true);
            return wp_send_json(["success"=>true]);
        });

        // @todo maybe we will add it later again
//        $this->loader->add_action('update_option_site_url', $client_api, 'register_client');

		$optionUpdater =  new UpdateOption();
		$this->loader->add_action('pre_update_option_erecht24_imprint_settings', $optionUpdater, 'execute', 10, 2 );
		$this->loader->add_action('pre_update_option_erecht24_privacy_policy_settings', $optionUpdater, 'execute', 10, 2 );
		$this->loader->add_action('pre_update_option_erecht24_privacy_policy_social_media_settings', $optionUpdater, 'execute', 10, 2 );
    }

    /**
	 * Register all of the hooks related to the frontend and admin functionality of the plugin.
	 */
	private function define_frontend_hooks() : void
    {
        //
	}

    /**
     * Register all of the hooks related to the frontend functionality of the plugin.
     */
    private function define_global_hooks() : void
    {
        $plugin_public = new Frontend( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );
        $this->loader->add_shortcode( 'erecht24_widget', $plugin_public, 'shortcode_erecht24' );
    }

	/**
	 * Register Rest Routes
	 */
    private function define_rest_routes() : void
    {
        add_action('rest_api_init', function () {
            // @todo change to post
            $controller = new PushController();
            $controller->register_routes();

            $controller = new CheckApiKeyController();
            $controller->register_routes();

            $controller = new ImportSingleDocumentController();
            $controller->register_routes();

            $controller = new ImportAllDocumentsController();
            $controller->register_routes();

            $controller = new SyncLocalTextController();
            $controller->register_routes();

            $controller = new AjaxUpdateSubOption();
            $controller->register_routes();

            $controller = new RefreshApiConnectionController();
            $controller->register_routes();

            $controller = new GetDebugInformationController();
            $controller->register_routes();

            $controller = new RunStatusChecksController();
	        $controller->register_routes();
        });
    }

	/**
     * Register all of the hooks related to 3rd party dependencies
     */
    private function define_3rd_party_hooks() : void
    {
        $elementorWidget = new Elementor();
        $this->loader->add_action( 'plugins_loaded', $elementorWidget, 'init', 100 );
    }

	/**
	 * Define Cron jobs
	 */
	private function defineCronJobs(): void
	{
		//
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the text domain of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The text domain of the plugin.
	 */
	public function get_plugin_text_domain() {
		return $this->plugin_text_domain;
	}
}
