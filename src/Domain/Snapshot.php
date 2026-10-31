<?php

namespace Codr\Eventually\Domain;

final readonly class Snapshot
{
    /**
     * @param array<string, mixed> $state
     */
    public function __construct(
        public int $version,
        public array $state,
        public int $schemaRevision,
    ) {
    }
}
