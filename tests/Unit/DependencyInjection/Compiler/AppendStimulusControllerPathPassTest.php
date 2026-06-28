<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\DependencyInjection\Compiler\AppendStimulusControllerPathPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class AppendStimulusControllerPathPassTest extends TestCase
{
    public function testAppendsBundlePathWithoutRemovingExistingPaths(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('stimulus.asset_mapper.controllers_map_generator', new Definition(
            \stdClass::class,
            ['arg0', 'arg1', ['/app/assets/controllers']],
        ));

        (new AppendStimulusControllerPathPass('/vendor/bundle/assets/controllers'))->process($container);

        $paths = $container->getDefinition('stimulus.asset_mapper.controllers_map_generator')->getArgument(2);
        self::assertSame(
            ['/app/assets/controllers', '/vendor/bundle/assets/controllers'],
            $paths,
        );
    }

    public function testDoesNotDuplicateBundlePath(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('stimulus.asset_mapper.controllers_map_generator', new Definition(
            \stdClass::class,
            ['arg0', 'arg1', ['/app/assets/controllers', '/vendor/bundle/assets/controllers']],
        ));

        (new AppendStimulusControllerPathPass('/vendor/bundle/assets/controllers'))->process($container);

        $paths = $container->getDefinition('stimulus.asset_mapper.controllers_map_generator')->getArgument(2);
        self::assertSame(
            ['/app/assets/controllers', '/vendor/bundle/assets/controllers'],
            $paths,
        );
    }

    public function testNoOpWhenStimulusServiceMissing(): void
    {
        $container = new ContainerBuilder();

        (new AppendStimulusControllerPathPass('/vendor/bundle/assets/controllers'))->process($container);

        self::assertFalse($container->hasDefinition('stimulus.asset_mapper.controllers_map_generator'));
    }
}
