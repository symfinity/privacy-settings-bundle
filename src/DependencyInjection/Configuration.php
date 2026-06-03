<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfinity_privacy_settings');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('categories')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('label')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('default_state')->defaultValue('disabled')->cannotBeEmpty()->end()
                            ->scalarNode('description')->defaultValue('')->end()
                        ->end()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
