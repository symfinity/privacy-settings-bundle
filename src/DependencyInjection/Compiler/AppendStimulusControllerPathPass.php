<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AppendStimulusControllerPathPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $controllerPath,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('stimulus.asset_mapper.controllers_map_generator')) {
            return;
        }

        $definition = $container->getDefinition('stimulus.asset_mapper.controllers_map_generator');
        $paths = $definition->getArgument(2);
        if (!\is_array($paths)) {
            $paths = [];
        }

        if (!\in_array($this->controllerPath, $paths, true)) {
            $paths[] = $this->controllerPath;
        }

        $definition->replaceArgument(2, $paths);
    }
}
