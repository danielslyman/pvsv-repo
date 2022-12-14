<?php
namespace JET_ABAF;

/**
 * Settings manager
 */
class Settings {

	/**
	 * Default settings array
	 *
	 * @var array
	 */
	private $defaults = array(
		'is_set'                   => false,
		'hide_columns_manager'     => false,
		'related_post_type'        => false,
		'wc_integration'           => false,
		'filters_store_type'       => 'session',
		'related_post_type_column' => '',
		'additional_columns'       => array(),
		'apartment_post_type'      => false,
		'booking_period'           => 'per_nights',
		'allow_checkout_only'      => false,
		'weekly_bookings'          => false,
		'week_offset'              => '',
		'one_day_bookings'         => false,
		'use_custom_labels'        => false,
		'hide_setup'               => false,
		'ical_synch'               => false,
		'labels_booked'            => 'Sold out',
		'labels_only_checkout'     => 'Only checkout',
		'labels_selected'          => 'Choosed:',
		'labels_nights'            => 'Nights',
		'labels_days'              => 'Days',
		'labels_apply'             => 'Close',
		'labels_week_1'            => 'Mon',
		'labels_week_2'            => 'Tue',
		'labels_week_3'            => 'Wed',
		'labels_week_4'            => 'Thu',
		'labels_week_5'            => 'Fri',
		'labels_week_6'            => 'Sat',
		'labels_week_7'            => 'Sun',
		'labels_month_name'        => 'January, February, March, April, May, June, July, August, September, October, November, December',
		'labels_past'              => 'Past',
		'labels_previous'          => 'Previous',
		'labels_prev_week'         => 'Week',
		'labels_prev_month'        => 'Month',
		'labels_prev_year'         => 'Year',
		'labels_default'           => 'Please select a date range',
		'synch_interval'           => 'daily',
		'synch_interval_hours'     => false,
		'synch_interval_mins'      => false,
		'days_off'                 => [],
		'disable_weekday_1'        => false,
		'disable_weekday_2'        => false,
		'disable_weekday_3'        => false,
		'disable_weekday_4'        => false,
		'disable_weekday_5'        => false,
		'disable_weekend_1'        => false,
		'disable_weekend_2'        => false,
	);

	/**
	 * Settings DB key
	 *
	 * @var string
	 */
	private $key = 'jet-abaf';

	/**
	 * Stored settings cache
	 *
	 * @var null
	 */
	private $settings = null;

	/**
	 * Stored labels
	 *
	 * @var null
	 */
	private $labels = null;

