<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Actions;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class ClientCreated
{
    /**
     * Function handles actions after ERecht24 client was created remotely
     * @param ApiResponse|null $apiResponse
     */
    public function execute(
        ?ApiResponse $apiResponse
    ) : void
    {
        if ( !($apiResponse instanceof ApiResponse) )
            return;

         ($apiResponse->isSuccess())
             ? $this->storeClientData($apiResponse)
             : $this->handleError($apiResponse);
    }

    /**
     * Function stores api response data in database
     * @param ApiResponse $apiResponse
     */
    private function storeClientData(
        ApiResponse $apiResponse
    ) : void
    {
        // we redefine and sanitize client options here because hooks could have modified them
        $client_options = [
            'secret'    => sanitize_text_field($apiResponse->getData('secret')),
            'client_id' => sanitize_text_field($apiResponse->getData('client_id'))
        ];

        // check necessary data
        if (!$client_options['secret'] || !$client_options['client_id']) {
            Helper::erecht24_log_error('An Error occurred. A client was created remotely but not locally.');
            return;
        }

        // save url for push requests to detect wordpress domain changes
        $clientData = json_decode(Client::createRequestBody(), true);
        $wp_options = [
            'push_uri' => md5($clientData['push_uri']),
        ];

        // update database
        try {
            $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_API_CLIENT);
            $optionManger->setOption($client_options)->save();

            // save push_uri
            $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_WP_SETTINGS);
            $optionManger->setOption($wp_options)->save();
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
        }
    }

    /**
     * Function may be used later
     *
     * @param ApiResponse $apiResponse
     */
    private function handleError(ApiResponse $apiResponse) : void
    {
        //
    }

}