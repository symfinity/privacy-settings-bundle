<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StimulusControllerPathsIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return StimulusControllerPathsTestKernel::class;
    }

    public function testBundleAppendsControllerPathWithoutReplacingAppPaths(): void
    {
        self::bootKernel();

        $generator = static::getContainer()->get('stimulus.asset_mapper.controllers_map_generator');
        self::assertTrue(method_exists($generator, 'getControllerPaths'));

        $paths = $generator->getControllerPaths();
        self::assertIsArray($paths);

        $kernel = self::$kernel;
        self::assertNotNull($kernel);
        $projectDir = $kernel->getProjectDir();
        $appControllers = $projectDir.'/assets/controllers';
        $bundleControllers = \dirname(__DIR__, 2).'/assets/controllers';

        self::assertContains($appControllers, $paths, 'App Stimulus controller path must remain registered.');
        self::assertContains($bundleControllers, $paths, 'Bundle Stimulus controller path must be appended.');
        self::assertGreaterThan(1, \count($paths), 'Multiple controller paths must coexist.');
    }
}
