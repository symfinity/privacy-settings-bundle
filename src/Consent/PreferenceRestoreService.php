<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;

final class PreferenceRestoreService
{
    public function __construct(
        private readonly PreferenceStoreInterface $store,
    ) {
    }

    public function hasStoredDecision(string $subjectKey): bool
    {
        return null !== $this->store->find($subjectKey);
    }

    /**
     * @param list<PrivacyCategory> $categories
     *
     * @return array<string, bool>
     */
    public function effectiveChoices(string $subjectKey, array $categories): array
    {
        $stored = $this->store->find($subjectKey);
        if (null === $stored) {
            $choices = [];
            foreach ($categories as $category) {
                $choices[$category->id] = $category->isRequired()
                    ? true
                    : (PrivacyCategory::DEFAULT_ENABLED === $category->defaultState);
            }

            return $choices;
        }

        return $stored->toEffectiveChoices($categories);
    }
}
