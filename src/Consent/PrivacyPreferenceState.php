<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class PrivacyPreferenceState
{
    /**
     * @param array<string, bool> $choices
     */
    public function __construct(
        public readonly string $subjectKey,
        public readonly array $choices,
        public readonly \DateTimeImmutable $updatedAt,
        public readonly string $source = 'ui',
    ) {
    }

    /**
     * @param list<PrivacyCategory> $categories
     */
    public function assertValidAgainst(array $categories): void
    {
        $byId = [];
        foreach ($categories as $category) {
            $byId[$category->id] = $category;
        }

        foreach ($this->choices as $id => $enabled) {
            if (!isset($byId[$id])) {
                throw PrivacySettingsException::unknownPreferenceCategory($id);
            }

            if (!is_bool($enabled)) {
                throw PrivacySettingsException::unknownCategoryState((string) $enabled);
            }

            if ($byId[$id]->isRequired() && false === $enabled) {
                throw PrivacySettingsException::requiredCategoryDisabled($id);
            }
        }
    }

    /**
     * @param list<PrivacyCategory> $categories
     */
    public function toEffectiveChoices(array $categories): array
    {
        $effective = [];
        foreach ($categories as $category) {
            $effective[$category->id] = $category->isRequired()
                ? true
                : ($this->choices[$category->id] ?? PrivacyCategory::DEFAULT_ENABLED === $category->defaultState);
        }

        return $effective;
    }
}
