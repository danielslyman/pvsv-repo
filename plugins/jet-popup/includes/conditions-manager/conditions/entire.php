<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Entire extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'entire';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Entire Site', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'entire';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 100;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * @param $value
	 *
	 * @return string|void
	 */
	public function get_label_by_value( $value = '' ) {
		return __( 'Entire Site', 'jet-popup' );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {
		return true;
	}

}