	/**
	 * [__construct description]
	 * @param array $pages [description]
	 */
	public function __construct() {
		add_action( 'wp_ajax_jet_abaf_save_settings', array( $this, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_jet_abaf_process_tables', array( $this, 'ajax_process_tables' ) );

		if ( is_admin() ) {
			$this->hook_db_columns();
		}

	}

	/**
	 * Save settings by ajax request
	 *
	 * @return [type] [description]
	 */
	public function ajax_save_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => 'Access denied',
			) );
		}

		$settings = ! empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : array();

		if ( empty( $settings ) ) {
			wp_send_json_error( array(
				'message' => 'Empty data',
			) );
		}

		if ( ! isset( $settings['additional_columns'] ) ) {
			$settings['additional_columns'] = array();
		}

		foreach ( $settings as $setting => $value ) {

			if ( $this->setting_registered( $setting ) ) {

				switch ( $setting ) {

					case 'additional_columns':
						$value = array_values( $value );
						break;

					case 'days_off':
						if ( ! is_array( $value ) ) {
							$value = false;
						}

						break;

					case 'use_custom_labels':
					case 'hide_setup':
					case 'hide_columns_manager':
					case 'wc_integration':
					case 'ical_synch':
					case 'weekly_bookings':
					case 'allow_checkout_only':
					case 'one_day_bookings':
					case 'disable_weekday_1':
					case 'disable_weekday_2':
					case 'disable_weekday_3':
					case 'disable_weekday_4':
					case 'disable_weekday_5':
					case 'disable_weekend_1':
					case 'disable_weekend_2':
						$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
						break;

				}

				$this->update( $setting, $value, false );

			}
		}

		do_action( 'jet-booking/settings/on-ajax-save', $this );

		$this->write();

		wp_send_json_success( array(
			'message' => esc_html__( 'Settings saved!', 'jet-booking' ),
		) );

	}

	/**
	 * AJAX callback for creatin/saving DB tables
	 *
	 * @return [type] [description]
	 */
	public function ajax_process_tables() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => 'Access denied',
			) );
		}

		$message = esc_html__( 'DB tables created!', 'jet-booking' );

		ob_start();

		try {

			if ( ! Plugin::instance()->db->is_units_table_exists() ) {
				Plugin::instance()->db->create_units_table();
			}

			if ( Plugin::instance()->db->is_bookings_table_exists() ) {
				$message = esc_html__( 'DB tables updated!', 'jet-booking' );
				Plugin::instance()->db->update_columns_diff( $this->get_clean_columns() );
			} else {
				$this->hook_db_columns();
				Plugin::instance()->db->install_table();
			}

		} catch ( \Exception $e ) {
			ob_get_clean();
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}

		$warnings = ob_get_clean();

		if ( ! empty( $warnings ) ) {
			wp_send_json_error( array( 'message' => $warnings ) );
		} else {
			wp_send_json_success( array( 'message' => $message ) );
		}

	}

	/**
	 * Get preapred additional columns list
	 *
	 * @return array
	 */
	public function get_clean_columns() {

		$columns       = $this->get( 'additional_columns' );
		$clean_columns = array();

		if ( empty( $columns ) ) {
			return $clean_columns;
		}

		foreach ( $columns as $column ) {
			if ( ! empty( $column['column'] ) ) {
				$clean_columns[] = $this->sanitize_column_name( $column['column'] );
			}
		}

		return $clean_columns;

	}

	public function sanitize_column_name( $column ) {
		return sanitize_key( str_replace( array( ' ', '-' ), '_', $column ) );
	}

	/**
	 * Hook new DB columns
	 * @return [type] [description]
	 */
	public function hook_db_columns() {

		$columns = $this->get_clean_columns();

		if ( empty( $columns ) ) {
			return;
		}

		add_filter( 'jet-abaf/db/additional-db-columns', function( $db_columns ) use ( $columns ) {

			if ( empty( $db_columns ) || ! is_array( $db_columns ) ) {
				$db_columns = array();
			}

			foreach ( $columns as $column ) {
				if ( is_array( $column ) && ! empty( $column['column'] ) ) {
					$db_columns[] = $column['column'];
				} else {
					$db_columns[] = $column;
				}
			}

			return $db_columns;

		} );

	}

	/**
	 * Sanitize updated working hours
	 *
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function sanitize_setting( $setting, $value ) {

		switch ( $setting ) {

			case 'additional_columns':
				$value = array_values( $value );
				break;

			case 'use_custom_labels':
			case 'hide_setup':
			case 'hide_columns_manager':
			case 'wc_integration':
			case 'ical_synch':
			case 'weekly_bookings':
			case 'one_day_bookings':
			case 'disable_weekday_1':
			case 'disable_weekday_2':
			case 'disable_weekday_3':
			case 'disable_weekday_4':
			case 'disable_weekday_5':
			case 'disable_weekend_1':
			case 'disable_weekend_2':
				$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				break;

		}

		return $value;
	}

	/**
	 * Passe settings.
	 *
	 * Processes settings before localization.
	 *
	 * @since  2.4.7
	 * @access public
	 *
	 * @param array $settings Plugin settings list.
	 *
	 * @return mixed
	 */
	public function passe_settings( $settings ) {

		if ( empty( $settings ) ) {
			return $settings;
		}

		if ( isset( $settings['days_off'] ) && ! empty( $settings['days_off'] ) ) {
			$new_days_off = [];

			foreach ( $settings['days_off'] as $value ) {
				if ( ! isset( $value["date"] ) ){
					$new_days_off[] = $value;
					continue;
				}

				$start          = $value["date"];
				$startTimeStamp = strtotime( $start );
				$new_days_off[] = [
					'start'          => $start,
					'startTimeStamp' => $startTimeStamp,
					'end'            => $start,
					'endTimeStamp'   => $startTimeStamp,
					'name'           => $value["name"],
					'type'           => 'days_off',
				];
			}

			$settings['days_off'] = $new_days_off;
		}

		return $settings;

	}

	/**
	 * Returna all available labels list
	 *
	 * @return [type] [description]
	 */
	public function get_labels( $key = null ) {

		if ( null === $this->labels ) {
			$this->labels = array(
				'booked'          => $this->get( 'labels_booked' ),
				'only_checkout'   => $this->get( 'labels_only_checkout' ),
				'selected'        => $this->get( 'labels_selected' ),
				'nights'          => $this->get( 'labels_nights' ),
				'days'            => $this->get( 'labels_days' ),
				'apply'           => $this->get( 'labels_apply' ),
				'week-1'          => $this->get( 'labels_week_1' ),
				'week-2'          => $this->get( 'labels_week_2' ),
				'week-3'          => $this->get( 'labels_week_3' ),
				'week-4'          => $this->get( 'labels_week_4' ),
				'week-5'          => $this->get( 'labels_week_5' ),
				'week-6'          => $this->get( 'labels_week_6' ),
				'week-7'          => $this->get( 'labels_week_7' ),
				'month-name'      => $this->get_array_from_string( $this->get( 'labels_month_name' ) ),
				'past'            => $this->get( 'labels_past' ),
				'previous'        => $this->get( 'labels_previous' ),
				'prev-week'       => $this->get( 'labels_prev_week' ),
				'prev-month'      => $this->get( 'labels_prev_month' ),
				'prev-year'       => $this->get( 'labels_prev_year' ),
				'default-default' => $this->get( 'labels_default' ),
			);
		}

		if ( ! $key ) {
			return $this->labels;
		} else {
			return isset( $this->labels[ $key ] ) ? $this->labels[ $key ] : null;
		}

	}

	/**
	 * Parse array from strig
	 *
	 * @return [type] [description]
	 */
	public function get_array_from_string( $string ) {
		$string = str_replace( ', ', ',', $string );
		return explode( ',', $string );
	}

	/**
	 * Return all settings and setup settings cache
	 *
	 * @return [type] [description]
	 */
	public function get_all() {

		if ( null === $this->settings ) {

			$this->settings = get_option( $this->key, array() );

			if ( ! is_array( $this->settings ) || empty( $this->settings ) ) {
				$this->settings = $this->defaults;
			} else {
				foreach ( $this->settings as $key => $value ) {
					$this->settings[ $key ] = $this->sanitize_setting( $key, $value );
				}
			}

			$this->settings = $this->passe_settings( $this->settings );

		}

		if ( empty( $this->settings['additional_columns'] ) ) {
			$this->settings['additional_columns'] = array();
		}

		$this->settings['additional_columns'] = array_values( $this->settings['additional_columns'] );

		return $this->settings;

	}

	/**
	 * Get.
	 *
	 * Returns setting by key.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $setting Setting key.
	 *
	 * @return mixed
	 */
	public function get( $setting ) {

		$settings = $this->get_all();

		if ( isset( $settings[ $setting ] ) ) {
			$value = $this->sanitize_setting( $setting, $settings[ $setting ] );
		} else {
			$value = isset( $this->defaults[ $setting ] ) ? $this->defaults[ $setting ] : false;
		}

		return apply_filters( 'jet-booking/settings/get/' . $setting, $value );

	}

	public function checkout_only_allowed() {
		return ( 'per_nights' === Plugin::instance()->settings->get( 'booking_period' ) && Plugin::instance()->settings->get( 'allow_checkout_only' ) );
	}

	/**
	 * Is one day booking.
	 *
	 * Check if one day booking option available and enable.
	 *
	 * @since  2.5.2
	 * @access public
	 *
	 * @return bool
	 */
	public function is_one_day_bookings() {
		return ( 'per_nights' !== Plugin::instance()->settings->get( 'booking_period' ) && Plugin::instance()->settings->get( 'one_day_bookings' ) );
	}

	/**
	 * Update setting in cahce and database
	 *
	 * @param  [type]  $setting [description]
	 * @param  boolean $write   [description]
	 * @return [type]           [description]
	 */
	public function update( $setting = null, $value = null, $write = true ) {

		$this->get_all();
		$this->settings[ $setting ] = $value;

		if ( $write ) {
			$this->write();
		}

	}

	/**
	 * Clear option in DB
	 *
	 * @return [type] [description]
	 */
	public function clear() {
		delete_option( $this->key );
	}

	/**
	 * Write settings cache
	 * @return [type] [description]
	 */
	public function write() {

		/**
		 * Modify options before write into DB
		 */
		do_action( 'jet-abaf/settings/before-write', $this );

		update_option( $this->key, $this->settings, false );
	}

	/**
	 * Check if passed settings is registered in defaults
	 *
	 * @return [type] [description]
	 */
	public function setting_registered( $setting = null ) {
		return ( $setting && isset( $this->defaults[ $setting ] ) );
	}

}