<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfinity\PrivacySettingsBundle\Consent\InMemoryPreferenceStore;
use Symfinity\PrivacySettingsBundle\PrivacySettingsBundle;
use Symfinity\UiKernel\UiKernelBundle;
use Symfinity\UxBlocksCore\SymfinityUxBlocksCoreBundle;
use Symfinity\UxBlocksForm\SymfinityUxBlocksFormBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class PrivacySettingsTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/test_kernel';
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new StimulusBundle(),
            new TwigComponentBundle(),
            new UiKernelBundle(),
            new SymfinityUxBlocksCoreBundle(),
            new SymfinityUxBlocksFormBundle(),
            new PrivacySettingsBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
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
                [
                    'id' => 'analytics',
                    'label' => 'Analytics',
                    'default_state' => 'disabled',
                    'description' => 'Optional analytics',
                ],
                [
                    'id' => 'media',
                    'label' => 'Media',
                    'default_state' => 'disabled',
                    'description' => 'Embedded media',
                ],
            ],
        ]);

        $container->extension('symfinity_ux_blocks_core', [
            'fragment_ids' => false,
        ]);

        $container->extension('symfinity_ux_blocks_form', [
            'fragment_ids' => false,
        ]);

        $container->extension('symfinity_ui_kernel', [
            'schema_version' => '1.0',
            'default_theme' => 'default',
            'default_variant' => 'default',
        ]);

        $container->extension('framework', [
            'secret' => 'test-secret',
            'test' => true,
            'assets' => [],
            'router' => ['utf8' => true],
            'php_errors' => ['log' => false],
            'form' => ['enabled' => true],
            'validation' => ['enabled' => true],
            'csrf_protection' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);

        $container->extension('twig', [
            'form_themes' => ['form_div_layout.html.twig'],
            'paths' => [
                \dirname(__DIR__) . '/Integration/templates' => 'IntegrationTest',
            ],
        ]);

        $container->services()
            ->set('twig.extension.form', FormExtension::class)
                ->args([service('translator')->nullOnInvalid()])
                ->tag('twig.extension')
            ->set('twig.form.engine', TwigRendererEngine::class)
                ->args([['form_div_layout.html.twig'], service('twig')])
            ->set('twig.form.renderer', FormRenderer::class)
                ->args([service('twig.form.engine'), service('security.csrf.token_manager')->nullOnInvalid()])
                ->tag('twig.runtime')
            ->set('Symfinity\PrivacySettingsBundle\Consent\PreferenceStoreInterface', InMemoryPreferenceStore::class);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components',
            'defaults' => [
                'Symfinity\\UxBlocksCore\\Twig\\Components\\' => 'components',
                'Symfinity\\UxBlocksForm\\Twig\\Components\\' => 'components',
                'Symfinity\\PrivacySettingsBundle\\Twig\\Components\\' => 'components',
            ],
        ]);
    }

    protected function configureRoutes(\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/config/routes.yaml');
    }
}
