<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Filter;

use eRecht24\LegalTexts\App\Api\ApiResponse;

class ClientCreated
{
    /**
     * A list of all possible data keys from [post] /v1/clients endpoint
     * @var array
     */
    private $allowedApiDataKeys = [
        'secret',
        'client_id',
        'message',
        'message_de',
        'token'
    ];

    /**
     * Function filters and sanitizes received api data
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

        // filter & sanitize received data
        $data = $apiResponse->getData() ?? [];
        foreach ($data as $key => $value) {
            if ( in_array($key, $this->allowedApiDataKeys) ) {
                $sanitizedResponse->addData([$key => sanitize_text_field($value)]);
            }
        }

        return $sanitizedResponse;
    }
}