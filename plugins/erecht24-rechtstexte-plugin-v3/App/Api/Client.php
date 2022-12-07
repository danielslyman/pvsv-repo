<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Api;

use eRecht24\LegalTexts\App\Hooks\Actions\ClientCreated as ClientCreatedAction;
use eRecht24\LegalTexts\App\Hooks\Actions\ClientDeleted as DatabaseStorer;
use eRecht24\LegalTexts\App\Hooks\Filter\ClientCreated as ClientCreatedFilter;
use eRecht24\LegalTexts\App\Hooks\Filter\ClientDeleted as RequestSanitizer;
use WP_REST_Server;

class Client extends BaseApi
{
    const CLIENT_CREATED_ACTION = 'erecht24_action_client_created';
    const CLIENT_CREATED_FILTER = 'erecht24_filter_client_created';

    const CLIENT_DELETED_ACTION = 'erecht24_action_client_deleted';
    const CLIENT_DELETED_FILTER = 'erecht24_filter_client_deleted';

    /**
     * Function provides a list of all clients
     * @return ApiResponse
     */
    public function listClients() : ApiResponse
    {
        return $this->handleResponse(
            wp_remote_request(
                $this->getApiUrl('v1/clients'),
                [
                    'method' => 'GET',
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'eRecht24' => (string) $this->getApiKey()
                    ]
                ]
            )
        );
    }

    /**
     * Function adds a client remotely
     * @return ApiResponse
     */
    public function addClient() : ApiResponse
    {
        // Api Request
        $response = $this->handleResponse(
            wp_remote_request(
                $this->getApiUrl('v1/clients'),
                [
                    'method' => 'POST',
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'eRecht24' => (string) $this->getApiKey()
                    ],
                    'body' => self::createRequestBody()
                ]
            )
        );

        // register default hooks
        $this->registerAddClientHooks();

        // Apply filter hooks
        $response = apply_filters(
            self::CLIENT_CREATED_FILTER,
            $response
        );

        // Apply action hooks
        do_action(
        self::CLIENT_CREATED_ACTION,
            $response
        );

        return $response;
    }

    /**
     * Function deletes client remotely
     * @param int $clientId
     * @return ApiResponse
     */
    public function deleteClient(
        int $clientId
    ) : ApiResponse
    {
        $response = $this->handleResponse(
            wp_remote_request(
                $this->getApiUrl('v1/clients/' . $clientId),
                [
                    'method' => 'DELETE',
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'eRecht24' => (string) $this->getApiKey()
                    ]
                ]
            )
        );

        // register default hooks
        $this->registerDeleteClientHooks();

        // Apply filter hooks
        $response = apply_filters(
            self::CLIENT_DELETED_FILTER,
            $response
        );

        // Apply action hooks
        do_action(
            self::CLIENT_DELETED_ACTION,
            $response
        );

        return $response;
    }

    /**
     * Function executes test push
     * @todo may sanitize data
     * @param int $clientId
     * @return ApiResponse
     */
    public function testPushPing(
        int $clientId
    ) : ApiResponse
    {
        return $this->handleResponse(
            wp_remote_request(
                $this->getApiUrl('v1/clients/' . $clientId . '/testPush'),
                [
                    'method' => 'POST',
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'eRecht24' => (string) $this->getApiKey(),
                    ]
                ]
            )
        );
    }

    /**
     * Function provides request body for add client method
     *
     * @return string
     */
    public static function createRequestBody() : string
    {
        $request_body  = [];

        global $wp_version;
        $request_body['push_method'] = WP_REST_Server::READABLE;
        $request_body['push_uri']    = get_rest_url(get_current_blog_id(), '/erecht24/v1/push');
        $request_body['cms']         = 'WordPress';
        $request_body['cms_version'] = $wp_version;
        $request_body['plugin_name'] = 'eRecht24.de Rechtstexte für WordPress';
        $request_body['author_mail'] = 'support@e-recht24.de';

        return json_encode($request_body, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Register default hooks after client was created
     */
    private function registerAddClientHooks() : void
    {
        // Register default filter Hook
        add_filter(
            self::CLIENT_CREATED_FILTER,
            [new ClientCreatedFilter(), 'execute'],
            1
        );

        // Register default action Hook
        add_action(
            self::CLIENT_CREATED_ACTION,
            [new ClientCreatedAction(), 'execute']
        );
    }

    /**
     * Register default hooks after Client was deleted
     */
    private function registerDeleteClientHooks() : void
    {
        // Register default filter Hook
        add_filter(
            self::CLIENT_DELETED_FILTER,
            [new RequestSanitizer(), 'execute'],
            1
        );

        // Register default action Hook
        add_action(
            self::CLIENT_DELETED_ACTION,
            [new DatabaseStorer(), 'execute']
        );
    }
}
