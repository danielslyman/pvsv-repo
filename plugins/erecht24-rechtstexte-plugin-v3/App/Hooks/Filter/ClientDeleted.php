<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Filter;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Helper;

class ClientDeleted
{
    /**
     * A list of all possible data keys from [post] /v1/clients endpoint
     * @var array
     */
    private $allowedApiDataKeys = [
        'message',
        'message_de'
    ];

    /**
     * @param ApiResponse|null $apiResponse
     * @return ApiResponse|null
     */
    public function execute(
        ?ApiResponse $apiResponse
    ) : ?ApiResponse
    {
        // force null if no valid ApiResponse
        if ( !($apiResponse instanceof ApiResponse) )
            return null;

        $sanitizedResponse = new ApiResponse(
            $apiResponse->getCode(),
            $apiResponse->isSuccess()
        );

        // Api does not send a feedback message. So we add one
        if ($sanitizedResponse->isSuccess()) {
            $sanitizedResponse->addData(['message' => __('Client successfully deleted.', Helper::PLUGIN_TEXT_DOMAIN)]);
        } else {
            // filter & sanitize received data
            $data = $apiResponse->getData();
            foreach ($data as $key => $value) {
                if ( in_array($key, $this->allowedApiDataKeys) ) {
                    $sanitizedResponse->addData([$key => sanitize_text_field($value)]);
                }
            }
        }

        return $sanitizedResponse;
    }
}