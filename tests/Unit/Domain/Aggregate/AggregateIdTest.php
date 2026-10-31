<?php

namespace Codr\Eventually\Tests\Unit\Domain\Aggregate;

use Codr\Eventually\Domain\Aggregate\AggregateId;
use PHPUnit\Framework\TestCase;

final class AggregateIdTest extends TestCase
{
    public function testFromStringAndToStringRoundTrip(): void
    {
        $id = AggregateId::fromString('018f1e3a-1234-7abc-8def-0123456789ab');

        self::assertSame('018f1e3a-1234-7abc-8def-0123456789ab', $id->toString());
    }

    public function testEqualsReturnsTrueForSameValue(): void
    {
        $id1 = AggregateId::fromString('same-value');
        $id2 = AggregateId::fromString('same-value');

        self::assertTrue($id1->equals($id2));
    }

    public function testEqualsReturnsFalseForDifferentValue(): void
    {
        $id1 = AggregateId::fromString('value-a');
        $id2 = AggregateId::fromString('value-b');

        self::assertFalse($id1->equals($id2));
    }
}
