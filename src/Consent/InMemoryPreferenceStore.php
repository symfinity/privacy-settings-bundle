<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

final class InMemoryPreferenceStore implements PreferenceStoreInterface
{
    /** @var array<string, PrivacyPreferenceState> */
    private array $storage = [];

    public function save(PrivacyPreferenceState $state): void
    {
        $this->storage[$state->subjectKey] = $state;
    }

    public function find(string $subjectKey): ?PrivacyPreferenceState
    {
        return $this->storage[$subjectKey] ?? null;
    }
}
