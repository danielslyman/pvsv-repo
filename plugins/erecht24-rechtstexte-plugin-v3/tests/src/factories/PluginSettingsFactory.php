<?php

namespace eRecht24\LegalTexts\tests\src\factories;

use eRecht24\LegalTexts\App\Service\WpOptionManager;
use WP_UnitTest_Factory_For_Thing;

class PluginSettingsFactory extends WP_UnitTest_Factory_For_Thing
{
    /**
     * @var array
     */
    private $default_options;

    public function __construct($factory = null)
    {
        parent::__construct($factory);
        $this->default_options = WpOptionManager::ALL_OPTIONS;
    }

    public function create_object($options)
    {
        foreach ($options as $key => $option) {
            $wpOptionManager = new WpOptionManager($key);
            $wpOptionManager->setOption($option)->save();
        }
    }

    public function update_object($object, $fields)
    {
        // TODO: Implement update_object() method.
    }

    public function get_object_by_id($object_id)
    {
        // TODO: Implement get_object_by_id() method.
    }
}