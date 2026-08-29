<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PrivacyDemoControllerIntegrationTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testPrivacyDemoRouteRendersConsentBanner(): void
    {
        $client = self::createClient();

        $client->request('GET', '/_privacy/demo');

        self::assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('id="ux-blocks-core-css"', $content);
        self::assertStringContainsString('data-privacy-consent', $content);
        self::assertStringContainsString('PrivacyMediaEmbed', $content);
        self::assertStringContainsString('privacy-media-embed', $content);
    }
}
