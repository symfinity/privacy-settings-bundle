<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Twig\Components;

use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('ConsentBanner', template: '@SymfinityPrivacySettings/components/ConsentBanner.html.twig')]
final class ConsentBanner
{
    /** @deprecated Layout is fixed: quick bottom-right, details centered. Kept for BC only. */
    public string $position = 'corner';

    public ?string $privacyPolicyUrl = null;

    public ?string $imprintUrl = null;

    private string $subjectKey = 'visitor';

    private bool $needsConsent = true;

    /** @var list<PrivacyCategory> */
    private array $categories = [];

    /** @var array<string, bool> */
    private array $choices = [];

    public function __construct(
        private readonly CategoryModelNormalizer $normalizer,
        private readonly PreferenceRestoreService $restoreService,
        private readonly UrlGeneratorInterface $urlGenerator,
        /** @var list<array{id: string, label: string, default_state: string, description?: string}> */
        #[Autowire(param: 'symfinity.privacy_settings.categories')]
        private readonly array $rawCategories,
        #[Autowire(param: 'symfinity.privacy_settings.enforcement.client_scripts')]
        private readonly bool $clientScriptsEnabled,
    ) {
    }

    public function mount(
        ?string $subjectKey = null,
        string $position = 'corner',
        ?string $privacyPolicyUrl = null,
        ?string $imprintUrl = null,
    ): void {
        $this->subjectKey = $this->resolveSubjectKey($subjectKey);
        $this->position = \in_array($position, ['modal', 'bottom', 'sheet', 'corner'], true) ? $position : 'corner';
        $this->privacyPolicyUrl = $this->normalizeOptionalUrl($privacyPolicyUrl);
        $this->imprintUrl = $this->normalizeOptionalUrl($imprintUrl);
        $this->categories = $this->normalizer->normalize($this->rawCategories);
        $this->choices = $this->restoreService->effectiveChoices($this->subjectKey, $this->categories);
        $this->needsConsent = !$this->restoreService->hasStoredDecision($this->subjectKey);
    }

    #[ExposeInTemplate('needsConsent')]
    public function exposedNeedsConsent(): bool
    {
        return $this->needsConsent;
    }

    #[ExposeInTemplate('subjectKey')]
    public function exposedSubjectKey(): string
    {
        return $this->subjectKey;
    }

    #[ExposeInTemplate('position')]
    public function exposedPosition(): string
    {
        return $this->position;
    }

    /**
     * @return list<PrivacyCategory>
     */
    #[ExposeInTemplate('categories')]
    public function exposedCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return array<string, bool>
     */
    #[ExposeInTemplate('choices')]
    public function exposedChoices(): array
    {
        return $this->choices;
    }

    #[ExposeInTemplate('submitUrl')]
    public function submitUrl(): string
    {
        return $this->urlGenerator->generate('symfinity_privacy_settings_consent_submit', [
            'subjectKey' => $this->subjectKey,
        ]);
    }

    #[ExposeInTemplate('privacyPolicyUrl')]
    public function exposedPrivacyPolicyUrl(): ?string
    {
        return $this->privacyPolicyUrl;
    }

    #[ExposeInTemplate('imprintUrl')]
    public function exposedImprintUrl(): ?string
    {
        return $this->imprintUrl;
    }

    #[ExposeInTemplate('clientScriptsEnabled')]
    public function exposedClientScriptsEnabled(): bool
    {
        return $this->clientScriptsEnabled;
    }

    private function normalizeOptionalUrl(?string $url): ?string
    {
        if (null === $url) {
            return null;
        }

        $url = trim($url);

        return '' === $url ? null : $url;
    }

    private function resolveSubjectKey(?string $subjectKey): string
    {
        if (null !== $subjectKey && '' !== trim($subjectKey)) {
            return trim($subjectKey);
        }

        // Anonymous consent is scoped by the signed consent cookie (CookiePreferenceStore),
        // not by embedding the session id in the subject key.
        return 'visitor';
    }
}
