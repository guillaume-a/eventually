<?php

namespace Codr\Eventually\Domain;

use Codr\Eventually\Domain\Aggregate\AggregateId;

interface EventInterface
{
    public function aggregateId(): AggregateId;
}
