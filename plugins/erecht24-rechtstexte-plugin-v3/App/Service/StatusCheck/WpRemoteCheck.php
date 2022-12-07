<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

use eRecht24\LegalTexts\App\Helper;
use WP_Error;

class WpRemoteCheck implements StatusCheckInterface
{
    /**
     * @var mixed
     */
    private $connection;

    /**
     * WpRemoteCheck constructor.
     * @param $connection
     */
    public function __construct(
        $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * check function wp_remote_request
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse
    {
        $success = $this->checkConnection();
        $msg = ($success)
            ? __('wp_remote_request is working properly.', Helper::PLUGIN_TEXT_DOMAIN)
            : __('wp_remote_request is not working properly. Please check your php.ini or contact your hosting provider.', Helper::PLUGIN_TEXT_DOMAIN);

        return new StatusCheckResponse(
            $success,
            $msg
        );
    }

    /**
     * Analyze class data
     * @return bool
     */
    private function checkConnection() : bool
    {
        if (is_bool($this->connection))
            return $this->connection;

        if (is_null($this->connection))
            return $this->checkManually();

        if ($this->connection instanceof WP_Error)
            return false;

        return true;
    }

    /**
     * check connection manually
     * @return bool
     */
    private function checkManually() : bool
    {
        $request = wp_remote_request('https://de.wordpress.com/');
        return !($request instanceof WP_Error);
    }
}