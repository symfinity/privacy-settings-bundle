<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Unit\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;
use Symfinity\PrivacySettingsBundle\Media\EmbedUrlResolver;

final class EmbedUrlResolverTest extends TestCase
{
    private EmbedUrlResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new EmbedUrlResolver();
    }

    public function testYoutubeEmbedUrl(): void
    {
        self::assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            $this->resolver->resolve(EmbedUrlResolver::PROVIDER_YOUTUBE, videoId: 'dQw4w9WgXcQ'),
        );
    }

    public function testVimeoEmbedUrl(): void
    {
        self::assertSame(
            'https://player.vimeo.com/video/76979871',
            $this->resolver->resolve(EmbedUrlResolver::PROVIDER_VIMEO, videoId: '76979871'),
        );
    }

    public function testGoogleMapsEmbedUrl(): void
    {
        self::assertSame(
            'https://www.google.com/maps?q=Berlin%2C%20Germany&output=embed',
            $this->resolver->resolve(EmbedUrlResolver::PROVIDER_GOOGLE_MAPS, mapQuery: 'Berlin, Germany'),
        );
    }

    public function testGenericHttpsEmbedUrl(): void
    {
        self::assertSame(
            'https://example.com/embed/widget',
            $this->resolver->resolve(EmbedUrlResolver::PROVIDER_GENERIC, embedUrl: 'https://example.com/embed/widget'),
        );
    }

    #[DataProvider('invalidGenericUrlProvider')]
    public function testGenericRejectsNonHttps(string $url): void
    {
        $this->expectException(PrivacySettingsException::class);
        $this->resolver->resolve(EmbedUrlResolver::PROVIDER_GENERIC, embedUrl: $url);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidGenericUrlProvider(): iterable
    {
        yield 'http' => ['http://example.com/embed'];
        yield 'relative' => ['/embed/widget'];
        yield 'empty' => [''];
    }
}
