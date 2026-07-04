<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection\Compiler;

use Symfinity\PrivacySettingsBundle\Controller\PrivacyDemoController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPrivacyDemoControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        if ($container->hasDefinition(PrivacyDemoController::class)) {
            return;
        }

        $container->register(PrivacyDemoController::class, PrivacyDemoController::class)
            ->setAutowired(true)
            ->setAutoconfigured(false)
            ->setPublic(true)
            ->addTag('controller.service_arguments')
            ->setArgument(
                '$importMapRenderer',
                new Reference('asset_mapper.importmap.renderer', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
            );
    }
}
