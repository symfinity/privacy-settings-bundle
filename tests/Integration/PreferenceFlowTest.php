<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceCaptureService;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceRestoreService;
use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;

final class PreferenceFlowTest extends TestCase
{
    public function testFirstContactAndRepeatContactRestoreFlow(): void
    {
        $categories = [
            new PrivacyCategory('required', 'Required', PrivacyCategory::DEFAULT_REQUIRED),
            new PrivacyCategory('analytics', 'Analytics', PrivacyCategory::DEFAULT_DISABLED),
        ];

        $store = new InMemoryPreferenceStore();
        $publisher = new ConsentDecisionEventPublisher();
        $capture = new PreferenceCaptureService($store, $publisher);
        $restore = new PreferenceRestoreService($store);

        $initial = $restore->effectiveChoices('visitor-a', $categories);
        self::assertSame(['required' => true, 'analytics' => false], $initial);

        $capture->capture('visitor-a', $categories, ['required' => true, 'analytics' => true]);
        $restored = $restore->effectiveChoices('visitor-a', $categories);

        self::assertTrue($restored['required']);
        self::assertTrue($restored['analytics']);
    }
}
