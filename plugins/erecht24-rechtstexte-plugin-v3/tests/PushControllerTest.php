<?php


use eRecht24\LegalTexts\App\Service\WpOptionManager;
use eRecht24\LegalTexts\tests\src\factories\PluginSettingsFactory;

class PushControllerTest extends WP_UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        WP_UnitTestCase_Base::factory()->pluginSettings = new PluginSettingsFactory();

        /** @var WP_REST_Server $wp_rest_server */
        global $wp_rest_server;
        $this->server = $wp_rest_server = new \WP_REST_Server;
        do_action('rest_api_init');
    }

    /**
     * Provide right and wrong documentTypes
     * @return array
     */
    public function wrongDocumentTypeProvider(): array
    {
        return [
            'camelcase privacyPolicy' => ['privacyPolicy'],
            'lowercase privacyPolicy' => ['privacypolicy'],
            'uppercase privacyPolicy' => ['PRIVACYPOLICY'],
            'lowercase imprint' => ['imprint'],
            'uppercase imprint' => ['IMPRINT'],
            'camelcase privacyPolicySocialMedia' => ['privacyPolicySocialMedia'],
            'lowercase privacyPolicySocialMedia' => ['privacypolicysocialmedia'],
            'uppercase privacyPolicySocialMedia' => ['PRIVACYPOLICYSOCIALMEDIA'],
        ];
    }

    /**
     * Ensure that the push endpoint can be called using for example a lowercase document type.
     * Some hosters / wafs change the upper- / lowercase in the url.
     * @dataProvider wrongDocumentTypeProvider
     */
    public function testDocumentSourceCanBePassedInMultipleWays(string $type)
    {
        $this->factory->pluginSettings->create([
            // set secret to a know secret
            WpOptionManager::WP_OPTION_API_CLIENT => [
                'secret' => 'my-secret',
            ],
            // set data source to local, request must fail later with http 422
            WpOptionManager::WP_OPTION_IMPRINT => [
                'document_source' => true
            ],
            WpOptionManager::WP_OPTION_PRIVACY_POLICY => [
                'document_source' => true
            ],
            WpOptionManager::WP_OPTION_SOCIAL_MEDIA => [
                'document_source' => true
            ]
        ]);

        $request = new WP_REST_Request('GET', '/erecht24/v1/push');
        $request->set_param('erecht24_type', $type);
        $request->set_param('erecht24_secret', 'my-secret');

        $response = $this->server->dispatch($request);

        // local data source, so we expect a 422 http status code
        $this->assertEquals(422, $response->get_status());
        $this->assertCount(3, $response->get_data());
    }
}
