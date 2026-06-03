<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class PrivacyCategoryTest extends TestCase
{
    public function testInvalidCategoryIdIsRejected(): void
    {
        $this->expectException(PrivacySettingsException::class);
        $this->expectExceptionMessage('PRIVACY_INVALID_CATEGORY_ID');

        new PrivacyCategory('Analytics!', 'Analytics', PrivacyCategory::DEFAULT_DISABLED);
    }

    public function testUnknownDefaultStateIsRejected(): void
    {
        $this->expectException(PrivacySettingsException::class);
        $this->expectExceptionMessage('PRIVACY_UNKNOWN_CATEGORY_STATE');

        new PrivacyCategory('analytics', 'Analytics', 'maybe');
    }
}
