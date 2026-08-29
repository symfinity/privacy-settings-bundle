<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CookiePreferenceStoreIntegrationTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return CookiePreferenceStoreTestKernel::class;
    }

    public function testAcceptAllSetsSignedConsentCookieAndHidesBanner(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('data-privacy-consent', (string) $client->getResponse()->getContent());

        $client->submitForm('Accept all', [
            'consent_decision' => 'accept_all',
        ]);

        self::assertResponseRedirects('/');

        $cookie = $client->getResponse()->headers->getCookies()[0] ?? null;
        self::assertNotNull($cookie);
        self::assertSame('symfinity_privacy_consent', $cookie->getName());
        self::assertGreaterThanOrEqual(180, (int) round(($cookie->getExpiresTime() - time()) / 86400));

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('data-privacy-consent', (string) $client->getResponse()->getContent());
        self::assertMatchesRegularExpression('/class="privacy-consent"[^>]*hidden/', (string) $client->getResponse()->getContent());
    }
}
