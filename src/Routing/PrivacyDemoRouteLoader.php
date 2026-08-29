<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Routing;

use Symfinity\PrivacySettingsBundle\Controller\PrivacyDemoController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class PrivacyDemoRouteLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly bool $routeEnabled,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the "privacy_settings_demo" loader twice.');
        }

        $this->loaded = true;

        $collection = new RouteCollection();
        if (!$this->routeEnabled) {
            return $collection;
        }

        $collection->add('symfinity_privacy_settings_demo', new Route(
            '/_privacy/demo',
            ['_controller' => PrivacyDemoController::class],
            methods: ['GET'],
        ));

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'privacy_settings_demo' === $type;
    }
}
