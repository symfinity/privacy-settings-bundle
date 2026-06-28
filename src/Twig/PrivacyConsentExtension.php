<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Twig;

use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PrivacyConsentExtension extends AbstractExtension
{
    /** @var list<PrivacyCategory>|null */
    private ?array $normalizedCategories = null;

    public function __construct(
        private readonly CategoryModelNormalizer $normalizer,
        private readonly PreferenceRestoreService $restoreService,
        /** @var list<array{id: string, label: string, default_state: string, description?: string}> */
        #[Autowire(param: 'symfinity.privacy_settings.categories')]
        private readonly array $rawCategories,
        #[Autowire(param: 'kernel.environment')]
        private readonly string $environment,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('privacy_consent', $this->privacyConsent(...)),
            new TwigFunction('privacy_effective_choices', $this->privacyEffectiveChoices(...)),
        ];
    }

    public function privacyConsent(string $categoryId, ?string $subjectKey = null): bool
    {
        $categories = $this->categories();
        if (!$this->isKnownCategory($categoryId, $categories)) {
            if ('prod' === $this->environment) {
                return false;
            }

            throw new \LogicException(sprintf(
                'Unknown privacy category "%s". Configure it under symfinity_privacy_settings.categories.',
                $categoryId,
            ));
        }

        $subjectKey = $this->resolveSubjectKey($subjectKey);
        $choices = $this->restoreService->effectiveChoices($subjectKey, $categories);

        return $choices[$categoryId] ?? false;
    }

    /**
     * @return array<string, bool>
     */
    public function privacyEffectiveChoices(?string $subjectKey = null): array
    {
        return $this->restoreService->effectiveChoices(
            $this->resolveSubjectKey($subjectKey),
            $this->categories(),
        );
    }

    /**
     * @param list<PrivacyCategory> $categories
     */
    private function isKnownCategory(string $categoryId, array $categories): bool
    {
        foreach ($categories as $category) {
            if ($category->id === $categoryId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<PrivacyCategory>
     */
    private function categories(): array
    {
        if (null === $this->normalizedCategories) {
            $this->normalizedCategories = $this->normalizer->normalize($this->rawCategories);
        }

        return $this->normalizedCategories;
    }

    private function resolveSubjectKey(?string $subjectKey): string
    {
        if (null !== $subjectKey && '' !== trim($subjectKey)) {
            return trim($subjectKey);
        }

        return 'visitor';
    }
}
