<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;
use Symfinity\PrivacySettingsBundle\Symfony\CategoryModelNormalizer;

final class CategoryModelNormalizerTest extends TestCase
{
    public function testDuplicateCategoryIdFailsDeterministically(): void
    {
        $normalizer = new CategoryModelNormalizer();

        $this->expectException(PrivacySettingsException::class);
        $this->expectExceptionMessage('PRIVACY_DUPLICATE_CATEGORY_ID');

        $normalizer->normalize([
            ['id' => 'analytics', 'label' => 'Analytics', 'default_state' => 'disabled'],
            ['id' => 'analytics', 'label' => 'Duplicate', 'default_state' => 'enabled'],
        ]);
    }

    public function testValidConfigurationNormalizes(): void
    {
        $normalizer = new CategoryModelNormalizer();
        $categories = $normalizer->normalize([
            ['id' => 'required', 'label' => 'Required', 'default_state' => 'required'],
            ['id' => 'analytics', 'label' => 'Analytics', 'default_state' => 'disabled'],
        ]);

        self::assertCount(2, $categories);
        self::assertSame('required', $categories[0]->id);
    }
}
