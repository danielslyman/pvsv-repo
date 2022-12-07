<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Controller\Rest;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Api\LegalDocument;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class PushController extends WP_REST_Controller
{
    const REST_ROUTE = '/push';

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() : void
    {
        register_rest_route(
            Helper::REST_NAMESPACE,
            self::REST_ROUTE,
            array(
                'methods'  => [WP_REST_Server::READABLE, WP_REST_Server::CREATABLE],
                'callback' => array( $this, 'execute' ),
                'args'     => array(),
                'permission_callback' => '__return_true',
                'show_in_index' => false,
            )
        );
    }

    /**
     * @param $request
     * @return WP_REST_Response
     */
    public function execute($request) : WP_REST_Response
    {
        $params = $request->get_params();

        // validate Secret
        $secret = $params['erecht24_secret'] ?? '';
        $secretValidation = $this->validateSecret($secret);
        if ( $secretValidation instanceof WP_REST_Response) {
            return $secretValidation;
        }

        // validate type
        $type = $params['erecht24_type'] ?? '';
        if (!$type) {
            return $this->missingTypeResponse();
        }

        switch (strtolower($type)) {
            case 'ping':
                return $this->pingResponse();
            case 'imprint':
            case 'privacypolicy':
            case 'privacypolicysocialmedia':
                return $this->handleLegalDocument($type);
            default:
                return $this->invalidTypeResponse();
        }
    }

    /**
     * Check secret param
     * @param string $secret
     * @return WP_REST_Response|null
     */
    private function validateSecret(
        string $secret
    ) : ?WP_REST_Response
    {
        if (!$secret)
            return $this->missingSecretResponse();

        if ($secret != Helper::getSecret())
            return $this->missMatchingSecretResponse();

        return null;
    }

    /**
     * Function validates everything and finally updates legal document
     * @param string $type
     * @return WpOptionManager|WP_REST_Response
     */
    private function handleLegalDocument(
        string $type
    ) {
        // get corresponding wp_option
        $wpOption = $this->getAssociatedWpOption($type);
        if ($wpOption instanceof WP_REST_Response)
            return $wpOption;

        // check if update is needed
        if ($wpOption->getSubOption('document_source'))
            return $this->localDocumentSourceResponse();

        // initiate legal document api
        $apiKey = Helper::getApiKey();
        try {
            $legalDocument = new LegalDocument($apiKey,$type);
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
            return $this->wpErrorResponse();
        }

        // import document
        $response = $legalDocument->importDocument();

        // success
        if ($response->isSuccess())
            return $this->successResponse();

        // fail
        return $this->errorResponse($response);
    }

    /**
     * Function provides corresponding WpOption
     * @param string $type
     * @return WpOptionManager|WP_REST_Response
     */
    private function getAssociatedWpOption(
        string $type
    ) {
        $wpOptionKey = Helper::getWpOptionByDocumentType($type);
        if (is_null($wpOptionKey)) {
            return $this->WpOptionMissingResponse();
        }

        try {
            $wpOption = new WpOptionManager($wpOptionKey);
        } catch (\Exception $e) {
            Helper::erecht24_log_error($e->getMessage());
            return $this->wpErrorResponse();
        }

        return $wpOption;
    }

    /**
     * 200
     * @return WP_REST_Response
     */
    private function successResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 200,
                'message' => 'Document successfully imported',
                'message_de' => 'Dokument erfolgreich importiert'
            ],
            200
        );
    }
    /**
     * 200
     * @return WP_REST_Response
     */
    private function pingResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 200,
                'message' => 'pong'
            ],
            200
        );
    }

    /**
     * 400
     * @return WP_REST_Response
     */
    private function invalidTypeResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 400,
                'message' => 'Unknown type',
                'message_de' => 'Unbekannter Typ'
            ],
            400);
    }
    /**
     * 400
     * @return WP_REST_Response
     */
    private function missingTypeResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 400,
                'message' => 'No type was defined.',
                'message_de' => 'Es wurde kein Typ definiert.'
            ],
            400
        );
    }
    /**
     * 400
     * @return WP_REST_Response
     */
    private function WpOptionMissingResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 400,
                'message' => 'No wp_option for given type available.',
                'message_de' => 'Keine WP_Option für gegebenen Typ verfügbar.'
            ],
            400
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
                'code'    => 400,
                'message' =>  $response->getData('message') ?? 'Error while importing the document',
                'message_de' => $response->getData('message_de') ?? 'Fehler beim Import des Dokuments'
            ],
            400
        );
    }

    /**
     * 401
     * @return WP_REST_Response
     */
    private function missingSecretResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code' => 401,
                'message' => 'No Api Secret sent. Please try again later.',
                'message_de' => 'Kein Api-Geheimnis gesendet. Bitte versuchen Sie es später noch einmal.'
            ],
            401
        );
    }
    /**
     * 401
     * @return WP_REST_Response
     */
    private function missMatchingSecretResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code' => 401,
                'message' => 'Unauthorized request. The given secret is not valid or has expired.',
                'message_de' => 'Unautorisierte Anfrage. Das angegebene Geheimnis ist nicht gültig oder abgelaufen.'
            ],
            401
        );
    }
    /**
     * 401
     * @return WP_REST_Response
     */
    private function wpErrorResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code' => 401,
                'message' => 'An unexpected Error happened. Please Check your wordpress site.',
                'message_de' => 'Ein unerwarteter Fehler ist aufgetreten. Bitte überprüfen Sie Ihre Wordpress-Seite.'
            ],
            401
        );
    }

    /**
     * 422
     * @return WP_REST_Response
     */
    private function localDocumentSourceResponse() : WP_REST_Response
    {
        return new WP_REST_Response(
            [
                'code'    => 422,
                'message' => 'The data source for the document is "local version". Therefore the document was not updated.',
                'message_de' => 'Die Datenquelle für das Dokument ist "lokale Version". Deshalb wurde das Dokument nicht geupdated.'
            ],
            422
        );
    }
}