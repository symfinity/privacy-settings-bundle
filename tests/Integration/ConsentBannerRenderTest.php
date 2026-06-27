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
        self::assertStringContainsString('data-ui-role="field"', $html);
        self::assertStringContainsString('data-ui-role="form-actions"', $html);
        self::assertStringContainsString('name="privacy[analytics]"', $html);
        self::assertStringContainsString('Save preferences', $html);
    }
}
