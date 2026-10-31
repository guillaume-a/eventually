<?php

namespace Codr\Eventually\Testing;

use Codr\Eventually\Application\Aggregate\AggregateRepositoryInterface;
use Codr\Eventually\Application\Exception\AggregateNotFound;
use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Aggregate\AggregateRoot;
use Codr\Eventually\Domain\EventInterface;

use function array_merge;
use function sprintf;

final class InMemoryAggregateRepository implements AggregateRepositoryInterface
{
    /** @var array<string, list<EventInterface>> */
    private array $streams = [];

    /**
     * @template T of AggregateRoot
     *
     * @param class-string<T> $aggregateClass
     *
     * @return T
     */
    public function load(string $aggregateClass, AggregateId $id): AggregateRoot
    {
        $key = self::key($aggregateClass, $id);

        if (!isset($this->streams[$key])) {
            throw new AggregateNotFound(sprintf('Aggregate "%s" of class "%s" not found.', $id->toString(), $aggregateClass));
        }

        return $aggregateClass::reconstituteFrom($this->streams[$key]);
    }

    public function save(AggregateRoot $aggregate): void
    {
        $key = self::key($aggregate::class, $aggregate->id());
        $this->streams[$key] = array_merge($this->streams[$key] ?? [], $aggregate->pullRecordedEvents());
    }

    /**
     * @param class-string<AggregateRoot> $aggregateClass
     *
     * @return list<EventInterface>
     */
    public function eventsOf(string $aggregateClass, AggregateId $id): array
    {
        return $this->streams[self::key($aggregateClass, $id)] ?? [];
    }

    private static function key(string $aggregateClass, AggregateId $id): string
    {
        return $aggregateClass.'#'.$id->toString();
    }
}
