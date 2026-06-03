<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

interface PreferenceStoreInterface
{
    public function save(PrivacyPreferenceState $state): void;

    public function find(string $subjectKey): ?PrivacyPreferenceState;
}
