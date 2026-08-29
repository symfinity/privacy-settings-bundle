<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfinity\PrivacySettingsBundle\Event\ConsentDecisionEventPublisher;

final class ConsentDecisionEventPublisherTest extends TestCase
{
    public function testOnlyChangedStatesEmitEvents(): void
    {
        $publisher = new ConsentDecisionEventPublisher();
        $events = $publisher->publishChanges(
            'visitor-a',
            ['required' => true, 'analytics' => false],
            ['required' => true, 'analytics' => true],
            'compliance-test',
        );

        self::assertCount(1, $events);
        self::assertSame('analytics', $events[0]->categoryId);
        self::assertSame('compliance-test', $events[0]->reason);
    }
}
