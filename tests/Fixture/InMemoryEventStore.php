<?php

namespace Codr\Eventually\Tests\Fixture;

use Codr\Eventually\Application\Aggregate\AggregateDefinitionResolver;
use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\AppendReceipt;
use Codr\Eventually\Domain\EventInterface;
use Codr\Eventually\Domain\EventMetadata;
use Codr\Eventually\Domain\EventStoreInterface;
use Codr\Eventually\Domain\Exception\ConcurrencyException;
use DateTimeImmutable;

use function array_keys;
use function array_map;
use function array_slice;
use function count;

final class InMemoryEventStore implements EventStoreInterface
{
    /** @var array<string, array<string, list<EventInterface>>> */
    private array $streams = [];

    public function append(string $aggregateClass, AggregateId $aggregateId, array $events, int $expectedVersion): AppendReceipt
    {
        $aggregateType = AggregateDefinitionResolver::typeOf($aggregateClass);
        $key = $aggregateId->toString();
        $existing = $this->streams[$aggregateType][$key] ?? [];

        if (count($existing) !== $expectedVersion) {
            throw new ConcurrencyException('Concurrent write detected.');
        }

        $this->streams[$aggregateType][$key] = [...$existing, ...$events];

        return new AppendReceipt(new EventMetadata(), new DateTimeImmutable());
    }

    public function streamFor(string $aggregateClass, AggregateId $aggregateId, int $fromVersion = 0): array
    {
        $aggregateType = AggregateDefinitionResolver::typeOf($aggregateClass);
        $events = $this->streams[$aggregateType][$aggregateId->toString()] ?? [];

        return array_slice($events, $fromVersion);
    }

    public function aggregateIdsFor(string $aggregateClass): array
    {
        $aggregateType = AggregateDefinitionResolver::typeOf($aggregateClass);

        return array_map(
            static fn (string $id): AggregateId => AggregateId::fromString($id),
            array_keys($this->streams[$aggregateType] ?? []),
        );
    }
}
