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
    /** @var ''|'bottom'|'sheet' */
    public string $position = 'bottom';

    private string $subjectKey = 'visitor';

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
    ) {
    }

    public function mount(string $subjectKey = 'visitor', string $position = 'bottom'): void
    {
        $this->subjectKey = '' !== trim($subjectKey) ? trim($subjectKey) : 'visitor';
        $this->position = \in_array($position, ['bottom', 'sheet'], true) ? $position : 'bottom';
        $this->categories = $this->normalizer->normalize($this->rawCategories);
        $this->choices = $this->restoreService->effectiveChoices($this->subjectKey, $this->categories);
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
}
