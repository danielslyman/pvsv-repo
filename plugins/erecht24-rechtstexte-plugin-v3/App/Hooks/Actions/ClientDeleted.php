<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Actions;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class ClientDeleted
{
    /**
     * Function handles actions after ERecht24 client was deleted remotely
     * @param ApiResponse|null $apiResponse
     */
    public function execute(
        ?ApiResponse $apiResponse
    ) : void
    {
        if ( !($apiResponse instanceof ApiResponse) )
            return;

        ($apiResponse->isSuccess())
            ? $this->resetClientData()
            : $this->handleError();
    }

    /**
     * Function stores api response data in database
     */
    private function resetClientData() : void
    {
        try {
            // update database
            $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_API_CLIENT);
            $optionManger->reset();

            // reset old push_uri
            $optionManger = new WpOptionManager(WpOptionManager::WP_OPTION_WP_SETTINGS);
            $optionManger->reset();
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
        }
    }

    /**
     * Function may be used later
     */
    private function handleError() : void
    {
        //
    }

}