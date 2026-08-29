<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Domain;

final class PrivacyCategory
{
    public const DEFAULT_REQUIRED = 'required';
    public const DEFAULT_ENABLED = 'enabled';
    public const DEFAULT_DISABLED = 'disabled';

    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly string $defaultState,
        public readonly string $description = '',
    ) {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id)) {
            throw PrivacySettingsException::invalidCategoryId($id);
        }

        if ('' === trim($label)) {
            throw PrivacySettingsException::emptyCategoryLabel($id);
        }

        if (!in_array($defaultState, [
            self::DEFAULT_REQUIRED,
            self::DEFAULT_ENABLED,
            self::DEFAULT_DISABLED,
        ], true)) {
            throw PrivacySettingsException::unknownCategoryState($defaultState);
        }
    }

    public function isRequired(): bool
    {
        return self::DEFAULT_REQUIRED === $this->defaultState;
    }
}
