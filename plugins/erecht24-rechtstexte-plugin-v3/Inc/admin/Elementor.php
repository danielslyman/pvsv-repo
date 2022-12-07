<?php

namespace eRecht24\LegalTexts\Inc\admin;

use Elementor\Plugin;
use eRecht24\LegalTexts\Inc\admin\Widgets\ElementorWidget;

/**
 * The main class that initiates and runs the plugin.
 *
 * @since 2.0.0
 */
class Elementor {
	/**
	 * Instance
	 *
	 * @since 2.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var ElementorWidget The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ElementorWidget An instance of the class.
	 * @since 2.0.0
	 *
	 * @access public
	 * @static
	 *
	 */
	public static function instance() {

		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 */
	public function init() {
	    // @todo check elementor timing
        if ( did_action( 'elementor/loaded' ) ) {
			// Add Plugin actions
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		}
	}


	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {
		// Register widget
        $elementorWidget = new ElementorWidget();
		Plugin::instance()->widgets_manager->register_widget_type( $elementorWidget );
	}
}
