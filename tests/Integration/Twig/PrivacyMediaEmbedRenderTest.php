<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class PrivacyMediaEmbedRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testRendersPassthroughWhenMediaAllowed(): void
    {
        self::bootKernel();
        $this->capture(['required' => true, 'analytics' => false, 'media' => true]);

        $html = (string) self::renderTwigComponent('PrivacyMediaEmbed', [
            'provider' => 'youtube',
            'videoId' => 'dQw4w9WgXcQ',
            'title' => 'Demo video',
        ]);

        self::assertStringContainsString('privacy-media-embed--passthrough', $html);
        self::assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
        self::assertStringNotContainsString('privacy-media-embed--facade', $html);
    }

    public function testRendersFacadeWhenMediaDenied(): void
    {
        self::bootKernel();
        $this->capture(['required' => true, 'analytics' => false, 'media' => false]);

        $html = (string) self::renderTwigComponent('PrivacyMediaEmbed', [
            'provider' => 'youtube',
            'videoId' => 'dQw4w9WgXcQ',
            'title' => 'Demo video',
        ]);

        self::assertStringContainsString('privacy-media-embed--facade', $html);
        self::assertStringContainsString('data-controller="privacy-settings-bundle--media-embed"', $html);
        self::assertStringContainsString('Load', $html);
        self::assertStringContainsString('Cookie settings', $html);
        self::assertStringNotContainsString('<iframe', $html);
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
