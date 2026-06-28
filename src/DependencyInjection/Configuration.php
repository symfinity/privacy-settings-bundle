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
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('driver')
                            ->values(['cookie', 'session'])
                            ->defaultValue('cookie')
                        ->end()
                        ->arrayNode('cookie')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('symfinity_privacy_consent')->cannotBeEmpty()->end()
                                ->integerNode('lifetime_days')->defaultValue(180)->min(180)->end()
                                ->scalarNode('policy_version')->defaultValue('1')->cannotBeEmpty()->end()
                                ->scalarNode('path')->defaultValue('/')->cannotBeEmpty()->end()
                                ->scalarNode('domain')->defaultNull()->end()
                                ->enumNode('secure')->values(['auto', 'true', 'false'])->defaultValue('auto')->end()
                                ->enumNode('samesite')->values(['lax', 'strict', 'none'])->defaultValue('lax')->end()
                                ->booleanNode('http_only')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('enforcement')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('client_scripts')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
