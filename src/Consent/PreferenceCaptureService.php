<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;

final class PreferenceCaptureService
{
    public function __construct(
        private readonly PreferenceStoreInterface $store,
        private readonly ConsentDecisionEventPublisher $eventPublisher,
    ) {
    }

    /**
     * @param list<PrivacyCategory> $categories
     * @param array<string, bool> $choices
     */
    public function capture(string $subjectKey, array $categories, array $choices, string $source = 'ui'): PrivacyPreferenceState
    {
        $previous = $this->store->find($subjectKey)?->toEffectiveChoices($categories) ?? [];

        $state = new PrivacyPreferenceState(
            subjectKey: $subjectKey,
            choices: $choices,
            updatedAt: new \DateTimeImmutable(),
            source: $source,
        );
        $state->assertValidAgainst($categories);

        $this->store->save($state);
        $effective = $state->toEffectiveChoices($categories);
        $this->eventPublisher->publishChanges($subjectKey, $previous, $effective, $source);

        return $state;
    }
}
