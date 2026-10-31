<?php

namespace Codr\Eventually\Domain;

final readonly class EventMetadata
{
    public function __construct(
        public ?string $correlationId = null,
        public ?string $causationId = null,
        public ?string $actorId = null,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_filter([
            'correlationId' => $this->correlationId,
            'causationId' => $this->causationId,
            'actorId' => $this->actorId,
        ], static fn (?string $value): bool => null !== $value);
    }
}
