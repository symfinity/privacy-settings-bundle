<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Consent;

use Symfony\Component\HttpFoundation\Cookie;

final class PendingConsentCookieRegistry
{
    private ?Cookie $cookie = null;

    public function queue(Cookie $cookie): void
    {
        $this->cookie = $cookie;
    }

    public function consume(): ?Cookie
    {
        $cookie = $this->cookie;
        $this->cookie = null;

        return $cookie;
    }
}
