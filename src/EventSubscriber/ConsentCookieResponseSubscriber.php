<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\EventSubscriber;

use Symfinity\PrivacySettingsBundle\Consent\PendingConsentCookieRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ConsentCookieResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly PendingConsentCookieRegistry $pendingCookieRegistry,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $cookie = $this->pendingCookieRegistry->consume();
        if (null === $cookie) {
            return;
        }

        $event->getResponse()->headers->setCookie($cookie);
    }
}
