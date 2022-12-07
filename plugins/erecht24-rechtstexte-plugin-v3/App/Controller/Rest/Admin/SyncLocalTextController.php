<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Service\WpOptionManager;
use eRecht24\LegalTexts\App\Helper;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class SyncLocalTextController extends WP_REST_Controller
{
    const REST_ROUTE = '/syncLocalText';

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
     * Function updates local texts with remote ones
     * @todo we write unescaped data in database
     */
    public function execute($request)
    {
        $params = $request->get_params();
        $type = Helper::getCamelCase($params['data_type']) ?? null;
        $wpOption = $this->getCorrespondingWpOption($type);
        if ($wpOption instanceof WP_REST_Response) {
            return $wpOption;
        }

        // check if legal documents are imported
        if (!$wpOption->getSubOption('document_de_last_update_remote')
            || !$wpOption->getSubOption('document_en_last_update_remote')) {
            // 411
            return $this->noImportResponse();
        }

        // collect data
        $timestamp = date("Y-m-d H:i:s", time());
        if ($de = $wpOption->getSubOption('document_de_remote')) {
            $wpOption
                ->setOption(['document_de_local' => $de], false)
                ->setOption(['document_de_last_update_local' => $timestamp]);
        }
        if ($en = $wpOption->getSubOption('document_en_remote')) {
            $wpOption
                ->setOption(['document_en_local' => $en], false)
                ->setOption(['document_en_last_update_local' => $timestamp]);
        }
        // update data
        $wpOption->save();

        return $this->successResponse($wpOption);
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
     * Function provides wp_option depending on document type
     * @param string|null $documentType
     * @return WpOptionManager|WP_REST_Response
     * @throws \Exception
     */
    private function getCorrespondingWpOption(
        ?string $documentType
    )
    {
        if (!$documentType) {
            // 422
            return $this->noTypeResponse();
        }

        $wpOptionString = Helper::getWpOptionByDocumentType($documentType);
        if (!$wpOptionString){
            // 423
            return $this->illegalTypeResponse();
        }

        // No need to catch exception here. We validated $wpOptionString above
        return new WpOptionManager($wpOptionString);
    }

    /**
     * 200
     * @param WpOptionManager $wpOption
     * @return WP_REST_Response
     */
    private function successResponse(
        WpOptionManager $wpOption
    ) : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('Legal texts successfully adopted.', Helper::PLUGIN_TEXT_DOMAIN),
                'html_de'=> $wpOption->getSubOption('document_de_local'),
                'html_en'=> $wpOption->getSubOption('document_en_local'),
                'timestamp_de' => $wpOption->getSubOption('document_de_last_update_local'),
                'timestamp_en' => $wpOption->getSubOption('document_en_last_update_local')
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
     * 423
     * @return WP_REST_Response
     */
    private function illegalTypeResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('Illegal document type specified. Abort.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            423
        );
    }

    /**
     * 424
     * @return WP_REST_Response
     */
    private function noImportResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('No data has been imported yet. Please import data first.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            424
        );
    }
}