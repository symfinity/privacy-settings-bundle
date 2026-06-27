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

        /** @var array<string, mixed> $privacy */
        $privacy = $request->request->all('privacy');
        $choices = [];

        foreach ($categories as $category) {
            $choices[$category->id] = $category->isRequired() || isset($privacy[$category->id]);
        }

        $this->captureService->capture($subjectKey, $categories, $choices);

        $referer = $request->headers->get('Referer');

        return new RedirectResponse(is_string($referer) && '' !== $referer ? $referer : '/');
    }
}
