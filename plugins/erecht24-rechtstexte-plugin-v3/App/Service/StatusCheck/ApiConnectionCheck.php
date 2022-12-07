<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client;
use eRecht24\LegalTexts\App\Helper;

class ApiConnectionCheck implements StatusCheckInterface
{
    /**
     * Check server connection
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse
    {
        $apiKey = Helper::getApiKey();
        if (!$apiKey)
            return $this->noApiKeyResponse();


        $apiClient = new Client($apiKey);
        $checkResponse = $apiClient->listClients();
        if ( !$checkResponse->isSuccess() )
            return $this->noConnectionResponse($checkResponse);


        return new StatusCheckResponse(
            true,
            __('Connection to eRecht24 server could be established successfully.', Helper::PLUGIN_TEXT_DOMAIN),
            $checkResponse
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function noApiKeyResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('No Api key available. Please add open tab "API key" and add your api key.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @param ApiResponse $checkResponse
     * @return StatusCheckResponse
     */
    private function noConnectionResponse(
        ApiResponse $checkResponse
    ) : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            Helper::getBestFittingApiErrorMessage(
                $checkResponse->getData(),
                __('Connection to the eRecht24 server could not be established. Please contact the admin.', Helper::PLUGIN_TEXT_DOMAIN)
            ),
            $checkResponse
        );
    }
}