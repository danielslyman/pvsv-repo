<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client;
use eRecht24\LegalTexts\App\Helper;

class PingPongCheck implements StatusCheckInterface
{
    /**
     * check if eRecht24 Server can push data
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse
    {
        $apiClient = new Client(Helper::getApiKey());
        $response = $apiClient->testPushPing(Helper::getClientId());

        if (!$response->isSuccess())
            return $this->errorResponse($response);

        return new StatusCheckResponse(
            true,
            __('The eRecht24 Server is able to push new data to your wordpress site.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @param ApiResponse $response
     * @return StatusCheckResponse
     */
    private function errorResponse(
        ApiResponse $response
    ) : StatusCheckResponse
    {
        $message = Helper::getBestFittingApiErrorMessage(
            $response->getData(),
            __('The eRecht24 Server is unable to push new data to your wordpress site. A new site url or htaccess protection can cause those issues. Please contact the admin.', Helper::PLUGIN_TEXT_DOMAIN),
            $response->getData('client_message') ?? __('No additional information')
        );

        return new StatusCheckResponse(
            false,
            $message
        );
    }
}