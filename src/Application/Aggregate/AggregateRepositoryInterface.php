<?php

namespace Codr\Eventually\Application\Aggregate;

use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Aggregate\AggregateRoot;

interface AggregateRepositoryInterface
{
    /**
     * @template T of AggregateRoot
     *
     * @param class-string<T> $aggregateClass
     *
     * @return T
     */
    public function load(string $aggregateClass, AggregateId $id): AggregateRoot;

    public function save(AggregateRoot $aggregate): void;
}
