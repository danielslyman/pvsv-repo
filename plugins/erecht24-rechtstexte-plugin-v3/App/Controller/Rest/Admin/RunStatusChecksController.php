<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Server;

class RunStatusChecksController extends WP_REST_Controller
{
    const REST_ROUTE = '/runStatusChecks';

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
     * @param WP_REST_Request $request
     * @return void
     */
    public function execute(WP_REST_Request $request) : void
    {
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
}