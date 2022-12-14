<?php


namespace JET_ABAF\Form_Fields;


use JET_ABAF\Plugin;

/**
 * @method getArgs( $key = '', $ifNotExist = false, $wrap_callable = false )
 * @method isRequired()
 * @method isNotEmptyArg( $key )
 * @method getCustomTemplate( $provider_id, $args )
 * @method scopeClass( $suffix = '' )
 * @method is_block_editor()
 * @method get_queried_post_id()
 *
 * Trait Check_In_Out_Render_Trait
 *
 * @package JET_ABAF\Form_Fields
 */
trait Check_In_Out_Render_Trait {

	/**
	 * Field Template.
	 *
	 * Print check-in-out field template.
	 *
	 * @since  2.3.0
	 * @since  2.5.2 Current date check for `$searched` dates.
	 * @since  2.5.5 Updated date check code.
	 * @access public
	 *
	 * @return false|string
	 * @throws \Exception
	 */
	public function field_template() {

		$layout     = $this->getArgs( 'cio_field_layout', 'single', 'esc_attr' );
		$store_type = Plugin::instance()->settings->get( 'filters_store_type' );

		if ( 'single' === $layout ) {
			$template = JET_ABAF_PATH . 'templates/form-field-single.php';
		} else {
			$template = JET_ABAF_PATH . 'templates/form-field-separate.php';
		}

		if ( 'session' === $store_type ) {
			$searched = Plugin::instance()->session->get( 'searched_dates' );
		} else {
			$searched = Plugin::instance()->cookies->get( 'searched_dates' );
		}

		$options         = [];
		$default_value   = $searched ? $searched : $this->getArgs( 'default', '', 'esc_attr' );
		$field_format    = $this->getArgs( 'cio_fields_format', 'YYYY-MM-DD', 'esc_attr' );
		$field_separator = $this->getArgs( 'cio_fields_separator', '', 'esc_attr' );
		$start_of_week   = $this->getArgs( 'start_of_week', 'monday', 'esc_attr' );
		$return_value    = $this->getArgs( 'cio_return_value', 'days_num', 'esc_attr' );
		$fields_position = $this->getArgs( 'cio_fields_position', 'inline' );
		$default_format  = $field_format;

		// Allow customize check-in-out field default value.
		$default_value = apply_filters( 'jet-booking/form-fields/check-in-out/default-value', $default_value );

		$options['start_of_week'] = 'monday';

		if ( $default_format ) {
			switch ( $default_format ) {
				case 'YYYY-MM-DD':
					$default_format = 'Y-m-d';
					break;

				case 'MM-DD-YYYY':
					$default_format = 'm-d-Y';
					break;

				case 'DD-MM-YYYY':
					$default_format = 'd-m-Y';
					break;
			}
		}

		$options['date_format'] = $default_format ? $default_format : 'Y-m-d';

		if ( $field_separator ) {
			if ( 'space' === $field_separator ) {
				$field_separator = ' ';
			}

			$field_format = str_replace( '-', $field_separator, $field_format );
		}

		if ( trim( $default_value ) ) {
			$default_value = explode( ' - ', $default_value );

			if ( ! empty( $default_value[0] ) && ! empty( $default_value[1] ) ) {
				if ( '' !== $field_separator ) {
					$default_format = str_replace( '-', $field_separator, $default_format );
				}

				$current_date = date( 'Y-m-d' );
				$checkin      = date( 'Y-m-d', $default_value[0] );
				$checkout     = date( 'Y-m-d', $default_value[1] );
				$booked_range = $this->get_booked_dates_in_range( $checkin, $checkout );

				if ( $checkin >= $current_date && ! ( in_array( $checkin, $booked_range ) && in_array( $checkout, $booked_range ) ) ) {
					if ( in_array( $checkin, $booked_range ) ) {
						$checkin = strtotime( end( $booked_range ) . ' + 1 day' );
						reset( $booked_range );
					} else {
						$checkin = $default_value[0];
					}

					if ( in_array( $checkout, $booked_range ) ) {
						if ( Plugin::instance()->settings->checkout_only_allowed() ) {
							$checkout = $default_value[1];
						} else {
							$checkout = strtotime( $booked_range[0] . ' - 1 day' );
						}
					} else {
						if ( ! empty( $booked_range ) && ! in_array( date( 'Y-m-d', $default_value[0] ), $booked_range ) ) {
							$checkout = strtotime( $booked_range[0] . ' - 1 day' );
						} else {
							$checkout = $default_value[1];
						}
					}

					$options['checkin'] = date( $default_format, $checkin );

					if ( Plugin::instance()->settings->is_one_day_bookings() ) {
						$options['checkout'] = $options['checkin'];
					} else {
						$options['checkout'] = date( $default_format, $checkout );
					}
				}
			}
		}

		Plugin::instance()->engine_plugin->enqueue_deps( $this->get_queried_post_id() );

		wp_localize_script( 'jquery-date-range-picker', 'JetABAFInput', array(
			'layout'        => $layout,
			'field_format'  => $field_format,
			'start_of_week' => $start_of_week,
			'return_value'  => $return_value,
		) );

		$args = $this->getArgs();

		ob_start();

		include $template;

		return ob_get_clean();

	}

	/**
	 * Get booked dates in range.
	 *
	 * Returns list of booked, disabled and off dates in defined range.
	 *
	 * @since  2.5.5
	 * @access public
	 *
	 * @param $checkin
	 * @param $checkout
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_booked_dates_in_range( $checkin, $checkout ) {

		$start = new \DateTime( $checkin );
		$end = new \DateTime( $checkout );

		$end->modify( '+1 day' );

		$period = new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end );

		if ( ! $period ) {
			return [];
		}

		$booked_dates  = Plugin::instance()->engine_plugin->get_off_dates( $this->get_queried_post_id() );
		$disabled_days = Plugin::instance()->engine_plugin->get_disabled_days( $this->get_queried_post_id() );
		$booked_range  = [];

		foreach ( $period as $key => $value ) {
			if ( in_array( $value->format( 'Y-m-d' ), $booked_dates ) || in_array( $value->format( 'w' ), $disabled_days ) ) {
				$booked_range[] = $value->format( 'Y-m-d' );
			}
		}

		sort( $booked_range );

		return $booked_range;

	}

}