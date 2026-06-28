<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Twig\Components;

use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;
use Symfinity\PrivacySettingsBundle\Media\EmbedUrlResolver;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('PrivacyMediaEmbed', template: '@SymfinityPrivacySettings/components/PrivacyMediaEmbed.html.twig')]
final class PrivacyMediaEmbed
{
    /** @var 'youtube'|'vimeo'|'google_maps'|'generic' */
    public string $provider = EmbedUrlResolver::PROVIDER_YOUTUBE;

    public ?string $videoId = null;

    public ?string $mapQuery = null;

    public ?string $embedUrl = null;

    public string $title = '';

    public string $aspectRatio = '16 / 9';

    public ?string $embedId = null;

    public ?string $subjectKey = null;

    private bool $mediaAllowed = false;

    private string $resolvedEmbedUrl = '';

    public function __construct(
        private readonly CategoryModelNormalizer $normalizer,
        private readonly PreferenceRestoreService $restoreService,
        private readonly EmbedUrlResolver $embedUrlResolver,
        /** @var list<array{id: string, label: string, default_state: string, description?: string}> */
        #[Autowire(param: 'symfinity.privacy_settings.categories')]
        private readonly array $rawCategories,
    ) {
    }

    public function mount(
        string $provider = EmbedUrlResolver::PROVIDER_YOUTUBE,
        ?string $videoId = null,
        ?string $mapQuery = null,
        ?string $embedUrl = null,
        string $title = '',
        string $aspectRatio = '16 / 9',
        ?string $embedId = null,
        ?string $subjectKey = null,
    ): void {
        $this->provider = $this->normalizeProvider($provider);
        $this->videoId = $videoId;
        $this->mapQuery = $mapQuery;
        $this->embedUrl = $embedUrl;
        $this->title = trim($title);
        $this->aspectRatio = $this->normalizeAspectRatio($aspectRatio);
        $this->embedId = $this->resolveEmbedId($embedId);
        $this->subjectKey = $this->resolveSubjectKey($subjectKey);

        $categories = $this->normalizer->normalize($this->rawCategories);
        $choices = $this->restoreService->effectiveChoices($this->subjectKey, $categories);
        $this->mediaAllowed = $choices['media'] ?? false;
        $this->resolvedEmbedUrl = $this->embedUrlResolver->resolve(
            $this->provider,
            $this->videoId,
            $this->mapQuery,
            $this->embedUrl,
        );
    }

    #[ExposeInTemplate('mediaAllowed')]
    public function exposedMediaAllowed(): bool
    {
        return $this->mediaAllowed;
    }

    #[ExposeInTemplate('resolvedEmbedUrl')]
    public function exposedResolvedEmbedUrl(): string
    {
        return $this->resolvedEmbedUrl;
    }

    #[ExposeInTemplate('embedId')]
    public function exposedEmbedId(): string
    {
        return $this->embedId ?? '';
    }

    #[ExposeInTemplate('aspectRatio')]
    public function exposedAspectRatio(): string
    {
        return $this->aspectRatio;
    }

    #[ExposeInTemplate('title')]
    public function exposedTitle(): string
    {
        return $this->title;
    }

    private function resolveEmbedId(?string $embedId): string
    {
        if (null !== $embedId && '' !== trim($embedId)) {
            return trim($embedId);
        }

        return substr(hash('sha256', implode('|', [
            $this->provider,
            $this->videoId ?? '',
            $this->mapQuery ?? '',
            $this->embedUrl ?? '',
            $this->title,
        ])), 0, 16);
    }

    private function resolveSubjectKey(?string $subjectKey): string
    {
        if (null !== $subjectKey && '' !== trim($subjectKey)) {
            return trim($subjectKey);
        }

        return 'visitor';
    }

    /**
     * @return 'youtube'|'vimeo'|'google_maps'|'generic'
     */
    private function normalizeAspectRatio(string $aspectRatio): string
    {
        $aspectRatio = trim($aspectRatio);
        if ('' === $aspectRatio) {
            return '16 / 9';
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)$/', $aspectRatio, $matches)) {
            return $matches[1].' / '.$matches[2];
        }

        return '16 / 9';
    }

    private function normalizeProvider(string $provider): string
    {
        if (!in_array($provider, [
            EmbedUrlResolver::PROVIDER_YOUTUBE,
            EmbedUrlResolver::PROVIDER_VIMEO,
            EmbedUrlResolver::PROVIDER_GOOGLE_MAPS,
            EmbedUrlResolver::PROVIDER_GENERIC,
        ], true)) {
            throw PrivacySettingsException::unsupportedMediaProvider($provider);
        }

        return $provider;
    }
}
