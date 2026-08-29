<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ConsentDecisionEventPublisher
{
    public function __construct(
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
    ) {
    }

    /**
     * @param array<string, bool> $before
     * @param array<string, bool> $after
     *
     * @return list<ConsentDecisionEvent>
     */
    public function publishChanges(string $subjectKey, array $before, array $after, string $reason = 'ui'): array
    {
        $events = [];

        foreach ($after as $categoryId => $state) {
            $previous = $before[$categoryId] ?? null;
            if ($previous === $state) {
                continue;
            }

            $event = new ConsentDecisionEvent(
                eventId: hash('sha256', sprintf('%s|%s|%s|%s', $subjectKey, $categoryId, (string) $previous, (string) $state)),
                subjectKey: $subjectKey,
                categoryId: $categoryId,
                previousState: is_bool($previous) ? $previous : null,
                newState: $state,
                timestamp: new \DateTimeImmutable(),
                reason: $reason,
            );
            $events[] = $event;
            $this->eventDispatcher?->dispatch($event);
        }

        return $events;
    }
}
