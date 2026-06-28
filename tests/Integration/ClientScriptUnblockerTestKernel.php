<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use Symfony\Component\HttpKernel\Kernel;

final class ClientScriptUnblockerTestKernel extends PrivacySettingsTestKernel
{
    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
    }

    protected function configureContainer(\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $container): void
    {
        parent::configureContainer($container);

        if ('test_client_scripts' !== $this->environment) {
            return;
        }

        $container->extension('symfinity_privacy_settings', [
            'enforcement' => [
                'client_scripts' => true,
            ],
        ]);
    }
}
