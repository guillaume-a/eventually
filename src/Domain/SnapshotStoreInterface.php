<?php

namespace Codr\Eventually\Domain;

use Codr\Eventually\Domain\Aggregate\AggregateId;

interface SnapshotStoreInterface
{
    /**
     * @param class-string $aggregateClass
     */
    public function load(string $aggregateClass, AggregateId $id): ?Snapshot;

    /**
     * @param class-string         $aggregateClass
     * @param array<string, mixed> $state
     */
    public function save(string $aggregateClass, AggregateId $id, int $version, array $state, int $schemaRevision): void;
}
