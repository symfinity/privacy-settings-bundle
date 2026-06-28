<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class PrivacyMediaEmbedRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;
    use StoresConsentChoicesTrait;

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
        $this->captureConsentChoices(self::getContainer(), 'visitor', $choices);
    }
}
