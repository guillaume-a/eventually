<?php

namespace Codr\Eventually\Domain;

use Codr\Eventually\Domain\Aggregate\AggregateId;

interface EventStoreInterface
{
    /**
     * @param class-string         $aggregateClass
     * @param list<EventInterface> $events
     */
    public function append(string $aggregateClass, AggregateId $aggregateId, array $events, int $expectedVersion): AppendReceipt;

    /**
     * @param class-string $aggregateClass
     *
     * @return list<EventInterface>
     */
    public function streamFor(string $aggregateClass, AggregateId $aggregateId, int $fromVersion = 0): array;

    /**
     * @param class-string $aggregateClass
     *
     * @return list<AggregateId>
     */
    public function aggregateIdsFor(string $aggregateClass): array;
}
