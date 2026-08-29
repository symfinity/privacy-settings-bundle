<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ConsentReopenRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;
    use StoresConsentChoicesTrait;

    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testBannerShellRendersWhenStoredDecisionExists(): void
    {
        self::bootKernel();
        $this->capture(['required' => true, 'analytics' => false, 'media' => false]);

        $html = (string) self::renderTwigComponent('ConsentBanner', [
            'subjectKey' => 'visitor',
        ]);

        self::assertStringContainsString('data-privacy-consent', $html);
        self::assertStringContainsString('hidden', $html);
        self::assertStringContainsString('privacy-settings-effective-choices', $html);
        self::assertStringContainsString('privacy-settings-bundle--consent', $html);
    }

    /**
     * @param array<string, bool> $choices
     */
    private function capture(array $choices): void
    {
        $this->captureConsentChoices(self::getContainer(), 'visitor', $choices);
    }
}
