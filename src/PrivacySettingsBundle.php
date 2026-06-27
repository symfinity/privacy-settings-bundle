<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle;

use Symfony\Bundle\TwigBundle\DependencyInjection\Configurator\TwigConfigurator;
use Symfinity\PrivacySettingsBundle\DependencyInjection\PrivacySettingsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class PrivacySettingsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new PrivacySettingsExtension();
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getPath() . '/config/routes.yaml');
    }

    public function configureTwig(TwigConfigurator $configurator): void
    {
        $configurator->path($this->getPath() . '/templates', 'SymfinityPrivacySettings');
    }
}
