<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\Inc\Core;

use eRecht24\LegalTexts\App\Helper;
use Plugin_Upgrader;
use WP_Upgrader;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @author     eRecht24
 **/
class Updater
{
    /**
     * Plugin Updater
     * @param $upgrader_object WP_Upgrader
     * @param $options array
     */
    public function execute(
    	WP_Upgrader $upgrader_object,
	    array $options =[]
    ) : void
    {
        $this->handleUpdateTo3_0_1($upgrader_object, $options);
    }

	/**
	 * Handle Update to version 3.0.1
	 * @param $upgrader_object WP_Upgrader
	 * @param $options array
	 */
	private function handleUpdateTo3_0_1($upgrader_object, $options)
	{
		if(
			isset($upgrader_object->result) ??
			is_array($upgrader_object->result) ??
			array_key_exists('destination', $upgrader_object->result) &&
			$upgrader_object->result['destination'] == Helper::PLUGIN_TEXT_DOMAIN )
		{
			$upgrader_object instanceof Plugin_Upgrader &&
			$option = get_option('external_updates-erecht24', null);
			$update = $option->update ?? null;
			$version = $update->version ?? '0';

			if ( version_compare($version, '3.0.1', '<=') ) {
				Deactivator::unregisterCron();
			}
		}
	}
}
