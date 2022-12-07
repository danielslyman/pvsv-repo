<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\Client as ApiClient;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class RefreshApiConnectionController extends WP_REST_Controller
{
    const REST_ROUTE = '/refreshApiConnection';

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() : void
    {
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
     * Function refreshes API Connection
     * @param $request
     * @return WP_REST_Response
     */
    public function execute($request) : WP_REST_Response
    {
        // delete old client remotely
        $this->deleteOldClient();

        // register new client remotely
        $response = $this->addNewClient();

        if (!$response->isSuccess()) {
            // 4XX
            return $this->errorResponse($response);
        }

        // 200
        if ( ! headers_sent() ) {
            header( "Content-Type: text/html; charset=" . get_option( 'blog_charset' ) );
            status_header( 200 );
            nocache_headers();
        }

        $view = new StatusTab();

        echo $view->runStatusChecks()->renderAjaxStatusTable();
        die();

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
     * Function deletes old api client
     */
    private function deleteOldClient()
    {
        $apiClient = new ApiClient(Helper::getApiKey());
        $apiClient->deleteClient(Helper::getClientId());
    }

    /**
     * Function adds new api client
     * @return ApiResponse
     */
    private function addNewClient() : ApiResponse
    {
        $apiClient = new ApiClient(Helper::getApiKey());
        return $apiClient->addClient();
    }

    /**
     * 4XX
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
                    __('There was a connection error. Please check the Api key.', Helper::PLUGIN_TEXT_DOMAIN)),
                'additionalMessage' => __('The problem could not be solved automatically. Please check your API key and the settings in your personal eRecht24 customer portal.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            $response->getCode()
        );
    }

}