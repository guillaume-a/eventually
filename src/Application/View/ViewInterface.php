<?php

namespace Codr\Eventually\Application\View;

use DateTimeImmutable;

interface ViewInterface
{
    public string $id { get; }

    public ?int $position { get; }

    public DateTimeImmutable $createdAt { get; }

    public DateTimeImmutable $updatedAt { get; }

    public ?DateTimeImmutable $deletedAt { get; }
}
