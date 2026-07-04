<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Twig;

use Symfinity\UxBlocksCore\Css\BlocksCoreCssProvider;
use Symfinity\UxBlocksForm\Css\BlocksFormCssProvider;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Emits ux-blocks inline CSS once per request when ConsentBanner / PrivacyMediaEmbed render.
 */
final class PrivacySettingsUiAssetsRenderer
{
    private const REQUEST_FLAG = '_symfinity_privacy_settings_ui_assets';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ?BlocksCoreCssProvider $coreCssProvider = null,
        private readonly ?BlocksFormCssProvider $formCssProvider = null,
    ) {
    }

    public function renderInlineStylesOnce(): string
    {
        if ($this->alreadyRendered()) {
            return '';
        }

        if ($this->coreCssProvider === null || $this->formCssProvider === null) {
            return '';
        }

        $this->markRendered();

        return \sprintf(
            '<style id="ux-blocks-core-css">%s</style><style id="ux-blocks-form-css">%s</style>',
            $this->coreCssProvider->stylesheet(),
            $this->formCssProvider->stylesheet(),
        );
    }

    private function alreadyRendered(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return false;
        }

        return $request->attributes->getBoolean(self::REQUEST_FLAG);
    }

    private function markRendered(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $request->attributes->set(self::REQUEST_FLAG, true);
        }
    }
}
