<?php

namespace JET_ABAF\Dashboard\Post_Meta;

use JET_ABAF\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Custom_Schedule_Meta extends Base_Vue_Meta_Box {

	/**
	 * CPT slug.
	 *
	 * Holds apartment custom post type slug.
	 *
	 * @since  2.5.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $cpt_slug;

	public function __construct() {

		$this->cpt_slug = Plugin::instance()->settings->get( 'apartment_post_type' );
		$this->meta_key = 'jet_abaf_custom_schedule';

		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_' . $this->meta_key , [ $this, 'save_post_meta' ] );
		}

		parent::__construct( $this->cpt_slug );

	}

	/**
	 * Default meta.
	 *
	 * Returns default custom schedule meta values.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @return array List on default value.
	 */
	public function get_default_meta() {
		return [
			'custom_schedule' => [
				'use_custom_schedule' => false,
				'days_off'            => [],
				'disable_weekday_1'   => false,
				'disable_weekday_2'   => false,
				'disable_weekday_3'   => false,
				'disable_weekday_4'   => false,
				'disable_weekday_5'   => false,
				'disable_weekend_1'   => false,
				'disable_weekend_2'   => false,
			],
		];
	}

	/**
	 * Parse settings.
	 *
	 * Parsed custom schedule data before written to the database and after get from the database.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @return array List of parsed settings.
	 */
	public function parse_settings( $settings ) {

		foreach ( $settings['custom_schedule'] as $key => $value ) {
			switch ( $key ) {
				case 'days_off':
					if ( ! is_array( $value ) ) {
						$settings['custom_schedule'][ $key ] = false;
					}
					break;

				case 'use_custom_schedule':
				case 'disable_weekday_1':
				case 'disable_weekday_2':
				case 'disable_weekday_3':
				case 'disable_weekday_4':
				case 'disable_weekday_5':
				case 'disable_weekend_1':
				case 'disable_weekend_2':
					$settings['custom_schedule'][ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
					break;

				default:
					$settings['custom_schedule'][ $key ] = $value;
					break;
			}
		}

		return $settings;

	}

	/**
	 * Assets.
	 *
	 * Include scripts and styles.
	 *
	 * @since  2.5.0
	 * @access public
	 */
	public function assets() {

		if ( ! $this->is_cpt_page() ) {
			return;
		}

		parent::assets();

		wp_enqueue_script(
			'jet-abaf-schedule-manager',
			$this->plugin_url . 'assets/js/admin/schedule-manager.js',
			[],
			$this->plugin_version,
			true
		);

		wp_enqueue_script(
			'jet-abaf-meta-custom-schedule',
			$this->plugin_url . 'assets/js/admin/meta-custom-schedule.js',
			[ 'jet-abaf-meta-extras', 'jet-abaf-schedule-manager', 'vuejs-datepicker' ],
			$this->plugin_version,
			true
		);

	}

	/**
	 * Vue templates.
	 *
	 * Return vue templates.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @return array List of vue templates.
	 */
	protected function get_vue_templates() {
		return [
			[
				'dir'  => 'jet-abaf-settings',
				'file' => 'settings-schedule',
			],
		];
	}

	/**
	 * Add meta box.
	 *
	 * Add a meta box to post.
	 *
	 * @since  2.5.0
	 * @access public
	 */
	public function add_meta_box() {

		if ( ! $this->is_cpt_page() ) {
			return;
		}

		add_meta_box(
			$this->meta_key,
			esc_html__( 'Custom Schedule', 'jet-booking' ),
			[ $this, 'meta_box_callback' ],
			[ $this->cpt_slug ],
			'normal',
			'high'
		);

	}

	/**
	 * Meta box callback.
	 *
	 * Includes meta box template.
	 *
	 * @since  2.5.0
	 * @access public
	 */
	public function meta_box_callback() {
		require_once( $this->plugin_path . 'templates/admin/post-meta/custom-schedule-meta.php' );
	}

}