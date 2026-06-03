<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PrivacySettingsExtension extends Extension
{
    public function getAlias(): string
    {
        return 'symfinity_privacy_settings';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $container->setParameter('symfinity.privacy_settings.categories', $config['categories']);
    }
}
