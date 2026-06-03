<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Attribute;

use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class TemplateAttributeContract
{
    public function __construct(
        private readonly StrictAttributeValidator $validator = new StrictAttributeValidator(),
    ) {
    }

    /**
     * @param array<string, string> $attributes
     */
    public function extractCategoryId(array $attributes): string
    {
        foreach ($attributes as $name => $value) {
            $this->validator->validate($name);

            if (StrictAttributeValidator::CANONICAL_ATTRIBUTE === $name) {
                if ('' === trim($value)) {
                    throw PrivacySettingsException::unsupportedAttribute($name);
                }

                return $value;
            }
        }

        throw PrivacySettingsException::unsupportedAttribute('missing:data-privacy-category');
    }
}
