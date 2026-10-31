<?php

namespace Codr\Eventually\Application\Event;

use Codr\Eventually\Domain\EventInterface;
use Codr\Eventually\Domain\EventMetadata;
use DateTimeImmutable;

final readonly class RecordedEvent
{
    public function __construct(
        public EventInterface $event,
        public int $version,
        public EventMetadata $metadata,
        public DateTimeImmutable $recordedAt,
    ) {
    }
}
