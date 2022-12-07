<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client as ApiClient;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class CheckApiKeyController extends WP_REST_Controller
{
    const REST_ROUTE = '/checkApiKey';

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
     * Function validates and handles API key
     * @param $request
     * @return WP_REST_Response
     */
    public function execute($request) : WP_REST_Response
    {
        // if no api_key exist in $_POST, we assume that the user wants to delete an old client
        $params = $request->get_params();
        $newApiKey = $params['apiKey'] ?? '';
        if ( !$newApiKey ) {
            $response = $this->deleteOldClient();
            if ( $response->isSuccess() ) {
                // 202
                return $this->clientDeletionSuccessResponse($newApiKey);
            }
            // 203
            return $this->forceDeletionResponse();
        }

        // no action required
        // @todo maybe reassign new client
        if ($newApiKey === Helper::getApiKey()) {
            // 201
            return $this->noActionRequiredResponse();
        }

        return $this->updateClient($newApiKey);
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
     * Function updates Api client
     * @param string $newApiKey
     * @return WP_REST_Response
     */
    private function updateClient(
        string $newApiKey
    ) : WP_REST_Response
    {
        // check new api connection
        $newApiClient = new ApiClient($newApiKey);
        $checkResponse = $newApiClient->listClients();
        if ( !$checkResponse->isSuccess() ) {
            // 400
            return $this->connectionErrorResponse($checkResponse);
        }

        // delete old client remotely
        $this->deleteOldClient();

        // register new client remotely
        return $this->addNewClient($newApiClient);
    }

    /**
     * Function deletes old api client
     * @return ApiResponse
     */
    private function deleteOldClient() : ApiResponse
    {
        $clientId = Helper::getClientId();
        if (!$clientId) {
            return new ApiResponse(
                200,
                true,
                [
                	'message' => __('No Client in Database stored. Deletion not needed', Helper::PLUGIN_TEXT_DOMAIN)
                ]
            );
        }

        $oldApiClient = new ApiClient(Helper::getApiKey());
        return $oldApiClient->deleteClient($clientId);
    }

    /**
     * Function adds new api client
     * @param ApiClient $apiClient
     * @return WP_REST_Response
     */
    private function addNewClient(
        ApiClient $apiClient
    ) : WP_REST_Response
    {
        $response = $apiClient->addClient();
        if ($response->isSuccess()) {
            // 200
            return $this->successResponse($apiClient->getApiKey());
        }

        // 400
        return $this->errorResponse($response);
    }

    /**
     * Function updates api key in database
     * @param $apiKey
     */
    private function updateWpOption(
        ?string $apiKey
    ) : void
    {
        try {
            $wpOption = new WpOptionManager(WpOptionManager::WP_OPTION_API_KEY);
            $wpOption->setOption(['api_key' => $apiKey])->save();
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
        }
    }

    /**
     * 200
     * @param string $apiKey
     * @return WP_REST_Response
     */
    private function successResponse(
        string $apiKey
    ) : WP_REST_Response
    {
        $this->updateWpOption($apiKey);
        return new WP_REST_Response(
            [
                'message' => __( 'Valid API Key. Client was successfully created.', Helper::PLUGIN_TEXT_DOMAIN),
                'apiKey' => $apiKey
            ],
            200
        );
    }

    /**
     * 201
     * @return WP_REST_Response
     */
    private function noActionRequiredResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __( 'No Action required', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            201
        );
    }

    /**
     * 202
     * @param string $newApiKey
     * @return WP_REST_Response
     */
    private function clientDeletionSuccessResponse(
        string $newApiKey
    ): WP_REST_Response
    {
        $this->updateWpOption($newApiKey);
        return new WP_REST_Response(
            [
                'message' => __('Old client successfully deleted.', Helper::PLUGIN_TEXT_DOMAIN),
                'apiKey' => ''
            ],
            202
        );
    }

    /**
     * 203
     * @return WP_REST_Response
     */
    private function forceDeletionResponse() : WP_REST_Response
    {
        // Even api deletion failed, we reset associated wp_option
        $this->updateWpOption('');
        try {
            $wpOption = new WpOptionManager(WpOptionManager::WP_OPTION_API_CLIENT);
            $wpOption->reset();
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
        }

        return new WP_REST_Response(
            [
                'message' => __('Api Client deletion was forced', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            203
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
                    __('There was a connection error. Please check the Api key.', Helper::PLUGIN_TEXT_DOMAIN))
            ],
            400
        );
    }

    /**
     * 400
     * @param ApiResponse $response
     * @return WP_REST_Response
     */
    private function connectionErrorResponse(
        ApiResponse $response
    ) : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => Helper::getBestFittingApiErrorMessage(
                    $response->getData(),
                    __('There was an error with new Api connection. Please check the Api key.', Helper::PLUGIN_TEXT_DOMAIN))
            ],
            400
        );
    }
}