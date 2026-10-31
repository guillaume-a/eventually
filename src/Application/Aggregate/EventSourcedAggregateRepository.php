<?php

namespace Codr\Eventually\Application\Aggregate;

use Codr\Eventually\Application\Event\DomainEventPublisherInterface;
use Codr\Eventually\Application\Event\RecordedEvent;
use Codr\Eventually\Application\Exception\AggregateNotFound;
use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Aggregate\AggregateRoot;
use Codr\Eventually\Domain\EventInterface;
use Codr\Eventually\Domain\EventStoreInterface;
use Codr\Eventually\Domain\Snapshot;
use Codr\Eventually\Domain\SnapshotStoreInterface;

use function array_keys;
use function array_map;
use function count;
use function intdiv;
use function sprintf;

final readonly class EventSourcedAggregateRepository implements AggregateRepositoryInterface
{
    public function __construct(
        private EventStoreInterface $eventStore,
        private SnapshotStoreInterface $snapshotStore,
        private DomainEventPublisherInterface $eventPublisher,
        private int $snapshotFrequency,
    ) {
    }

    /**
     * @template T of AggregateRoot
     *
     * @param class-string<T> $aggregateClass
     *
     * @return T
     */
    public function load(string $aggregateClass, AggregateId $id): AggregateRoot
    {
        $snapshot = $this->snapshotStore->load($aggregateClass, $id);

        $schemaRevision = AggregateDefinitionResolver::revisionOf($aggregateClass);

        if (null === $snapshot || $snapshot->schemaRevision < $schemaRevision) {
            $snapshot = new Snapshot(0, [], $schemaRevision);
        }

        $events = $this->eventStore->streamFor($aggregateClass, $id, $snapshot->version);

        if (0 === $snapshot->version && [] === $events) {
            throw new AggregateNotFound(sprintf('Aggregate "%s" of class "%s" not found.', $id->toString(), $aggregateClass));
        }

        return 0 === $snapshot->version
            ? $aggregateClass::reconstituteFrom($events)
            : $aggregateClass::reconstituteFromSnapshot($snapshot->state, $snapshot->version, $events);
    }

    public function save(AggregateRoot $aggregate): void
    {
        $id = $aggregate->id();
        $newEvents = $aggregate->pullRecordedEvents();
        $previousVersion = $aggregate->version() - count($newEvents);

        $receipt = $this->eventStore->append($aggregate::class, $id, $newEvents, $previousVersion);

        if ($this->snapshotFrequency > 0 && intdiv($aggregate->version(), $this->snapshotFrequency) > intdiv($previousVersion, $this->snapshotFrequency)) {
            $this->snapshotStore->save($aggregate::class, $id, $aggregate->version(), $aggregate->snapshotState(), AggregateDefinitionResolver::revisionOf($aggregate::class));
        }

        $this->eventPublisher->publish(array_map(
            static fn (EventInterface $event, int $index): RecordedEvent => new RecordedEvent($event, $previousVersion + $index + 1, $receipt->metadata, $receipt->recordedAt),
            $newEvents,
            array_keys($newEvents),
        ));
    }
}
