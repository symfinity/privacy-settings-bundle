<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Event;

final class ConsentDecisionEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $subjectKey,
        public readonly string $categoryId,
        public readonly ?bool $previousState,
        public readonly bool $newState,
        public readonly \DateTimeImmutable $timestamp,
        public readonly string $reason,
    ) {
    }
}
