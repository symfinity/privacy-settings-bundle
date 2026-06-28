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

final class ConsentReopenRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

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
        $container = self::getContainer();
        $normalizer = $container->get(CategoryModelNormalizer::class);
        $categories = $normalizer->normalize($container->getParameter('symfinity.privacy_settings.categories'));
        $store = $container->get(PreferenceStoreInterface::class);
        self::assertInstanceOf(InMemoryPreferenceStore::class, $store);
        (new PreferenceCaptureService($store, new ConsentDecisionEventPublisher()))
            ->capture('visitor', $categories, $choices);
    }
}
