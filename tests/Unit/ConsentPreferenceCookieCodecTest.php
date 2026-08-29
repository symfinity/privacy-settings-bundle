<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Consent\ConsentPreferenceCookieCodec;

final class ConsentPreferenceCookieCodecTest extends TestCase
{
    public function testEncodeDecodeRoundTrip(): void
    {
        $codec = new ConsentPreferenceCookieCodec('secret', '1');
        $subjects = [
            'visitor' => [
                'c' => ['required' => true, 'analytics' => true],
                'u' => '2026-06-28T15:00:00+00:00',
                's' => 'quick_accept',
            ],
        ];

        $encoded = $codec->encode($subjects);
        self::assertSame($subjects, $codec->decode($encoded));
    }

    public function testTamperedPayloadIsRejected(): void
    {
        $codec = new ConsentPreferenceCookieCodec('secret', '1');
        $encoded = $codec->encode([
            'visitor' => [
                'c' => ['required' => true, 'analytics' => false],
                'u' => '2026-06-28T15:00:00+00:00',
                's' => 'quick_reject',
            ],
        ]);

        self::assertNull($codec->decode($encoded.'x'));
    }

    public function testPolicyVersionMismatchIsRejected(): void
    {
        $codec = new ConsentPreferenceCookieCodec('secret', '1');
        $encoded = $codec->encode([
            'visitor' => [
                'c' => ['required' => true, 'analytics' => true],
                'u' => '2026-06-28T15:00:00+00:00',
                's' => 'quick_accept',
            ],
        ]);

        $otherVersion = new ConsentPreferenceCookieCodec('secret', '2');
        self::assertNull($otherVersion->decode($encoded));
    }
}
