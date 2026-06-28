<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class ConsentBannerSubjectKeyTest extends WebTestCase
{
    use InteractsWithTwigComponents;

    protected static function getKernelClass(): string
    {
        return PrivacySettingsTestKernel::class;
    }

    public function testDefaultSubjectKeyIsVisitorAndHidesAfterAcceptAll(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request = Request::create('/');
        $request->setSession($session);
        $container->get('request_stack')->push($request);

        $htmlBefore = (string) self::renderTwigComponent('ConsentBanner');
        self::assertStringContainsString('action="/_privacy/consent/visitor"', $htmlBefore);

        $normalizer = $container->get(CategoryModelNormalizer::class);
        $categories = $normalizer->normalize($container->getParameter('symfinity.privacy_settings.categories'));
        $store = $container->get(PreferenceStoreInterface::class);
        self::assertInstanceOf(InMemoryPreferenceStore::class, $store);
        (new PreferenceCaptureService($store, new ConsentDecisionEventPublisher()))
            ->capture('visitor', $categories, ['required' => true, 'analytics' => true, 'media' => true]);

        $htmlAfter = (string) self::renderTwigComponent('ConsentBanner');
        self::assertStringContainsString('data-privacy-consent', $htmlAfter);
        self::assertMatchesRegularExpression('/class="privacy-consent"[^>]*hidden/', $htmlAfter);
    }

    public function testExplicitSubjectKeyIsPreservedInSubmitUrl(): void
    {
        static::createClient();

        $html = (string) self::renderTwigComponent('ConsentBanner', [
            'subjectKey' => 'account-42',
        ]);

        self::assertStringContainsString('action="/_privacy/consent/account-42"', $html);
    }
}
