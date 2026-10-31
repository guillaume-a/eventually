<?php

namespace Codr\Eventually\Tests\Unit\Application\Aggregate;

use Codr\Eventually\Application\Aggregate\EventSourcedAggregateRepository;
use Codr\Eventually\Application\Exception\AggregateNotFound;
use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Snapshot;
use Codr\Eventually\Tests\Fixture\Counter;
use Codr\Eventually\Tests\Fixture\InMemoryEventStore;
use Codr\Eventually\Tests\Fixture\InMemorySnapshotStore;
use Codr\Eventually\Tests\Fixture\NullDomainEventPublisher;
use Codr\Eventually\Tests\Fixture\RevisedCounter;
use PHPUnit\Framework\TestCase;

final class EventSourcedAggregateRepositoryTest extends TestCase
{
    public function testSaveWritesASnapshotExactlyEverySnapshotFrequencyVersions(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 3);

        $id = AggregateId::fromString('counter-1');
        $counter = Counter::start($id);

        $repository->save($counter);
        self::assertNull($snapshotStore->load(Counter::class, $id));

        $counter->increment();
        $repository->save($counter);
        self::assertNull(self::snapshotVersion($snapshotStore, $id));

        $counter->increment();
        $repository->save($counter);

        self::assertSame(3, self::snapshotVersion($snapshotStore, $id));
    }

    public function testLoadReconstitutesFromSnapshotPlusRemainingEvents(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 2);

        $id = AggregateId::fromString('counter-1');
        $counter = Counter::start($id);
        $repository->save($counter);

        $counter->increment();
        $repository->save($counter);
        self::assertInstanceOf(Snapshot::class, $snapshotStore->load(Counter::class, $id));

        $counter->increment();
        $repository->save($counter);

        $loaded = $repository->load(Counter::class, $id);

        self::assertSame(3, $loaded->version());
        self::assertSame(2, $loaded->count());
    }

    public function testLoadIgnoresASnapshotWithAnOutdatedSchemaRevisionAndRebuildsFromEvents(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 3);

        $id = AggregateId::fromString('revised-1');
        $aggregate = RevisedCounter::start($id);
        $aggregate->touch();
        $repository->save($aggregate);

        $snapshotStore->save(RevisedCounter::class, $id, 2, ['count' => 99], 1);

        $loaded = $repository->load(RevisedCounter::class, $id);

        self::assertSame(2, $loaded->version());
        self::assertSame(2, $loaded->count);
    }

    public function testSaveStoresTheSchemaRevisionDeclaredByTheAttribute(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 1);

        $id = AggregateId::fromString('revised-1');
        $repository->save(RevisedCounter::start($id));

        self::assertSame(2, $snapshotStore->load(RevisedCounter::class, $id)?->schemaRevision);
    }

    public function testSaveSnapshotsWhenASingleSaveCrossesABoundary(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 20);

        $id = AggregateId::fromString('counter-1');
        $counter = Counter::start($id);
        for ($i = 1; $i < 19; ++$i) {
            $counter->increment();
        }
        $repository->save($counter);
        self::assertSame(19, $counter->version());
        self::assertNull(self::snapshotVersion($snapshotStore, $id));

        $counter->increment();
        $counter->increment();
        $counter->increment();
        $repository->save($counter);

        self::assertSame(22, $counter->version());
        self::assertSame(22, self::snapshotVersion($snapshotStore, $id));
    }

    public function testSaveWithoutNewEventsWritesNoSnapshot(): void
    {
        $snapshotStore = new InMemorySnapshotStore();
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), $snapshotStore, new NullDomainEventPublisher(), 1);

        $id = AggregateId::fromString('counter-1');
        $counter = Counter::start($id);
        $repository->save($counter);
        $snapshotStore->forget(Counter::class, $id);

        $repository->save($counter);

        self::assertNull(self::snapshotVersion($snapshotStore, $id));
    }

    public function testLoadThrowsWhenNoSnapshotAndNoEventsExist(): void
    {
        $repository = new EventSourcedAggregateRepository(new InMemoryEventStore(), new InMemorySnapshotStore(), new NullDomainEventPublisher(), 20);

        $this->expectException(AggregateNotFound::class);

        $repository->load(Counter::class, AggregateId::fromString('missing'));
    }

    private static function snapshotVersion(InMemorySnapshotStore $store, AggregateId $id): ?int
    {
        return $store->load(Counter::class, $id)?->version;
    }
}
