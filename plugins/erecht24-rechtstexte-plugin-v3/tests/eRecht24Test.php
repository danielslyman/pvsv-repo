<?php

class eRecht24Test extends WP_UnitTestCase
{
    /**
     * Check that the activation hook got registered
     */
    public function testActivationHookIsRegistered()
    {
        $this->assertTrue(has_action('activate_erecht24/erecht24.php'), 'Activation hook should registered.');
    }

    /**
     * Check that the deactivation hook got registered
     */
    public function testDeactivationHookIsRegistered()
    {
        $this->assertTrue(has_action('deactivate_erecht24/erecht24.php'), 'Deactivation hook should registered.');
    }
}
