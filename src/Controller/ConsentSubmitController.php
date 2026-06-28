<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Controller;

use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConsentSubmitController
{
    public function __construct(
        private readonly CategoryModelNormalizer $normalizer,
        private readonly PreferenceCaptureService $captureService,
        /** @var list<array{id: string, label: string, default_state: string, description?: string}> */
        #[Autowire(param: 'symfinity.privacy_settings.categories')]
        private readonly array $rawCategories,
    ) {
    }

    public function __invoke(Request $request, string $subjectKey): Response
    {
        /** @var list<PrivacyCategory> $categories */
        $categories = $this->normalizer->normalize($this->rawCategories);

        $decision = $request->request->getString('consent_decision');
        $source = 'ui';

        if ('accept_all' === $decision) {
            $choices = $this->choicesForAcceptAll($categories);
            $source = 'quick_accept';
        } elseif ('reject_all' === $decision) {
            $choices = $this->choicesForRejectAll($categories);
            $source = 'quick_reject';
        } else {
            /** @var array<string, mixed> $privacy */
            $privacy = $request->request->all('privacy');
            $choices = [];

            foreach ($categories as $category) {
                $choices[$category->id] = $category->isRequired() || isset($privacy[$category->id]);
            }
            $source = 'details';
        }

        $this->captureService->capture($subjectKey, $categories, $choices, $source);

        $referer = $request->headers->get('Referer');

        return new RedirectResponse(is_string($referer) && '' !== $referer ? $referer : '/');
    }

    /**
     * @param list<PrivacyCategory> $categories
     *
     * @return array<string, bool>
     */
    private function choicesForAcceptAll(array $categories): array
    {
        $choices = [];
        foreach ($categories as $category) {
            $choices[$category->id] = true;
        }

        return $choices;
    }

    /**
     * @param list<PrivacyCategory> $categories
     *
     * @return array<string, bool>
     */
    private function choicesForRejectAll(array $categories): array
    {
        $choices = [];
        foreach ($categories as $category) {
            $choices[$category->id] = $category->isRequired();
        }

        return $choices;
    }
}
