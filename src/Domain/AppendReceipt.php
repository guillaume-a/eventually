<?php

namespace Codr\Eventually\Domain;

use DateTimeImmutable;

final readonly class AppendReceipt
{
    public function __construct(
        public EventMetadata $metadata,
        public DateTimeImmutable $recordedAt,
    ) {
    }
}
