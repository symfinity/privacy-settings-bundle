<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionPreferenceStore implements PreferenceStoreInterface
{
    private const SESSION_KEY = '_symfinity_privacy_preferences';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function save(PrivacyPreferenceState $state): void
    {
        $session = $this->session();
        /** @var array<string, array{subjectKey: string, choices: array<string, bool>, updatedAt: string, source: string}> $all */
        $all = $session->get(self::SESSION_KEY, []);
        $all[$state->subjectKey] = [
            'subjectKey' => $state->subjectKey,
            'choices' => $state->choices,
            'updatedAt' => $state->updatedAt->format(\DateTimeInterface::ATOM),
            'source' => $state->source,
        ];
        $session->set(self::SESSION_KEY, $all);
    }

    public function find(string $subjectKey): ?PrivacyPreferenceState
    {
        $session = $this->session();
        /** @var array<string, array{subjectKey: string, choices: array<string, bool>, updatedAt: string, source: string}> $all */
        $all = $session->get(self::SESSION_KEY, []);
        /** @var mixed $rawPayload */
        $rawPayload = $all[$subjectKey] ?? null;

        if (!\is_array($rawPayload)) {
            return null;
        }

        /** @var array<mixed> $payload */
        $payload = $rawPayload;

        if (!\is_string($payload['updatedAt'] ?? null)
            || !\is_array($payload['choices'] ?? null)
            || !\is_string($payload['subjectKey'] ?? null)
            || !\is_string($payload['source'] ?? null)) {
            return null;
        }

        $updatedAt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $payload['updatedAt']);

        if (!$updatedAt instanceof \DateTimeImmutable) {
            return null;
        }

        /** @var array<string, bool> $choices */
        $choices = $payload['choices'];

        return new PrivacyPreferenceState(
            subjectKey: $payload['subjectKey'],
            choices: $choices,
            updatedAt: $updatedAt,
            source: $payload['source'],
        );
    }

    private function session(): SessionInterface
    {
        $session = $this->requestStack->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        return $session;
    }
}
