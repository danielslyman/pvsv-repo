<?php

namespace eRecht24\LegalTexts\Inc\frontend;

use eRecht24\LegalTexts\App\Service\WpOptionManager;
use const eRecht24\LegalTexts\PLUGIN_NAME_DIR;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author    eRecht24
 */
class Frontend
{

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
     * @since       2.0.0
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;

        add_action('wp_head', array($this, 'wl_add_google_analytics'), 2);
        add_action('wp_head', array($this, 'wl_add_google_optout_analytics'), 1);

	    add_action( 'init', array($this, 'register_gutenberg_block') );
	    add_action( 'enqueue_block_editor_assets', array($this, 'enque_gutenberg_editor_js') );
	    add_action( 'enqueue_block_editor_assets', array($this, 'set_script_translations' ));

        global $wp_version;
        // do not use filter 'block_categories' after version 5.8.0, it is deprecated
        if (version_compare($wp_version, '5.8.0', '<'))
            add_filter('block_categories', array($this, 'register_gutenberg_category'), 10, 2);
        else
            add_filter('block_categories_all', array($this, 'register_gutenberg_category'), 10, 2);

        add_shortcode( 'erecht24', array($this, 'shortcode_erecht24') );

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_styles()
    {

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
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts()
    {

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
    }

	public function enque_gutenberg_editor_js() {
		$block_name      = 'erecht24';
		// The JS block script
		$script_file = 'js/wl-erecht24-gutenberg-de.js';
		wp_enqueue_script(
			$block_name,
			plugin_dir_url( __FILE__ ) . $script_file,
			[   // Dependencies that will have to be imported on the block JS file
				'wp-blocks', // Required: contains registerBlockType function that creates a block
				'wp-element', // Required: contains element function that handles HTML markup
				'wp-editor', // Required: contains RichText component for editable inputs
				'wp-i18n', // contains registerBlockType function that creates a block
			],
			filemtime( plugin_dir_path( __FILE__ ) . $script_file )
		);
	}

	public function register_gutenberg_block() {
		// Registering the block
		$block_name      = 'erecht24';
		$block_namespace = 'erecht24/' . $block_name;
		register_block_type(
			$block_namespace,
			[
				'editor_script' => 'erecht24',
				'render_callback' => array($this, 'render_shortcode_in_gutenberg'),
				'attributes'      => array(
					'lang' => array(
						'type'    => 'string',
					),
					'type' => array(
						'type'    => 'string',
					),
					'strip_title' => array(
						'type'    => 'boolean',
					)
				)
			]
		);
	}

	public function render_shortcode_in_gutenberg($atts) {
		return $this->shortcode_erecht24($atts);
	}

	public function register_gutenberg_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'erecht24-blocks',
					'title' => __( 'eRecht24', $this->plugin_text_domain ),
				),
			)
		);
	}

	public function set_script_translations() {
		wp_set_script_translations( 'erecht24', $this->plugin_text_domain, PLUGIN_NAME_DIR . 'languages' );
	}

    public function shortcode_erecht24($atts)
    {
        $atts = shortcode_atts(array(
            'type' => 'imprint',
            'strip_title' => false,
            'lang' => 'de',
        ), $atts, 'erecht24');

	    $atts['strip_title'] = filter_var( $atts['strip_title'], FILTER_VALIDATE_BOOLEAN );
        $type = $atts['type'];

        $options = get_option('erecht24_' . $type . '_settings');

	    $content = '<div style="word-wrap: break-word;">';
        if (isset($options['document_source']) && $options['document_source']) {
	        $content .= $options['document_' . $atts['lang'] . '_local'] ?? __('Not found', 'erecht24');
        } else {
	        $content .= $options['document_' . $atts['lang'] . '_remote'] ?? __('Not found', 'erecht24');
        }

        if ($atts['strip_title'] === true) {
	        $content = $this->wl_delete_all_between('<h1>', '</h1>', $content);
        }

        if (empty($content)) {
            return __('Unfortunately no legal text was found. Please check your eRecht24 settings.', 'erecht24');
        }

        $content .= '</div>';

        return $content;
    }

    /**
     * Helper to remove not needed content between two strings (e.g html tags)
     */
    private function wl_delete_all_between($beginning, $end, $string)
    {
        $beginningPos = stripos($string, $beginning);
        $endPos = stripos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return str_replace($textToDelete, '', $string); //dont use substr here, as it is slower then str_replace
    }

    /**
     * Add Google Analytics OptOut if chosen in options
     */
    public function wl_add_google_optout_analytics()
    {
	    $options = new WpOptionManager(WpOptionManager::WP_OPTION_GOOGLE_ANALYTICS);

	    $google_key = $options->getSubOption('google_analytics_key');
        if ($options->getSubOption('google_analytics_opt_out')) {
            ?>
            <script>

                /**
                 * Google OutOut Script
                 */
                var gaProperty = '<?php echo $google_key ?>';
                var disableStr = 'ga-disable-' + gaProperty;
                if (document.cookie.indexOf(disableStr + '=true') > -1) {
                    window[disableStr] = true;
                }

                function gaOptout() {
                    document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
                    window[disableStr] = true;
                    alert('<?php echo __( 'Tracking by Google Analytics has been deactivated in your browser for this website.', $this->plugin_text_domain );?>');
                }

            </script>
            <?php
        }
    }

    /**
     * Add Google Analytics Tracking if chosen in options
     */
    public function wl_add_google_analytics()
    {
	    $options = new WpOptionManager(WpOptionManager::WP_OPTION_GOOGLE_ANALYTICS);

        // check if enabled
	    if(!$options->getSubOption('google_analytics_enabled'))
		    return;

        $google_key = $options->getSubOption('google_analytics_key');
        if(!$google_key || 0 === strlen($google_key) )
            return;

        $version = (0 === strpos(strtolower($google_key), 'g')) ? 4 : 3;
        $google_analytics_usercentrics = $options->getSubOption('google_analytics_usercentrics');
        if ($google_analytics_usercentrics) {
            ?>
            <script type="text/plain" data-usercentrics="Google Analytics" async src="//www.googletagmanager.com/gtag/js?id=<?php echo $google_key ?>"></script>
            <script type="text/plain" data-usercentrics="Google Analytics">
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                <?php if(4 === $version): ?>
                     gtag('config', '<?php echo $google_key ?>');
                <?php else: ?>
                     gtag('config', '<?php echo $google_key ?>', { 'anonymize_ip': true });
                <?php endif; ?>
            </script>
            <?php
        } else {
            ?>
            <script async src="//www.googletagmanager.com/gtag/js?id=<?php echo $google_key ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                <?php if(4 === $version): ?>
                    gtag('config', '<?php echo $google_key ?>');
                <?php else: ?>
                    gtag('config', '<?php echo $google_key ?>', { 'anonymize_ip': true });
                <?php endif; ?>
            </script>
            <?php
        }
    }
}
