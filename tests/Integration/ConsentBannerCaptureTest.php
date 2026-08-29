<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Controller\ConsentSubmitController;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ConsentBannerCaptureTest extends TestCase
{
    public function testSubmitControllerCapturesChoices(): void
    {
        $rawCategories = [
            [
                'id' => 'required',
                'label' => 'Required',
                'default_state' => 'required',
            ],
            [
                'id' => 'analytics',
                'label' => 'Analytics',
                'default_state' => 'disabled',
            ],
        ];

        $normalizer = new CategoryModelNormalizer();
        $categories = $normalizer->normalize($rawCategories);
        $store = new InMemoryPreferenceStore();
        $capture = new PreferenceCaptureService($store, new ConsentDecisionEventPublisher());
        $restore = new PreferenceRestoreService($store);

        $controller = new ConsentSubmitController($normalizer, $capture, $rawCategories);

        $request = Request::create(
            '/_privacy/consent/visitor-submit',
            'POST',
            ['privacy' => ['required' => '1', 'analytics' => '1']],
        );
        $request->headers->set('Referer', '/');

        $response = $controller($request, 'visitor-submit');

        self::assertSame(302, $response->getStatusCode());
        self::assertSame(['required' => true, 'analytics' => true], $restore->effectiveChoices('visitor-submit', $categories));
    }

    public function testRequiredCategoryStaysEnabledWhenOmittedFromPost(): void
    {
        $rawCategories = [
            [
                'id' => 'required',
                'label' => 'Required',
                'default_state' => 'required',
            ],
            [
                'id' => 'analytics',
                'label' => 'Analytics',
                'default_state' => 'disabled',
            ],
        ];

        $normalizer = new CategoryModelNormalizer();
        $categories = $normalizer->normalize($rawCategories);
        $store = new InMemoryPreferenceStore();
        $capture = new PreferenceCaptureService($store, new ConsentDecisionEventPublisher());
        $restore = new PreferenceRestoreService($store);
        $controller = new ConsentSubmitController($normalizer, $capture, $rawCategories);

        $request = Request::create(
            '/_privacy/consent/visitor-required',
            'POST',
            ['privacy' => []],
        );

        $controller($request, 'visitor-required');

        self::assertTrue($restore->effectiveChoices('visitor-required', $categories)['required']);
        self::assertFalse($restore->effectiveChoices('visitor-required', $categories)['analytics']);
    }

    public function testAcceptAllDecisionEnablesOptionalCategories(): void
    {
        $rawCategories = [
            [
                'id' => 'required',
                'label' => 'Required',
                'default_state' => 'required',
            ],
            [
                'id' => 'analytics',
                'label' => 'Analytics',
                'default_state' => 'disabled',
            ],
        ];

        $normalizer = new CategoryModelNormalizer();
        $categories = $normalizer->normalize($rawCategories);
        $store = new InMemoryPreferenceStore();
        $capture = new PreferenceCaptureService($store, new ConsentDecisionEventPublisher());
        $restore = new PreferenceRestoreService($store);
        $controller = new ConsentSubmitController($normalizer, $capture, $rawCategories);

        $request = Request::create(
            '/_privacy/consent/visitor-accept',
            'POST',
            ['consent_decision' => 'accept_all'],
        );

        $controller($request, 'visitor-accept');

        self::assertSame(['required' => true, 'analytics' => true], $restore->effectiveChoices('visitor-accept', $categories));
    }

    public function testRejectAllDecisionDisablesOptionalCategories(): void
    {
        $rawCategories = [
            [
                'id' => 'required',
                'label' => 'Required',
                'default_state' => 'required',
            ],
            [
                'id' => 'analytics',
                'label' => 'Analytics',
                'default_state' => 'disabled',
            ],
        ];

        $normalizer = new CategoryModelNormalizer();
        $categories = $normalizer->normalize($rawCategories);
        $store = new InMemoryPreferenceStore();
        $capture = new PreferenceCaptureService($store, new ConsentDecisionEventPublisher());
        $restore = new PreferenceRestoreService($store);
        $controller = new ConsentSubmitController($normalizer, $capture, $rawCategories);

        $request = Request::create(
            '/_privacy/consent/visitor-reject',
            'POST',
            ['consent_decision' => 'reject_all'],
        );

        $controller($request, 'visitor-reject');

        self::assertTrue($restore->effectiveChoices('visitor-reject', $categories)['required']);
        self::assertFalse($restore->effectiveChoices('visitor-reject', $categories)['analytics']);
    }
}
