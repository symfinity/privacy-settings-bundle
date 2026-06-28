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
        $payload = $all[$subjectKey] ?? null;

        if (!\is_array($payload)) {
            return null;
        }

        $updatedAt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $payload['updatedAt'] ?? '');

        if (!$updatedAt instanceof \DateTimeImmutable) {
            return null;
        }

        /** @var array<string, bool> $choices */
        $choices = $payload['choices'] ?? [];

        return new PrivacyPreferenceState(
            subjectKey: $payload['subjectKey'] ?? $subjectKey,
            choices: $choices,
            updatedAt: $updatedAt,
            source: $payload['source'] ?? 'ui',
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
