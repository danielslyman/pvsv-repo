<?php

namespace eRecht24\LegalTexts\Inc\admin;

use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;
use eRecht24\LegalTexts\App\View\Admin\WpOptionPage;
use Exception;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since 2.0.0
 *
 * @author    eRecht24
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param string $plugin_text_domain The text domain of this plugin.
	 *
	 * @since 2.0.0
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->plugin_text_domain = $plugin_text_domain;

        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );


        add_filter( 'mce_external_plugins', array( $this, 'enqueue_tinymce_scripts' ) );
        add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons_editor' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wl-erecht24-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        $screen = get_current_screen();

        // Check screen base and page
        if ( 'settings_page_erecht24-settings' === $screen->base )
        {
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wl-erecht24-admin.js', array( 'jquery' ), $this->version, false );
            wp_localize_script( $this->plugin_name, 'erecht24Data', array(
                'locale' => substr( get_locale(), 0, 2 ),
                'plugin_title' => __('eRecht24 Legal texts for WordPress', 'erecht24')
            ) );
        }
	}

	/**
	 * Enqueue a custom  JavaScript for the TinyMCE Editor
	 */
	public function enqueue_tinymce_scripts( $plugin_array ) {
		$plugin_array['erecht24'] = plugin_dir_url( __FILE__ ) . 'js/wl-erecht24-tinymce.js';

		return $plugin_array;
	}

	/**
	 * Register the button in the TinyMCE Editor
	 *
	 * @param $buttons
	 *
	 * @return array
	 */
	public function register_tinymce_buttons_editor( $buttons ) {
		$buttons[] = 'erecht24';

		return $buttons;
	}

    /**
     * Register settings link in plugin list
     *
     * @param $buttons
     *
     * @return array
     */
    public function add_additional_action_link ( $links ) {
        $settingsLinks = array(
            '<a href="'.esc_url( get_admin_url(null, 'options-general.php?page=erecht24-settings') ).'">'. __('Settings', 'erecht24') . '</a>',
        );
        return array_merge(  $settingsLinks, $links );
    }

	/**
	 * Adds admin notices
	 */
	public function admin_notices() {
		if ( !Helper::getApiKey() ) {
			?>
            <div class="notice notice-error NoAPI-KEY">
                <p><?php echo __('eRecht24 Legal texts for WordPress', 'erecht24') . ': ' . __( 'No API key was set', 'erecht24' ) ?>. <a
                            href="<?php echo admin_url( 'options-general.php?page=erecht24-settings&tab=api_key' ) ?>"><?php _e( 'Configure API-Key', 'erecht24' ) ?></a>
                </p>
            </div>
			<?php
		}

		$messageOptions = get_option( 'erecht24_api_messages' );

		if ( isset( $messageOptions ) && ! empty( $messageOptions ) ) {
			?>
            <div class='notice notice-info is-dismissible erecht24-notice'>
				<?php
				if ( get_locale() === 'de_DE' ) {
					echo '<p>' . __('eRecht24 Legal texts for WordPress', 'erecht24') . ': ' . $messageOptions['message_de'] . '</p>';
					if ( ! empty( $messageOptions['call2action_de'] ) ) {
						echo '<p><a class="button button-primary" href="options-general.php?page=erecht24-settings">' . $messageOptions['call2action_de'] . '</a></p>';
					}
				} else {
					echo '<p>' . __('eRecht24 Legal texts for WordPress', 'erecht24') . ': ' . $messageOptions['message'] . '</p>';
					if ( ! empty( $messageOptions['call2action'] ) ) {
						echo '<p><a class="button button-primary" href="options-general.php?page=erecht24-settings">' . $messageOptions['call2action'] . '</a></p>';
					}
				}
				?>
            </div>
			<?php
		}

        /**
         * Search for old shortcodes if user has not disabled the warning
         */
        $currentUserID = wp_get_current_user()->get("ID");

        $permanentWarningBan = get_option("permanentHideShortcodeWarning-UserID-".$currentUserID);
        $temporaryWarningBan = get_transient("temporaryHideShortcodeWarning-UserID-".$currentUserID);


        if (!$permanentWarningBan and !$temporaryWarningBan){
            global $wpdb;
            $result = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_type NOT IN ('revision', 'nav_menu_item') AND ($wpdb->posts.post_content like '%[impressum%]%' OR $wpdb->posts.post_content like '%[datenschutz%]%')" );

            if (count($result) > 0) {
                ?>
                <div  class='notice notice-info is-dismissible erecht24-notice deprecatedShortCodeWarning'>
                    <?php
                        if ( is_plugin_active( 'wl-erecht24/wl-erecht24.php' ) ) {
                            echo '<p>';
                            _e( 'Your eRecht24 legal texts plugin for WordPress has been successfully updated. The new version offers you a wider range of functions. During the installation, we automatically transferred your existing data from the previous version of the eRecht24 Legal Texts plugin into the new configuration. Please check whether your imprint and your data protection declaration are still displayed correctly on your website. If you discover any problems, please refer to the documentation of the plugin.', 'erecht24' );
                            echo '</p>';
                        }
                    ?>
                    <p>
                        <?php _e('On the following pages/post an old eRecht24 shortcode is still available. Search for <code>[impressum]</code> or <code>[datenschutz]</code> in the content and replace them with the new shortcodes. You can find detailed instructions <a href="options-general.php?page=erecht24-settings&tab=documentation">here</a>.', 'erecht24'); ?>
                    </p>
                    <ul class="notice-posts-list">
                    <?php
                        foreach ($result as $aPost) {
                            echo '<li><a href="'. get_edit_post_link($aPost->ID).'">' . $aPost->post_title .'</a></li>';
                        }

                    ?>
                    </ul>
                    <p>
		                <?php _e('To permanently hide this message press <a href="" id="banOldShortcodeWarning">here</a>.',"erecht24") ?>
                    </p>
                </div>
                <?php
            }
        }
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
	    $page = new WpOptionPage(WpOptionPage::TEMPLATE_PATH);
		// This page will be under "Settings"
		add_options_page(
			__( 'eRecht24 Settings', 'erecht24' ),
			__( 'eRecht24 Settings', 'erecht24' ),
			'manage_options',
			'erecht24-settings',
			array( $page, 'render' )
		);
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		$this->createApiKeySettings();
		$this->createSettings( 'imprint', 'Imprint' );
		$this->createSettings( 'privacy_policy', 'Privacy Policy' );
		$this->createSettings( 'privacy_policy_social_media', 'Privacy policy for social media profile' );
		$this->createGoogleAnalyticsSettings();

        wp_localize_script( 'wp-api', 'eRecht24ApiSettings', array( 'root' => esc_url_raw( rest_url() ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
        wp_enqueue_script('wp-api');
	}

	public function createApiKeySettings() {
		register_setting(
			'erecht24_api_key_group', // Option group
			'erecht24_api_key_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'erecht24_api_key', // ID
			__( 'API Key', 'erecht24' ), // Title
			array( $this, 'print_section_info_api_key' ), // Callback
			'erecht24_api_key_page' // Page
		);

		add_settings_field(
			'api_key', // ID
			__( 'API Key', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_api_key_page', // Page
			'erecht24_api_key', // Section
			array(
				'group'       => 'erecht24_api_key_settings',
				'name'        => 'api_key',
				'value'       => Helper::getApiKey(),
				'type'        => 'text',
				'description' => __( 'Enter here your API key from <a href="https://www.e-recht24.de/mitglieder/tools/projekt-manager/" target="_blank">eRecht24 project manager for websites</a>.', 'erecht24' )
			)
		);

		add_settings_field(
			'sync_all_data', // ID
			__( 'Syncronize', 'erecht24' ), // Title
			array( $this, 'button_callback' ), // Callback
			'erecht24_api_key_page', // Page
			'erecht24_api_key', // Section
			array(
				'title'     => __( 'Syncronize and save all legal texts', 'erecht24' ),
				'icon'      => 'dashicons-controls-play',
				'data_type' => 'sync_all',
				'buttonActionClass' => 'syncAllLegalTexts',
                'description' => __("This function is available once valid api-key has been entered.", "erecht24")
			)
		);
	}

	/**
	 * Create Settings pages
	 *
	 * @param string $type
	 * @param string $name
	 */
	public function createSettings( $type = 'imprint', $name = 'Imprint' ) {
	    $associatedWpOptionKey = Helper::getWpOptionByDocumentType($type);

        try {
            $wpOption = new WpOptionManager($associatedWpOptionKey);
        } catch (Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
            return;
        }

		register_setting(
			'erecht24_' . $type . '_group', // Option group
			'erecht24_' . $type . '_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'erecht24_' . $type, // ID
			__( $name, 'erecht24' ), // Title
			array( $this, 'print_section_info_' . $type ), // Callback
			'erecht24_' . $type . '_page' // Page
		);

		add_settings_field(
			'document_source', // ID
			__( 'Data source', 'erecht24' ), // Title
			array( $this, 'input_switch' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
                'data_type' => $type,
				'name'  => 'document_source',
                'value' =>  $wpOption->getSubOption('document_source')
			)
		);

		add_settings_field(
			'sync_data', // ID
			__( 'Syncronize', 'erecht24' ), // Title
			array( $this, 'button_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'title'     => __( 'Syncronize and save data', 'erecht24' ),
				'icon'      => 'dashicons-controls-play',
				'data_type' => $type
			)
		);

		add_settings_field(
			'importSyncToLocal', // ID
			__( 'Import', 'erecht24' ), // Title
			array( $this, 'button_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'title'     => __( 'Overwrite local data with latest synchronised data', 'erecht24' ),
				'icon'      => 'dashicons-controls-play',
				'data_type' => $type,
                'buttonActionClass' => 'importSyncToLocal',
			)
		);

		add_settings_field(
			'document_de_remote', // ID
			__( 'HTML Code (DE)', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_de_remote',
                'value' =>  $wpOption->getSubOption('document_de_remote'),
				'type'  => 'textarea',
				'class' => 'wl-readonly-on-server wl_source__server',
				'rows'  => 6
			)
		);

		add_settings_field(
			'document_de_last_update_remote', // ID
			__( 'Last updated', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_de_last_update_remote',
                'value' =>  $wpOption->getSubOption('document_de_last_update_remote'),
				'type'  => 'last_update',
				'class' => 'wl_source__server'
			)
		);

		add_settings_field(
			'document_de_local', // ID
			__( 'HTML Code (DE) - Local', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_de_local',
                'value' =>  $wpOption->getSubOption('document_de_local'),
				'type'  => 'wp_editor',
				'class' => 'wl-readonly-on-server wl_source__local',
				'rows'  => 6
			)
		);

		add_settings_field(
			'document_de_last_update_local', // ID
			__( 'Last updated', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_de_last_update_local',
                'value' =>  $wpOption->getSubOption('document_de_last_update_local'),
				'type'  => 'last_update',
				'class' => 'wl_source__local'
			)
		);

		add_settings_field(
			'document_en_remote', // ID
			__( 'HTML Code (EN)', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_en_remote',
                'value' =>  $wpOption->getSubOption('document_en_remote'),
				'type'  => 'textarea',
				'class' => 'wl-readonly-on-server wl_source__server',
				'rows'  => 6
			)
		);

		add_settings_field(
			'document_en_local', // ID
			__( 'HTML Code (EN) - Local', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_en_local',
                'value' =>  $wpOption->getSubOption('document_en_local'),
				'type'  => 'wp_editor',
				'class' => 'wl-readonly-on-server wl_source__local',
				'rows'  => 6
			)
		);

		add_settings_field(
			'document_en_last_update_remote', // ID
			__( 'Last updated', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_en_last_update_remote',
                'value' =>  $wpOption->getSubOption('document_en_last_update_remote'),
				'type'  => 'last_update',
				'class' => 'wl_source__server'
			)
		);

		add_settings_field(
			'document_en_last_update_local', // ID
			__( 'Last updated', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_' . $type . '_page', // Page
			'erecht24_' . $type, // Section
			array(
				'group' => 'erecht24_' . $type . '_settings',
				'name'  => 'document_en_last_update_local',
                'value' =>  $wpOption->getSubOption('document_en_last_update_local'),
				'type'  => 'last_update',
				'class' => 'wl_source__local'
			)
		);
	}

	/**
	 * Creates Google Settings
	 */
	public function createGoogleAnalyticsSettings() {
        try {
            $wpOption = new WpOptionManager(WpOptionManager::WP_OPTION_GOOGLE_ANALYTICS);
        } catch (Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
            return;
        }

		register_setting(
			'erecht24_google_analytics_group', // Option group
			'erecht24_google_analytics_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'erecht24_google_analytics', // ID
			__( 'Google Analytics', 'erecht24' ), // Title
			array( $this, 'print_section_info_google_analytics' ), // Callback
			'erecht24_google_analytics_page' // Page
		);

		add_settings_field(
			'google_analytics_key', // ID
			__( 'Google Analytics Tracking ID', 'erecht24' ), // Title
			array( $this, 'input_callback' ), // Callback
			'erecht24_google_analytics_page', // Page
			'erecht24_google_analytics', // Section
			array(
				'group'       => 'erecht24_google_analytics_settings',
				'name'        => 'google_analytics_key',
                'value' =>  $wpOption->getSubOption('google_analytics_key'),
				'type'        => 'text',
				'description' => __( 'Enter here your Google Analytics ID like G-ABCDABCD or UA-12345687-1', 'erecht24' ),
				'placeholder' => 'G-ABCDABCD'
			)
		);

		add_settings_field(
			'google_analytics_enabled', // ID
			__( 'Enable Google Analytics?', 'erecht24' ), // Title
			array( $this, 'input_switch' ), // Callback
			'erecht24_google_analytics_page', // Page
			'erecht24_google_analytics', // Section
			array(
				'group' => 'erecht24_google_analytics_settings',
				'name'  => 'google_analytics_enabled',
                'description' => false,
                'value' =>  $wpOption->getSubOption('google_analytics_enabled')
			)
		);

		add_settings_field(
			'google_analytics_usercentrics', // ID
			__('Enable Usercentrics consent management', 'erecht24'), // Title
			array($this, 'input_switch'), // Callback
			'erecht24_google_analytics_page', // Page
			'erecht24_google_analytics', // Section
			array(
				'group' => 'erecht24_google_analytics_settings',
				'name' => 'google_analytics_usercentrics',
				'description' => false,
				'individual_description' => __('If you use the Usercentrics Consent Management Platform on your website, you can activate this option. Thus, your Google Analytics source code is initially delivered in a deactivated state and only activated after the user has given the cookie consent via the cookie banner.', 'erecht24'),
				'value' =>  $wpOption->getSubOption('google_analytics_usercentrics')
			)
		);

		add_settings_field(
			'google_analytics_opt_out', // ID
			__( 'Enable Google Analytics Opt Out?', 'erecht24' ), // Title
			array( $this, 'input_switch' ), // Callback
			'erecht24_google_analytics_page', // Page
			'erecht24_google_analytics', // Section
			array(
				'group' => 'erecht24_google_analytics_settings',
				'name'  => 'google_analytics_opt_out',
				'description' => false,
                'value' =>  $wpOption->getSubOption('google_analytics_opt_out'),
                'individual_description' =>
                    '<div class="description" style="margin-top: 10px;">' .
                        __('If you <b>against our recommendation</b> do not obtain active consent for the use of Google Analytics, you should at least give the option to so-called opt-out. To do this, activate this option and proceed as follows:', 'erecht24') .
                    '<ol>' .
                        '<li>' .
                            __('Add the following text below your privacy policy: Disable Google Analytics', 'erecht24') .
                        '</li>' .
                        '<li>' .
                            __('Insert the following HTML code below this text:', 'erecht24') .'<span style="background: #f0f0f0; display: inline-block; padding: 3px 5px ">'.'&lt;a onclick="gaOptout();"&gt;'. __('Disable Google Analytics', 'erecht24') . '&lt;/a&gt;'.'</span>' .
                        '</li>' .
                        '<li>' .
                            __('WordPress may remove the Javascript function. Check on your website by clicking the opt-out button whether the cookie is set. If this button does not work and thus the opt-out cookie is not set after the click, your Google Analytics integration is <b>not legally compliant</b> without using a Consent tool!', 'erecht24') .
                        '</li>' .
                    '</ol>'.
                '</div>'
			)
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();

		// tab 1: api key
		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		// Imprint, privacy policy and privacy policy for social media
		if ( isset( $input['document_de_remote'] ) ) {
			$new_input['document_de_remote'] = $input['document_de_remote'];
		}
		if ( isset( $input['document_en_remote'] ) ) {
			$new_input['document_en_remote'] = $input['document_en_remote'];
		}
		if ( isset( $input['document_de_local'] ) ) {
			$new_input['document_de_local'] = $input['document_de_local'];
		}
		if ( isset( $input['document_en_local'] ) ) {
			$new_input['document_en_local'] = $input['document_en_local'];
		}
		if ( isset( $input['document_de_last_update_local'] ) ) {
			$new_input['document_de_last_update_local'] = sanitize_text_field( $input['document_de_last_update_local'] );
		}
		if ( isset( $input['document_en_last_update_local'] ) ) {
			$new_input['document_en_last_update_local'] = sanitize_text_field( $input['document_en_last_update_local'] );
		}
		if ( isset( $input['document_de_last_update_remote'] ) ) {
			$new_input['document_de_last_update_remote'] = sanitize_text_field( $input['document_de_last_update_remote'] );
		}
		if ( isset( $input['document_en_last_update_remote'] ) ) {
			$new_input['document_en_last_update_remote'] = sanitize_text_field( $input['document_en_last_update_remote'] );
		}
		if ( isset( $input['document_source'] ) ) {
			$new_input['document_source'] = (bool) $input['document_source'];
		}
		// tab 5: google analytics
		if ( isset( $input['google_analytics_key'] ) ) {
			$new_input['google_analytics_key'] =  sanitize_text_field($input['google_analytics_key']);
		}
		if ( isset( $input['google_analytics_opt_out'] ) ) {
			$new_input['google_analytics_opt_out'] = (bool) $input['google_analytics_opt_out'];
		}
		if ( isset( $input['google_analytics_enabled'] ) ) {
			$new_input['google_analytics_enabled'] = (bool) $input['google_analytics_enabled'];
		}
        if ( isset( $input['google_analytics_usercentrics'] ) ) {
            $new_input['google_analytics_usercentrics'] = (bool) $input['google_analytics_usercentrics'];
        }

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info_api_key() {
		print '<p class="introduction">' . __( 'Please enter the API key provided by eRecht24 and hit save.', 'erecht24' ) . '</p>';
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info_imprint() {
		print '<p class="introduction">' . __( 'Choose here whether you want to get the imprint data from eRecht24 server or to make your own local entries.', 'erecht24' ) . '</p>';
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info_privacy_policy() {
		print '<p class="introduction">' . __( 'Choose here whether you want to get the privacy police data from the eRecht24 server or to make your own local entries.', 'erecht24' ) . '</p>';
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info_privacy_policy_social_media() {
		print '<p class="introduction">' . __( 'Choose here whether you want to get the privacy police for social media profiles data from the eRecht24 server or to make your own local entries.', 'erecht24' ) . '</p>';
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info_google_analytics() {
		print '<p class="introduction">' . __( 'If you use Google Analytics, enter your Property ID here. The tracking code with opt-out will be automatically generated by the plugin.', 'erecht24' ) . '</p>';
	}

	/**
	 * @param array $args
	 */
	public function input_switch( Array $args ) {
		$option_group = $args['group'];
		$option_name  = $args['name'];
		$checked      = $args['value'];
		$description = $args['description'] ?? true;
        $disabled = $args['disabled'] ?? false;
        $individual_description = $args['individual_description'] ?? '';

		if ($description) {
		    echo '<div class="checkbox-description">';
            echo    '<span class="leftLabel" style="display: inline-block; padding-right: 5px">' . __( 'Project Manager', 'erecht24' ) . '</span>';
			echo    '<input type="checkbox" class="erecht24-ajax-option-toggle modernswitch" id="' . $option_name . '" name="' . $option_group . '[' . $option_name . ']" ' . checked( 1, $checked, false ) . ' '.($disabled ? 'disabled' : '').' data-option="'. $option_group .'" data-sub-option="'. $option_name .'" />';
			echo    '<span class="rightLabel" style="display: inline-block; padding-left: 5px">' . __( 'local Version', 'erecht24' ) . '</span>';
            echo '</div>';
		} else  {
			echo '<input type="checkbox" class="erecht24-ajax-option-toggle modernswitch" id="' . $option_name . '" name="' . $option_group . '[' . $option_name . ']" ' . checked( 1, $checked, false ) . ' '.($disabled ? 'disabled' : '').' value="1" data-option="'. $option_group .'" data-sub-option="'. $option_name .'" />';
		}
        if ($individual_description) {
	        echo '<div class="description" style="margin-top: 10px;">'.$individual_description.'</div>';
        }
	}

	/**
	 * @param array $args
	 */
	public function input_callback( Array $args ) {
		$input_type   = $args['type'] ?? 'text';
		$class        = $args['class'] ?? '';
		$placeholder  = $args['placeholder'] ?? '';
		$option_group = $args['group'];
		$option_name  = $args['name'];
		$value        = $args['value'];
		$checked      = 'checked="checked"';
		$description  = $args['description'] ?? '';

		// field
		if ( $input_type === 'textarea' ) {
		    echo '<textarea name="' . $option_group . '[' . $option_name . ']' . '" style="width: 100%;" rows="10">' . html_entity_decode( $value, ENT_HTML5 , 'UTF-8' )  . '</textarea>';

		    // TODO: Perhaps in the future
            //			wp_editor( html_entity_decode( $value ), $option_name, array(
            //				'editor_class'  => 'wp-editor',
            //				'tinymce'       => false,
            //				'teeny'         => false,
            //				'quicktags'     => true,
            //				'media_buttons' => false,
            //				'textarea_name' => $option_group . '[' . $option_name . ']'
            //			) );

		} elseif ( $input_type === 'wp_editor' ) {
			echo '<textarea name="' . $option_group . '[' . $option_name . ']' . '" style="width: 100%;" rows="10">' . html_entity_decode( $value, ENT_HTML5 , 'UTF-8' )  . '</textarea>';

            //			wp_editor( html_entity_decode( $value ), $option_name, array(
            //				'editor_class'  => 'wp-editor',
            //				'tinymce'       => array(
            //					'skin'            => 'lightgray',
            //					'theme'           => 'modern',
            //					'entity_encoding' => 'raw',
            //					'editor_height'   => 600
            //				),
            //				'teeny'         => false,
            //				'quicktags'     => true,
            //				'media_buttons' => false,
            //				'textarea_name' => $option_group . '[' . $option_name . ']'
            //			) );
		} elseif ( $input_type === 'last_update' ) {
			echo '<input type="text" class="' . $class . '" id="' . $option_name . '" name="' . $option_group . '[' . $option_name . ']" value="' . $value . '" ' . $checked . ' placeholder="' . $placeholder . '" readonly/>';
		} else {
			echo '<input type="' . $input_type . '" class="' . $class . '" id="' . $option_name . '" name="' . $option_group . '[' . $option_name . ']" value="' . $value . '" ' . $checked . ' placeholder="' . $placeholder . '" />';
		}

		echo '<p class="description">' . $description . '</p>';
	}

	/**
	 * @param array $args
	 */
	public function button_callback( Array $args ) {
		$title     = $args['title'] ?? '';
		$icon      = $args['icon'] ?? null;
		$data_type = $args['data_type'] ?? null;
		$buttonActionClass= $args['buttonActionClass'] ?? "wl-synchronize-button";
		$icon_html = '';
		$disabled = '';

		$api_key_options = get_option( 'erecht24_api_key_settings' );

		if ( !isset($api_key_options['api_key']) || ! $api_key_options['api_key'] ) {
		    $disabled = 'disabled';
		}

		if ( $icon ) {
			$icon_html = "<span class='dashicons $icon' style='margin-top: 3px'></span>";
		}
		echo '<button class="button button-primary '.$buttonActionClass.'" data-action="' . $data_type . '" ' . $disabled . '>' . $icon_html . '</span>' . $title . '</button>';
		if ( isset($api_key_options['description'])) {
			echo '<p class="description">'. $args["description"].'</p>';
		}
	}

	/**
	 *  Dismisses the api message
	 */
	public function dismiss_api_message() {
		if ( $_POST['temporaryDisableOldShortcodeWarning'] ) {
			$currentUserID = wp_get_current_user()->get("ID");
            set_transient("temporaryHideShortcodeWarning-UserID-".$currentUserID,true,900);
		}
		update_option( 'erecht24_api_messages', null );
        wp_send_json_success( 'success' );
	}
}
