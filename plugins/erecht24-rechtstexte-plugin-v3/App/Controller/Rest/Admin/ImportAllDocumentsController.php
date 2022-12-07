<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\LegalDocument;
use eRecht24\LegalTexts\App\Api\LegalDocument as ApiClient;
use eRecht24\LegalTexts\App\Helper;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class ImportAllDocumentsController extends WP_REST_Controller
{
    const REST_ROUTE = '/updateAllDocuments';

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        register_rest_route(
            Helper::REST_NAMESPACE,
            self::REST_ROUTE,
            array(
                'methods'  => [WP_REST_Server::READABLE],
                'callback' => array( $this, 'execute' ),
                'args'     => array(),
                'permission_callback' => array( $this, 'validate' ),
                'show_in_index' => false,
            )
        );
    }

    /**
     * Function updates all legal documents
     * @throws \Exception
     */
    public function execute() : WP_REST_Response
    {
        $responses = [
            'success' => [],
            'failed' => []
        ];
        foreach (LegalDocument::ALLOWED_DOCUMENT_TYPES as $documentType) {
            $response = $this->updateDocumentByType($documentType);
            if ($response->isSuccess()) {
                $responses['success'][] = sprintf(
                    __('%s successfully imported.', Helper::PLUGIN_TEXT_DOMAIN),
                    Helper::getTranslatedDocumentType($documentType)
                );
            } else {
                $responses['failed'][] = $documentType . ': ' . Helper::getBestFittingApiErrorMessage(
                    $response->getData(),
                    sprintf(
                        __('%s could not be imported. Please try again later.', Helper::PLUGIN_TEXT_DOMAIN),
	                    Helper::getTranslatedDocumentType($documentType)
                    )
                );
            }
        }

        return new WP_REST_Response(
            $responses,
            200
        );
    }

    /**
     * Check if action is allowed
     * @return bool
     */
    public function validate() : bool
    {
        $user = wp_get_current_user();
        $allowed_roles = array( 'administrator' );
        return  ( array_intersect( $allowed_roles, $user->roles ) || current_user_can( 'manage_options' ) );
    }

    /**
     * Function updates legal document by type
     * @param string $type
     * @return ApiResponse
     * @throws \Exception
     */
    private function updateDocumentByType(
        string $type
    ) : ApiResponse
    {
        $apiClient = $this->getApiClient($type);

        if (!$apiClient) {
            return new ApiResponse(
                404,
                false,
                [
                	'message' => __('No API Client available. Please check document type and / or API key.', Helper::PLUGIN_TEXT_DOMAIN)
                ]
            );
        }

        return $apiClient->importDocument();
    }

    /**
     * Function provides Api key from database
     * @param string $type
     * @return ApiClient|null
     * @throws \Exception
     */
    private function getApiClient(
        string $type
    ): ?ApiClient
    {
        if ($key = Helper::getApiKey()) {
            try {
                return new ApiClient($key, $type);
            } catch (\Exception $e) {
                Helper::erecht24_log_error($e->getMessage());
            }
        }

        return null;
    }
}