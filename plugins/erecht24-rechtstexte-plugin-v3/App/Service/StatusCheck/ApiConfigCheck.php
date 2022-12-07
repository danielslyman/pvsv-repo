<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class ApiConfigCheck implements StatusCheckInterface
{
    private $data;

    /**
     * ApiConfigCheck constructor.
     * @param ApiResponse|null $data
     */
    public function __construct(
        ?ApiResponse $data
    ) {
        $this->data = $data;
    }

    /**
     * Check if wp config matches eRecht24 server config
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse
    {
        /* @var $data ApiResponse */
        $data = $this->data;
        if ( !($data instanceof ApiResponse) )
            return $this->apiConnectionErrorResponse();

        $apiSecret = Helper::getSecret();
        $apiClientId = Helper::getClientId();
        if (!$apiClientId || !$apiSecret)
            return $this->wrongClientSettingsResponse();

        if (empty($data->getData() ?? []) )
            return $this->wrongClientSettingsRemoteResponse();

        $success = false;
        foreach ($data->getData() as $client) {
            if ($client['client_id'] == $apiClientId)
                $success = true;
        }

        if (!$success)
            return $this->missConfigurationResponse();

        return new StatusCheckResponse(
            true,
            __('The Api interface is configured correctly.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function apiConnectionErrorResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('No Api key available. Please add open tab "API key" and add your api key.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function missConfigurationResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('The data from your database is different from the data on the eRecht24 server. Please click the button "Refresh Connection".', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function wrongClientSettingsResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('The Api Client settings in your database are faulty. Please click the button "Refresh Connection". ', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function wrongClientSettingsRemoteResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('The remote Api Client settings are faulty. Please click the button "Refresh Connection". ', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }
}