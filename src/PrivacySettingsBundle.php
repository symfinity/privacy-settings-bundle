<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle;

use Symfinity\PrivacySettingsBundle\DependencyInjection\PrivacySettingsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PrivacySettingsBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PrivacySettingsExtension();
    }
}
