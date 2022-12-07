<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Controller\Rest\PushController;
use eRecht24\LegalTexts\App\Helper;
use WP_Debug_Data;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class GetDebugInformationController extends WP_REST_Controller
{
    const REST_ROUTE = '/getDebugInformation';

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
     * Function validates and handles API key
     * @param $request
     * @return WP_REST_Response
     */
    public function execute($request) : WP_REST_Response
    {
	    global $wp_version;

        $params = $request->get_params();
        $data['api_key'] = substr(Helper::getApiKey(), 0, -8);
        $data['wp_version'] = $wp_version;
        $data['plugin_version'] = Helper::PLUGIN_VERSION;
        $data['api_host'] = Helper::API_HOST_URL;
        $data['api_secret'] = substr(Helper::getSecret(), 0, -8);
        $data['api_push_uri'] = get_rest_url(get_current_blog_id(), '/' . Helper::REST_NAMESPACE . PushController::REST_ROUTE);
        $data['client_id'] = Helper::getClientId();
        $data['error_log'] =  array_slice( (array) json_decode(get_option('erecht24_error_log', '{}') ?? [], true), 0, 75, true);

        // try to attach wordpress healthinformation to debug output
        $filename = ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';

        if (file_exists($filename)) {
            require_once $filename;
            require_once ABSPATH . 'wp-admin/includes/update.php';
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            WP_Debug_Data::check_for_updates();
            $data['wp_debug'] = WP_Debug_Data::debug_data();
        }

        return new WP_REST_Response(
            $data,
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
}