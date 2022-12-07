<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Filter;

use eRecht24\LegalTexts\App\Api\ApiResponse;

class DocumentImported
{
    /**
     * A list of all possible data keys from [get] /v1/imprint endpoint
     * @var array
     */
    private $allowedApiDataKeys = [
        'created',
        'modified',
        'message',
        'message_de',
        'token'
    ];

    /**
     * A list of all possible data keys from [get] /v1/imprint endpoint
     * @var array
     */
    private $unfilteredDataKeys = [
        'html_de',
        'html_en',
    ];

    /**
     * Function filters and sanitizes api response data
     * @param ApiResponse|null $apiResponse
     * @return ApiResponse|null
     * @todo we write unescaped data in database table
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
            // sanitize
            if ( in_array($key, $this->allowedApiDataKeys) ) {
                $sanitizedResponse->addData([$key => sanitize_text_field($value)]);
            }

            // no sanitizing
            if ( in_array($key, $this->unfilteredDataKeys) ) {
                $sanitizedResponse->addData([$key => $value]);
            }
        }

        return $sanitizedResponse;
    }
}