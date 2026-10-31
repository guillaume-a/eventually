<?php

namespace Codr\Eventually\Tests\Unit\Application\Aggregate;

use Codr\Eventually\Application\Aggregate\AggregateDefinitionResolver;
use Codr\Eventually\Domain\Attribute\Aggregate;
use Codr\Eventually\Tests\Fixture\Counter;
use Codr\Eventually\Tests\Fixture\RevisedCounter;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AggregateDefinitionResolverTest extends TestCase
{
    public function testItReadsTypeAndRevision(): void
    {
        self::assertSame('counter', AggregateDefinitionResolver::typeOf(Counter::class));
        self::assertSame(1, AggregateDefinitionResolver::revisionOf(Counter::class));
        self::assertSame('revised_counter', AggregateDefinitionResolver::typeOf(RevisedCounter::class));
        self::assertSame(2, AggregateDefinitionResolver::revisionOf(RevisedCounter::class));
    }

    public function testItThrowsWhenTheAttributeIsMissing(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(stdClass::class);
        $this->expectExceptionMessage(Aggregate::class);

        AggregateDefinitionResolver::typeOf(stdClass::class);
    }
}
