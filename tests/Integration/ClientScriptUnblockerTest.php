<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ClientScriptUnblockerTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testClientScriptsDisabledDoesNotRegisterUnblockerController(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);

        $html = (string) self::renderTwigComponent('ConsentBanner', [
            'subjectKey' => 'visitor-unblocker-off',
        ]);

        self::assertStringNotContainsString('privacy-settings-bundle--script-unblocker', $html);
    }

    public function testClientScriptsEnabledRegistersUnblockerController(): void
    {
        self::bootKernel(['environment' => 'test_client_scripts', 'debug' => true]);

        $html = (string) self::renderTwigComponent('ConsentBanner', [
            'subjectKey' => 'visitor-unblocker-on',
        ]);

        self::assertStringContainsString('privacy-settings-bundle--script-unblocker', $html);
        self::assertStringContainsString('privacy-settings-effective-choices', $html);
    }

    protected static function getKernelClass(): string
    {
        return ClientScriptUnblockerTestKernel::class;
    }
}
