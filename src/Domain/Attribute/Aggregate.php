<?php

namespace Codr\Eventually\Domain\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Aggregate
{
    public function __construct(
        public string $type,
        public int $revision = 1,
    ) {
    }
}
