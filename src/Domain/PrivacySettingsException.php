<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Domain;

final class PrivacySettingsException extends \InvalidArgumentException
{
    public static function invalidCategoryId(string $id): self
    {
        return new self(sprintf('PRIVACY_INVALID_CATEGORY_ID: "%s" is not a valid category id.', $id));
    }

    public static function emptyCategoryLabel(string $id): self
    {
        return new self(sprintf('PRIVACY_EMPTY_CATEGORY_LABEL: category "%s" must define a label.', $id));
    }

    public static function duplicateCategoryId(string $id): self
    {
        return new self(sprintf('PRIVACY_DUPLICATE_CATEGORY_ID: duplicate category "%s".', $id));
    }

    public static function unknownCategoryState(string $state): self
    {
        return new self(sprintf('PRIVACY_UNKNOWN_CATEGORY_STATE: "%s" is not allowed.', $state));
    }

    public static function forbiddenAttribute(string $attribute): self
    {
        return new self(sprintf('PRIVACY_FORBIDDEN_ATTRIBUTE: "%s" is forbidden.', $attribute));
    }

    public static function unsupportedAttribute(string $attribute): self
    {
        return new self(sprintf('PRIVACY_UNSUPPORTED_ATTRIBUTE: "%s" is unsupported.', $attribute));
    }

    public static function unknownPreferenceCategory(string $id): self
    {
        return new self(sprintf('PRIVACY_UNKNOWN_PREFERENCE_CATEGORY: "%s" is not configured.', $id));
    }

    public static function requiredCategoryDisabled(string $id): self
    {
        return new self(sprintf('PRIVACY_REQUIRED_CATEGORY_DISABLED: "%s" cannot be disabled.', $id));
    }
}
