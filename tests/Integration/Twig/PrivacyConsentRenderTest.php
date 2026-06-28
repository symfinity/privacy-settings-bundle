<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PrivacyConsentRenderTest extends KernelTestCase
{
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
        $container = self::getContainer();
        $normalizer = $container->get(CategoryModelNormalizer::class);
        $categories = $normalizer->normalize($container->getParameter('symfinity.privacy_settings.categories'));
        $store = $container->get(PreferenceStoreInterface::class);
        self::assertInstanceOf(InMemoryPreferenceStore::class, $store);
        (new PreferenceCaptureService($store, new ConsentDecisionEventPublisher()))
            ->capture('visitor', $categories, $choices);
    }
}
