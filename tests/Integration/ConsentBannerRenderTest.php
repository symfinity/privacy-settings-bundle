<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ConsentBannerRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testConsentBannerRendersFormRoles(): void
    {
        self::bootKernel();

        $html = (string) self::renderTwigComponent('ConsentBanner', [
            'subjectKey' => 'visitor-test',
        ]);

        self::assertStringContainsString('data-privacy-consent', $html);
        self::assertStringContainsString('privacy-consent__backdrop', $html);
        self::assertStringContainsString('data-ui-role="form-actions"', $html);
        self::assertStringContainsString('name="privacy[analytics]"', $html);
        self::assertStringContainsString('Accept all', $html);
        self::assertStringContainsString('Reject optional', $html);
        self::assertStringContainsString('Manage settings', $html);
        self::assertStringContainsString('Save settings', $html);
        self::assertStringContainsString('data-controller="privacy-settings-bundle--consent"', $html);
        self::assertStringContainsString('privacy-settings-bundle/styles/privacy-settings-consent', $html);
        self::assertStringContainsString('type="submit"', $html);
        self::assertStringContainsString('data-turbo="false"', $html);
    }
}
