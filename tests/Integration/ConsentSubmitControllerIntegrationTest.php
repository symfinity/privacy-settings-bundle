<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ConsentSubmitControllerIntegrationTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testConsentSubmitRouteIsCallable(): void
    {
        $client = self::createClient();
        \assert($client instanceof KernelBrowser);

        $client->request(
            'POST',
            '/_privacy/consent/visitor-http',
            ['consent_decision' => 'reject_all'],
        );

        self::assertResponseRedirects('/');
    }
}
