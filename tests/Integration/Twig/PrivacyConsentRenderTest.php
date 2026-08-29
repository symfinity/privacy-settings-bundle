<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class PrivacyConsentRenderTest extends KernelTestCase
{
    use StoresConsentChoicesTrait;
    public function testRendersSnippetWhenAnalyticsAllowed(): void
    {
        self::bootKernel();
        $this->storeAnalyticsAllowed();

        $html = $this->renderFixture();

        self::assertStringContainsString('analytics-enabled', $html);
    }

    public function testOmitsSnippetWhenAnalyticsDenied(): void
    {
        self::bootKernel();
        $this->storeAnalyticsDenied();

        $html = $this->renderFixture();

        self::assertStringNotContainsString('analytics-enabled', $html);
    }

    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    private function renderFixture(): string
    {
        $twig = self::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);

        return $twig->render('@IntegrationTest/privacy_consent_fixture.html.twig');
    }

    private function storeAnalyticsAllowed(): void
    {
        $this->capture(['required' => true, 'analytics' => true, 'media' => false]);
    }

    private function storeAnalyticsDenied(): void
    {
        $this->capture(['required' => true, 'analytics' => false, 'media' => false]);
    }

    /**
     * @param array<string, bool> $choices
     */
    private function capture(array $choices): void
    {
        $this->captureConsentChoices(self::getContainer(), 'visitor', $choices);
    }
}
