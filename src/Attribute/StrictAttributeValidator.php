<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Attribute;

use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class StrictAttributeValidator
{
    public const CANONICAL_ATTRIBUTE = 'data-privacy-category';
    public const FORBIDDEN_ALIASES = [
        'data-cookiecategory',
        'data-cc',
    ];

    public function validate(string $attribute): void
    {
        if (in_array($attribute, self::FORBIDDEN_ALIASES, true)) {
            throw PrivacySettingsException::forbiddenAttribute($attribute);
        }

        if (self::CANONICAL_ATTRIBUTE !== $attribute) {
            throw PrivacySettingsException::unsupportedAttribute($attribute);
        }
    }
}
