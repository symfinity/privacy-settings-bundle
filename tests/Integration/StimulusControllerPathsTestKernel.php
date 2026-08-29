<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\PrivacySettingsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\UX\StimulusBundle\StimulusBundle;

final class StimulusControllerPathsTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__).'/Fixtures/stimulus_paths_app';
    }

    public function getCacheDir(): string
    {
        return \dirname(__DIR__, 2).'/var/cache/stimulus_paths_test_kernel';
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new StimulusBundle(),
            new PrivacySettingsBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test-secret',
            'test' => true,
            'assets' => [],
            'router' => ['utf8' => true],
            'php_errors' => ['log' => false],
        ]);

        $container->extension('stimulus', [
            'controller_paths' => [
                '%kernel.project_dir%/assets/controllers',
            ],
        ]);

        $container->extension('symfinity_privacy_settings', [
            'enforcement' => [
                'client_scripts' => false,
            ],
            'categories' => [
                [
                    'id' => 'required',
                    'label' => 'Required',
                    'default_state' => 'required',
                ],
            ],
        ]);
    }

    protected function configureRoutes(\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator $routes): void
    {
    }
}
