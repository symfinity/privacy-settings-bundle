<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PrivacySettingsExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $packageDir = \dirname(__DIR__, 2);

        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        $packageDir . '/assets' => 'privacy-settings-bundle',
                    ],
                ],
            ]);
        }

        $container->prependExtensionConfig('twig', [
            'paths' => [
                $packageDir . '/templates' => 'SymfinityPrivacySettings',
            ],
        ]);

        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Symfinity\\PrivacySettingsBundle\\Twig\\Components\\' => [
                    'template_directory' => 'components',
                ],
            ],
        ]);
    }

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

        /** @var list<array{id: string, label: string, default_state: string, description?: string}> $categories */
        $categories = $config['categories'];
        $container->setParameter('symfinity.privacy_settings.categories', $categories);
    }
}
