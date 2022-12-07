<?php

namespace JET_ABAF\Rest_API;

use JET_ABAF\Plugin;

class Endpoint_Booked_Dates extends \Jet_Engine_Base_API_Endpoint {

	public function get_name() {
		return 'booked-dates';
	}

	public function callback( $request ) {

		$params = $request->get_params();
		$item   = ! empty( $params['item'] ) ? $params['item'] : array();

		if ( empty( $item ) ) {
			return rest_ensure_response( [
				'success' => false,
				'data'    => __( 'No data to check booked dates.', 'jet-booking' ),
			] );
		}

		if ( empty( $item['apartment_id'] ) ) {
			return rest_ensure_response( [
				'success' => false,
				'data'    => __( 'Incorrect item data.', 'jet-booking' ),
			] );
		}

		$booked_dates = Plugin::instance()->engine_plugin->get_off_dates( $item['apartment_id'] );

		return rest_ensure_response( [
			'success'          => true,
			'booked_dates'     => $booked_dates,
			'disabled_days'    => Plugin::instance()->engine_plugin->get_disabled_days( $item['apartment_id'] ),
			'booked_next'      => Plugin::instance()->engine_plugin->get_next_booked_dates( $booked_dates ),
			'checkout_only'    => Plugin::instance()->settings->checkout_only_allowed(),
			'per_nights'       => Plugin::instance()->engine_plugin->is_per_nights_booking(),
			'labels'           => Plugin::instance()->settings->get_labels(),
			'custom_labels'    => Plugin::instance()->settings->get( 'use_custom_labels' ),
			'weekly_bookings'  => Plugin::instance()->settings->get( 'weekly_bookings' ),
			'week_offset'      => Plugin::instance()->settings->get( 'week_offset' ),
			'one_day_bookings' => Plugin::instance()->settings->is_one_day_bookings(),
		] );

	}

	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function get_method() {
		return 'POST';
	}

	public function get_args() {
		return [];
	}

}