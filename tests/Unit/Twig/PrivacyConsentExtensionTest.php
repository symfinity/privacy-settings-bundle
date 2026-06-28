<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfinity\PrivacySettingsBundle\Twig\PrivacyConsentExtension;

final class PrivacyConsentExtensionTest extends TestCase
{
    /** @var list<array{id: string, label: string, default_state: string, description?: string}> */
    private array $rawCategories = [
        [
            'id' => 'required',
            'label' => 'Required',
            'default_state' => 'required',
        ],
        [
            'id' => 'analytics',
            'label' => 'Analytics',
            'default_state' => 'disabled',
        ],
    ];

    public function testAllowedCategoryWhenStored(): void
    {
        $extension = $this->createExtension('dev', ['required' => true, 'analytics' => true]);

        self::assertTrue($extension->privacyConsent('analytics'));
    }

    public function testDeniedCategoryWhenStored(): void
    {
        $extension = $this->createExtension('dev', ['required' => true, 'analytics' => false]);

        self::assertFalse($extension->privacyConsent('analytics'));
    }

    public function testRequiredCategoryWithoutStoredDecision(): void
    {
        $extension = $this->createExtension('dev');

        self::assertTrue($extension->privacyConsent('required'));
        self::assertFalse($extension->privacyConsent('analytics'));
    }

    public function testUnknownCategoryThrowsInDev(): void
    {
        $extension = $this->createExtension('dev');

        $this->expectException(\LogicException::class);
        $extension->privacyConsent('unknown');
    }

    public function testUnknownCategoryReturnsFalseInProd(): void
    {
        $extension = $this->createExtension('prod');

        self::assertFalse($extension->privacyConsent('unknown'));
    }

    /**
     * @param array<string, bool>|null $storedChoices
     */
    private function createExtension(string $environment, ?array $storedChoices = null): PrivacyConsentExtension
    {
        $store = new InMemoryPreferenceStore();
        if (null !== $storedChoices) {
            $categories = (new CategoryModelNormalizer())->normalize($this->rawCategories);
            (new PreferenceCaptureService($store, new ConsentDecisionEventPublisher()))
                ->capture('visitor', $categories, $storedChoices);
        }

        return new PrivacyConsentExtension(
            new CategoryModelNormalizer(),
            new PreferenceRestoreService($store),
            $this->rawCategories,
            $environment,
        );
    }
}
