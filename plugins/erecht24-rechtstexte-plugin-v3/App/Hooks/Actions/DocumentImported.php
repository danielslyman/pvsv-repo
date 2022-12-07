<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Actions;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\LegalDocument;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class DocumentImported
{
    /**
     * Function handles actions after ERecht24 Imprint was imported
     * @param ApiResponse|null $apiResponse
     * @param string $documentType
     */
    public function execute(
        ?ApiResponse $apiResponse,
        string $documentType = ''
    ) : void
    {
        if ( !($apiResponse instanceof ApiResponse) ) {
            Helper::erecht24_log_error('An Error occurred while importing document. No Response Data provided. Abort.');
            return;
        }

         ($apiResponse->isSuccess())
             ? $this->storeDocument($apiResponse, $documentType)
             : $this->handleError($apiResponse);
    }

    /**
     * Function stores api response data in database
     * @param ApiResponse $apiResponse
     * @param string $documentType
     */
    private function storeDocument(
        ApiResponse $apiResponse,
        string $documentType
    ) : void
    {
        if ( !LegalDocument::documentTypeIsValid($documentType) ) {
            Helper::erecht24_log_error('An Error occurred. Illegal document type was supplied. Abort');
            return;
        }

        // we redefine and sanitize client options here because hooks could have modified them
        // @todo we write unescaped data in database table
        $documentOptions = [
            'document_de_remote' => (string) $apiResponse->getData('html_de') ?? '',
            'document_en_remote' => (string) $apiResponse->getData('html_en') ?? '',
            'document_de_last_update_remote' => (string) sanitize_text_field($apiResponse->getData('modified') ?? ''),
            'document_en_last_update_remote' => (string) sanitize_text_field($apiResponse->getData('modified') ?? '')
        ];

        // check necessary data
        if (!$documentOptions['document_de_remote'] && !$documentOptions['document_en_remote']) {
            Helper::erecht24_log_error('An Error occurred. Server did not send any document data. Database not updated');
            return;
        }

       $this->updateWpOptionByDocumentType($documentType, $documentOptions);
    }

	/**
	 * Function updates associated wp option
	 * @param string $documentType
	 * @param array $optionData
	 */
    private function updateWpOptionByDocumentType(
        string $documentType,
        array $optionData
    ) : void
    {
        try {
            // update database
            $optionKey = Helper::getWpOptionByDocumentType($documentType);
            if (!$optionKey) {
                Helper::erecht24_log_error('No Option found. Please contact admin.');
            }

            $optionManger = new WpOptionManager($optionKey);
            $optionManger->setOption($optionData, false)->save();
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
        }
    }

    /**
     * Function may be used later
     * @param ApiResponse $apiResponse
     */
    private function handleError(ApiResponse $apiResponse) : void
    {
        //
    }

}