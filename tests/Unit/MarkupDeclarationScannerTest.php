<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Attribute\MarkupDeclarationScanner;

final class MarkupDeclarationScannerTest extends TestCase
{
    public function testScannerReportsAcceptedAndRejectedAttributes(): void
    {
        $scanner = new MarkupDeclarationScanner();
        $result = $scanner->scan('<div data-privacy-category="analytics" data-cookiecategory="legacy"></div>');

        self::assertSame([
            ['attribute' => 'data-privacy-category', 'status' => 'accepted'],
            ['attribute' => 'data-cookiecategory', 'status' => 'rejected'],
        ], $result);
    }
}
