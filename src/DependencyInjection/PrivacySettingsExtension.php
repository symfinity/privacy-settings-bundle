<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\DependencyInjection;

use Symfinity\PrivacySettingsBundle\Consent\ConsentPreferenceCookieCodec;
use Symfinity\PrivacySettingsBundle\Consent\CookiePreferenceStore;
use Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface;
use Symfinity\PrivacySettingsBundle\Consent\SessionPreferenceStore;
use Symfinity\PrivacySettingsBundle\EventSubscriber\ConsentCookieResponseSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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

        // Stimulus controller_paths: append-only via AppendStimulusControllerPathPass — see
        // docs/contracts/stimulus-controller-path-boundary.md (MUST NOT prepend controller_paths here).
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
        $container->setParameter(
            'symfinity.privacy_settings.enforcement.client_scripts',
            $config['enforcement']['client_scripts'],
        );

        $driver = $config['storage']['driver'];
        $container->setParameter('symfinity.privacy_settings.storage.driver', $driver);

        if ('cookie' === $driver) {
            $cookie = $config['storage']['cookie'];
            $container->setParameter('symfinity.privacy_settings.storage.cookie.name', $cookie['name']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.lifetime_days', $cookie['lifetime_days']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.policy_version', $cookie['policy_version']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.path', $cookie['path']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.domain', $cookie['domain']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.secure', $cookie['secure']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.samesite', $cookie['samesite']);
            $container->setParameter('symfinity.privacy_settings.storage.cookie.http_only', $cookie['http_only']);

            $container->register(ConsentPreferenceCookieCodec::class)
                ->setAutowired(false)
                ->setAutoconfigured(false)
                ->setArguments([
                    '%kernel.secret%',
                    '%symfinity.privacy_settings.storage.cookie.policy_version%',
                ]);

            $container->register(CookiePreferenceStore::class)
                ->setAutowired(false)
                ->setAutoconfigured(false)
                ->setArguments([
                    '$requestStack' => new Reference('request_stack'),
                    '$codec' => new Reference(ConsentPreferenceCookieCodec::class),
                    '$pendingCookieRegistry' => new Reference('symfinity.privacy_settings.pending_cookie_registry'),
                    '$cookieName' => '%symfinity.privacy_settings.storage.cookie.name%',
                    '$lifetimeDays' => '%symfinity.privacy_settings.storage.cookie.lifetime_days%',
                    '$cookiePath' => '%symfinity.privacy_settings.storage.cookie.path%',
                    '$cookieDomain' => '%symfinity.privacy_settings.storage.cookie.domain%',
                    '$cookieSecure' => '%symfinity.privacy_settings.storage.cookie.secure%',
                    '$cookieSameSite' => '%symfinity.privacy_settings.storage.cookie.samesite%',
                    '$cookieHttpOnly' => '%symfinity.privacy_settings.storage.cookie.http_only%',
                ]);

            $container->setAlias(PreferenceStoreInterface::class, CookiePreferenceStore::class);
        } else {
            $container->setAlias(PreferenceStoreInterface::class, SessionPreferenceStore::class);
        }
    }
}
