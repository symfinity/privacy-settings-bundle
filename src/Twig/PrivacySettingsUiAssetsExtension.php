<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PrivacySettingsUiAssetsExtension extends AbstractExtension
{
    public function __construct(
        private readonly PrivacySettingsUiAssetsRenderer $uiAssetsRenderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('privacy_settings_ui_assets', $this->uiAssetsRenderer->renderInlineStylesOnce(...), ['is_safe' => ['html']]),
        ];
    }
}
