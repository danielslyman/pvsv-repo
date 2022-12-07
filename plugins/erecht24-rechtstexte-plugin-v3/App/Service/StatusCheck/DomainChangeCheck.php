<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

use eRecht24\LegalTexts\App\Api\Client;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class DomainChangeCheck implements StatusCheckInterface
{

    /**
     * Check if url of the WordPress got changed since the client was registered
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse
    {
        $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_WP_SETTINGS);
        $client = json_decode(Client::createRequestBody(), true);
        $actualPushUriMd5 = md5($client['push_uri']);

        // save hashed push uri (migration)
        if (Helper::getApiKey() !== "" && $optionManger->getSubOption('push_uri') == "") {
            $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_WP_SETTINGS);
            $optionManger->setOption(['push_uri' => $actualPushUriMd5])->save();
        }

        if ( Helper::getApiKey() !== "" && $actualPushUriMd5 !== $optionManger->getSubOption('push_uri') )
            return $this->domainChangedErrorResponse();

        return new StatusCheckResponse(
            true,
            __('WordPress Address (URL) is unchanged.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }

    /**
     * @return StatusCheckResponse
     */
    private function domainChangedErrorResponse() : StatusCheckResponse
    {
        return new StatusCheckResponse(
            false,
            __('The WordPress Address (URL) changed since you activated the eRecht24 API client. You have to update the client.', Helper::PLUGIN_TEXT_DOMAIN)
        );
    }
}