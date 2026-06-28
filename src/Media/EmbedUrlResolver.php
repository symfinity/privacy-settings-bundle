<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Media;

use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class EmbedUrlResolver
{
    public const PROVIDER_YOUTUBE = 'youtube';
    public const PROVIDER_VIMEO = 'vimeo';
    public const PROVIDER_GOOGLE_MAPS = 'google_maps';
    public const PROVIDER_GENERIC = 'generic';

    /**
     * @param 'youtube'|'vimeo'|'google_maps'|'generic' $provider
     */
    public function resolve(
        string $provider,
        ?string $videoId = null,
        ?string $mapQuery = null,
        ?string $embedUrl = null,
    ): string {
        if (!in_array($provider, [
            self::PROVIDER_YOUTUBE,
            self::PROVIDER_VIMEO,
            self::PROVIDER_GOOGLE_MAPS,
            self::PROVIDER_GENERIC,
        ], true)) {
            throw PrivacySettingsException::unsupportedMediaProvider($provider);
        }

        return match ($provider) {
            self::PROVIDER_YOUTUBE => $this->resolveYoutube($videoId),
            self::PROVIDER_VIMEO => $this->resolveVimeo($videoId),
            self::PROVIDER_GOOGLE_MAPS => $this->resolveGoogleMaps($mapQuery),
            self::PROVIDER_GENERIC => $this->resolveGeneric($embedUrl),
        };
    }

    private function resolveYoutube(?string $videoId): string
    {
        $videoId = $this->requireNonEmpty($videoId, 'videoId');

        if (!preg_match('/^[A-Za-z0-9_-]{6,64}$/', $videoId)) {
            throw PrivacySettingsException::invalidMediaEmbedId('videoId', $videoId);
        }

        return sprintf('https://www.youtube-nocookie.com/embed/%s', rawurlencode($videoId));
    }

    private function resolveVimeo(?string $videoId): string
    {
        $videoId = $this->requireNonEmpty($videoId, 'videoId');

        if (!preg_match('/^\d{1,20}$/', $videoId)) {
            throw PrivacySettingsException::invalidMediaEmbedId('videoId', $videoId);
        }

        return sprintf('https://player.vimeo.com/video/%s', rawurlencode($videoId));
    }

    private function resolveGoogleMaps(?string $mapQuery): string
    {
        $mapQuery = $this->requireNonEmpty($mapQuery, 'mapQuery');

        return sprintf(
            'https://www.google.com/maps?q=%s&output=embed',
            rawurlencode($mapQuery),
        );
    }

    private function resolveGeneric(?string $embedUrl): string
    {
        $embedUrl = $this->requireNonEmpty($embedUrl, 'embedUrl');

        $parts = parse_url($embedUrl);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            throw PrivacySettingsException::invalidMediaEmbedUrl($embedUrl);
        }

        if ('https' !== strtolower($parts['scheme'])) {
            throw PrivacySettingsException::invalidMediaEmbedUrl($embedUrl);
        }

        return $embedUrl;
    }

    private function requireNonEmpty(?string $value, string $field): string
    {
        $value = null === $value ? '' : trim($value);
        if ('' === $value) {
            throw PrivacySettingsException::missingMediaEmbedField($field);
        }

        return $value;
    }
}
