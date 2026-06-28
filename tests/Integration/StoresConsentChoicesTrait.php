<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait StoresConsentChoicesTrait
{
    /**
     * @param array<string, bool> $choices
     */
    private function captureConsentChoices(ContainerInterface $container, string $subjectKey, array $choices): void
    {
        $normalizer = $container->get(CategoryModelNormalizer::class);
        \assert($normalizer instanceof CategoryModelNormalizer);

        /** @var list<array{id: string, label: string, default_state: string, description?: string}> $rawCategories */
        $rawCategories = $container->getParameter('symfinity.privacy_settings.categories');
        $categories = $normalizer->normalize($rawCategories);

        $store = $container->get(PreferenceStoreInterface::class);
        \assert($store instanceof InMemoryPreferenceStore);

        (new PreferenceCaptureService($store, new ConsentDecisionEventPublisher()))
            ->capture($subjectKey, $categories, $choices);
    }
}
