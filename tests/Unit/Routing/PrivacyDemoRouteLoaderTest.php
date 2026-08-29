<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit\Routing;

use Symfinity\PrivacySettingsBundle\Routing\PrivacyDemoRouteLoader;
use PHPUnit\Framework\TestCase;

final class PrivacyDemoRouteLoaderTest extends TestCase
{
    public function testLoadReturnsEmptyCollectionWhenDisabled(): void
    {
        $loader = new PrivacyDemoRouteLoader(false);

        self::assertCount(0, $loader->load('.', 'privacy_settings_demo'));
    }

    public function testLoadRegistersDemoRouteWhenEnabled(): void
    {
        $loader = new PrivacyDemoRouteLoader(true);

        $collection = $loader->load('.', 'privacy_settings_demo');

        self::assertTrue($collection->get('symfinity_privacy_settings_demo') !== null);
    }
}
