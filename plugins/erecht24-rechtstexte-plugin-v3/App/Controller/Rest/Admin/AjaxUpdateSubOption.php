<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest\Admin;

use eRecht24\LegalTexts\App\Service\WpOptionManager;
use eRecht24\LegalTexts\App\Helper;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class AjaxUpdateSubOption extends WP_REST_Controller
{
    const REST_ROUTE = '/ajaxUpdateSubOption';

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
     * Function updates sub option on the fly
     * @param $request
     * @return WP_REST_Response
     * @throws \Exception
     */
    public function execute($request) : WP_REST_Response
    {
        $params = $request->get_params();
	    $option = (string) $params['option'] ?? null;
        $wpOption = $this->getCorrespondingWpOption($option);
        if ($wpOption instanceof WP_REST_Response)
            return $wpOption;

        $subOption = (string) $params['subOption'] ?? null;
        $subOptionValue = $params['value'] ?? null;
        if ( is_null($subOption) || is_null($subOptionValue) )
            // 411
            return $this->requiredParamsMissingResponse();

        // 200
        $wpOption->setOption([$subOption => $subOptionValue])->save();
        return new WP_REST_Response(
            [
                'message' => __('Automatic saving was successful.', Helper::PLUGIN_TEXT_DOMAIN),
                'subOption' => $subOption,
                'value' => $wpOption->getSubOption($subOption)
            ],
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
     * Function provides valid wp_option depending on request parameter
     * @param string|null $option
     * @return WpOptionManager|WP_REST_Response
     * @throws \Exception
     */
    private function getCorrespondingWpOption(
        ?string $option
    )
    {
	    if (!$option)
		    return $this->noOptionResponse();
	    try {
		    $wpOption = new WpOptionManager($option);
	    } catch (\Exception $e) {
		    Helper::erecht24_log_error($e->getMessage());
		    return $this->illegalOptionResponse();
	    }

        // No need to catch exception here. We validated $wpOptionString above
        return $wpOption;
    }

    /**
     * 411
     * @return WP_REST_Response
     */
    private function requiredParamsMissingResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __(
                    'Your changes could not be saved automatically. Please click the button "Save changes". Reason: Required parameters are missing.',
                    Helper::PLUGIN_TEXT_DOMAIN
                )
            ],
            411
        );
    }

    /**
     * 422
     * @return WP_REST_Response
     */
    private function noOptionResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('Your changes could not be saved automatically. Please click the button "Save changes". Reason: No WP_Option specified. Please try again later.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            422
        );
    }

    /**
     * 423
     * @return WP_REST_Response
     */
    private function illegalOptionResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'message' => __('Your changes could not be saved automatically. Please click the button "Save changes". Reason: Unsupported wp_option specified. Abort.', Helper::PLUGIN_TEXT_DOMAIN)
            ],
            423
        );
    }
}