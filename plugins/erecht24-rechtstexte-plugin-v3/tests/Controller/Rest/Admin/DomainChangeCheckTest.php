<?php

use eRecht24\LegalTexts\App\Service\StatusCheck\DomainChangeCheck;
use eRecht24\LegalTexts\App\Service\StatusCheck\StatusCheckResponse;
use eRecht24\LegalTexts\App\Service\WpOptionManager;
use eRecht24\LegalTexts\tests\src\factories\PluginSettingsFactory;

class DomainChangeCheckTest extends WP_UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->factory->pluginSettings = new PluginSettingsFactory();
    }

    /**
     * Check without any changes (plugin fresh installed)
     * @return void
     */
    public function testWithEmptyApiKey()
    {
        $domainCheck = new DomainChangeCheck();
        $result = $domainCheck->check();

        $this->assertInstanceOf(StatusCheckResponse::class, $result);
        $this->assertEquals(true, $result->isSuccess(), 'Status check should show alright.');
    }

    /**
     * URL of WordPress is unchanged
     * @return void
     */
    public function testUnchangedUrl()
    {
        $this->factory->pluginSettings->create([
            WpOptionManager::WP_OPTION_API_KEY => [
                'api_key' => 'secret-api-key'
            ],
            // set secret to a know secret
            WpOptionManager::WP_OPTION_API_CLIENT => [
                'secret' => 'my-secret',
                'client_id' => 120
            ],
            WpOptionManager::WP_OPTION_WP_SETTINGS => [
                'push_uri' => md5(get_rest_url(get_current_blog_id(), '/erecht24/v1/push'))
            ]
        ]);

        $domainCheck = new DomainChangeCheck();
        $result = $domainCheck->check();

        $this->assertInstanceOf(StatusCheckResponse::class, $result);
        $this->assertEquals(true, $result->isSuccess(), 'Status check should show alright.');
    }

    /**
     * URL of WordPress changed
     * @return void
     */
    public function testChangedUrl()
    {
        $this->factory->pluginSettings->create([
            WpOptionManager::WP_OPTION_API_KEY => [
                'api_key' => 'secret-api-key'
            ],
            // set secret to a know secret
            WpOptionManager::WP_OPTION_API_CLIENT => [
                'secret' => 'my-secret',
                'client_id' => 120
            ],
            WpOptionManager::WP_OPTION_WP_SETTINGS => [
                'push_uri' => md5('https://example.com/index.php?rest_route=/erecht24/v1/push"')
            ]
        ]);

        $domainCheck = new DomainChangeCheck();
        $result = $domainCheck->check();

        $this->assertInstanceOf(StatusCheckResponse::class, $result);
        $this->assertEquals(false, $result->isSuccess(), 'Status check should show error.');
    }
}
