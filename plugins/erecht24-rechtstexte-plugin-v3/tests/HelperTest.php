<?php


use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\WpOptionManager;

class HelperTest extends WP_UnitTestCase
{

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
            'lowercase + underscore privacyPolicy' => ['privacy_policy'],
            'uppercase + underscore privacyPolicy' => ['PRIVACY_POLICY'],
            'lowercase imprint' => ['imprint'],
            'uppercase imprint' => ['IMPRINT'],
            'camelcase privacyPolicySocialMedia' => ['privacyPolicySocialMedia'],
            'lowercase privacyPolicySocialMedia' => ['privacypolicysocialmedia'],
            'uppercase privacyPolicySocialMedia' => ['PRIVACYPOLICYSOCIALMEDIA'],
            'lowercase + underscore privacyPolicySocialMedia' => ['privacy_policy_social_media'],
            'uppercase + underscore privacyPolicySocialMedia' => ['PRIVACY_POLICY_SOCIAL_MEDIA'],
        ];
    }

    /**
     * Ensure that wrong written types do converted to the right wp_option
     * @dataProvider wrongDocumentTypeProvider
     */
    public function testGetWpOptionByDocumentType($type)
    {
        $actual = Helper::getWpOptionByDocumentType($type);

        switch (strtolower($type)) {
            case 'privacypolicy':
            case 'privacy_policy':
                $expected = WpOptionManager::WP_OPTION_PRIVACY_POLICY;
                break;
            case 'privacypolicysocialmedia':
            case 'privacy_policy_social_media':
                $expected = WpOptionManager::WP_OPTION_SOCIAL_MEDIA;
                break;
            case 'imprint':
                $expected = WpOptionManager::WP_OPTION_IMPRINT;
                break;
        }

        $this->assertEquals($expected, $actual);
    }
}
