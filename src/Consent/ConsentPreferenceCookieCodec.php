<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

/**
 * Signed JSON payload for consent cookies.
 *
 * @phpstan-type SubjectPayload array{c: array<string, bool>, u: string, s: string}
 * @phpstan-type CookiePayload array{v: int, p: string, subjects: array<string, SubjectPayload>}
 */
final class ConsentPreferenceCookieCodec
{
    private const FORMAT_VERSION = 1;

    public function __construct(
        private readonly string $secret,
        private readonly string $policyVersion,
    ) {
    }

    /**
     * @return array<string, SubjectPayload>|null
     */
    public function decode(?string $raw): ?array
    {
        if (null === $raw || '' === $raw) {
            return null;
        }

        $parts = explode('.', $raw, 3);
        if (3 !== \count($parts)) {
            return null;
        }

        [$version, $payload, $signature] = $parts;
        if ('v'.self::FORMAT_VERSION !== $version) {
            return null;
        }

        if (!hash_equals($this->sign($payload), $signature)) {
            return null;
        }

        try {
            /** @var CookiePayload $decoded */
            $decoded = json_decode($this->base64UrlDecode($payload), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if ($decoded['v'] !== self::FORMAT_VERSION) {
            return null;
        }

        if ($decoded['p'] !== $this->policyVersion) {
            return null;
        }

        return $decoded['subjects'];
    }

    /**
     * @param array<string, SubjectPayload> $subjects
     */
    public function encode(array $subjects): string
    {
        $payload = $this->base64UrlEncode(json_encode([
            'v' => self::FORMAT_VERSION,
            'p' => $this->policyVersion,
            'subjects' => $subjects,
        ], \JSON_THROW_ON_ERROR));

        return 'v'.self::FORMAT_VERSION.'.'.$payload.'.'.$this->sign($payload);
    }

    private function sign(string $payload): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $payload, $this->secret, true));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = (4 - \strlen($value) % 4) % 4;
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', $padding), true);

        return false === $decoded ? '' : $decoded;
    }
}
