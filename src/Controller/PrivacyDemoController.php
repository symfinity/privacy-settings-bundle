<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Controller;

use Symfony\Component\AssetMapper\ImportMap\ImportMapRenderer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class PrivacyDemoController
{
    public function __construct(
        private readonly Environment $twig,
        #[Autowire(param: 'symfinity.privacy_settings.demo.route_enabled')]
        private readonly bool $routeEnabled,
        private readonly ?ImportMapRenderer $importMapRenderer = null,
    ) {
    }

    public function __invoke(): Response
    {
        if (!$this->routeEnabled) {
            throw new NotFoundHttpException('Privacy settings demo route is disabled.');
        }

        return new Response(
            $this->twig->render('@SymfinityPrivacySettings/demo/showcase.html.twig', [
                'headImportmap' => $this->renderOptionalImportmap(),
            ]),
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    private function renderOptionalImportmap(): string
    {
        if ($this->importMapRenderer === null) {
            return '';
        }

        try {
            return $this->importMapRenderer->render('app');
        } catch (\InvalidArgumentException) {
            return '';
        }
    }
}
