<?php

namespace Codr\Eventually\Tests\Fixture;

use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Attribute\DomainEvent;
use Codr\Eventually\Domain\EventInterface;

#[DomainEvent('counter_was_started')]
final readonly class CounterWasStarted implements EventInterface
{
    public function __construct(private AggregateId $id)
    {
    }

    public function aggregateId(): AggregateId
    {
        return $this->id;
    }
}
