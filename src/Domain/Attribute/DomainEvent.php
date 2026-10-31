<?php

namespace Codr\Eventually\Domain\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class DomainEvent
{
    public function __construct(
        public string $type,
        public int $revision = 1,
    ) {
    }
}
