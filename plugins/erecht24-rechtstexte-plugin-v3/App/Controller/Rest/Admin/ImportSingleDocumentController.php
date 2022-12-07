<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\LegalDocument as ApiClient;
use eRecht24\LegalTexts\App\Helper;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class ImportSingleDocumentController extends WP_REST_Controller
{
    const REST_ROUTE = '/updateSingleDocument';

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
     * Function updates single legal document
     * @param $request
     * @return WP_REST_Response
     * @throws \Exception
     */
    public function execute($request) : WP_REST_Response
    {
        $params = $request->get_params();
        $type = Helper::getCamelCase($params['data_type']) ?? null;
        if (!$type) {
            // 422
            return $this->noTypeResponse();
        }

        $response = $this->updateDocumentByType($type);
        if (!$response->isSuccess()) {
	        // 400
	        return $this->errorResponse($response);
        }

	    // 200
	    return $this->successResponse($type);
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
     * @return ApiResponse|void
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

	/**
	 * 200
	 * @param string $type
	 * @return WP_REST_Response
	 */
    private function successResponse(
    	string $type
    ) :WP_REST_Response
    {
	    return new WP_REST_Response(
            [
	            'message' => sprintf(
		            __('%s successfully imported.', Helper::PLUGIN_TEXT_DOMAIN),
		            Helper::getTranslatedDocumentType($type)
	            )
            ],
            200
        );
    }

    /**
     * 422
     * @return WP_REST_Response
     */
    private function noTypeResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('No Document type specified. Please try again later.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            422
        );
    }

    /**
     * 400
     * @param ApiResponse $response
     * @return WP_REST_Response
     */
    private function errorResponse(
        ApiResponse $response
    ) : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => Helper::getBestFittingApiErrorMessage(
                    $response->getData(),
                    __('There was a connection error. Please try again later.', Helper::PLUGIN_TEXT_DOMAIN))
            ],
            400
        );
    }
}