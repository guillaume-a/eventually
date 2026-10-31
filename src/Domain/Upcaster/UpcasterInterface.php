<?php

namespace Codr\Eventually\Domain\Upcaster;

interface UpcasterInterface
{
    public function eventType(): string;

    public function fromRevision(): int;

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function upcast(array $payload): array;
}
