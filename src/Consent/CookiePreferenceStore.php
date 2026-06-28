<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

final class CookiePreferenceStore implements PreferenceStoreInterface
{
    /** @var array<string, PrivacyPreferenceState> */
    private array $requestCache = [];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ConsentPreferenceCookieCodec $codec,
        private readonly PendingConsentCookieRegistry $pendingCookieRegistry,
        private readonly string $cookieName,
        private readonly int $lifetimeDays,
        private readonly string $cookiePath,
        private readonly ?string $cookieDomain,
        private readonly string $cookieSecure,
        private readonly string $cookieSameSite,
        private readonly bool $cookieHttpOnly,
    ) {
    }

    public function save(PrivacyPreferenceState $state): void
    {
        $this->requestCache[$state->subjectKey] = $state;

        $subjects = $this->loadAllSubjects();
        $subjects[$state->subjectKey] = [
            'c' => $state->choices,
            'u' => $state->updatedAt->format(\DateTimeInterface::ATOM),
            's' => $state->source,
        ];

        $request = $this->requestStack->getCurrentRequest();
        $secure = match ($this->cookieSecure) {
            'true' => true,
            'false' => false,
            default => null !== $request && $request->isSecure(),
        };

        $cookie = Cookie::create($this->cookieName)
            ->withValue($this->codec->encode($subjects))
            ->withExpires(new \DateTimeImmutable('+'.$this->lifetimeDays.' days'))
            ->withPath($this->cookiePath)
            ->withSecure($secure)
            ->withHttpOnly($this->cookieHttpOnly)
            ->withSameSite($this->cookieSameSite);

        if (null !== $this->cookieDomain && '' !== $this->cookieDomain) {
            $cookie = $cookie->withDomain($this->cookieDomain);
        }

        $this->pendingCookieRegistry->queue($cookie);
    }

    public function find(string $subjectKey): ?PrivacyPreferenceState
    {
        if (isset($this->requestCache[$subjectKey])) {
            return $this->requestCache[$subjectKey];
        }

        $payload = $this->loadAllSubjects()[$subjectKey] ?? null;
        if (!\is_array($payload)) {
            return null;
        }

        $updatedAt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $payload['u'] ?? '');
        if (!$updatedAt instanceof \DateTimeImmutable) {
            return null;
        }

        /** @var array<string, bool> $choices */
        $choices = $payload['c'] ?? [];

        return new PrivacyPreferenceState(
            subjectKey: $subjectKey,
            choices: $choices,
            updatedAt: $updatedAt,
            source: $payload['s'] ?? 'ui',
        );
    }

    /**
     * @return array<string, array{c: array<string, bool>, u: string, s: string}>
     */
    private function loadAllSubjects(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return [];
        }

        $decoded = $this->codec->decode($request->cookies->get($this->cookieName));

        return $decoded ?? [];
    }
}
