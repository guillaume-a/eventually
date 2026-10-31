<?php

namespace Codr\Eventually\Tests\Fixture;

use Codr\Eventually\Application\Aggregate\AggregateDefinitionResolver;
use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Snapshot;
use Codr\Eventually\Domain\SnapshotStoreInterface;

final class InMemorySnapshotStore implements SnapshotStoreInterface
{
    /** @var array<string, array<string, Snapshot>> */
    private array $snapshots = [];

    public function load(string $aggregateClass, AggregateId $id): ?Snapshot
    {
        return $this->snapshots[AggregateDefinitionResolver::typeOf($aggregateClass)][$id->toString()] ?? null;
    }

    /**
     * @param class-string $aggregateClass
     */
    public function forget(string $aggregateClass, AggregateId $id): void
    {
        unset($this->snapshots[AggregateDefinitionResolver::typeOf($aggregateClass)][$id->toString()]);
    }

    public function save(string $aggregateClass, AggregateId $id, int $version, array $state, int $schemaRevision): void
    {
        $this->snapshots[AggregateDefinitionResolver::typeOf($aggregateClass)][$id->toString()] = new Snapshot($version, $state, $schemaRevision);
    }
}
