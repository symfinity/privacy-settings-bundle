<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Attribute\StrictAttributeValidator;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class StrictAttributeValidatorTest extends TestCase
{
    public function testCanonicalAttributeIsAccepted(): void
    {
        $validator = new StrictAttributeValidator();
        $validator->validate(StrictAttributeValidator::CANONICAL_ATTRIBUTE);

        self::addToAssertionCount(1);
    }

    public function testForbiddenAliasDataCookiecategoryIsRejected(): void
    {
        $validator = new StrictAttributeValidator();

        $this->expectException(PrivacySettingsException::class);
        $this->expectExceptionMessage('PRIVACY_FORBIDDEN_ATTRIBUTE');
        $validator->validate('data-cookiecategory');
    }

    public function testForbiddenAliasDataCcIsRejected(): void
    {
        $validator = new StrictAttributeValidator();

        $this->expectException(PrivacySettingsException::class);
        $this->expectExceptionMessage('PRIVACY_FORBIDDEN_ATTRIBUTE');
        $validator->validate('data-cc');
    }
}
