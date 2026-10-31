<?php

namespace Codr\Eventually\Application\View;

use DateTimeImmutable;

abstract class View implements ViewInterface
{
    public ?int $position = null;

    public ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        public string $id,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    public function touch(DateTimeImmutable $at): void
    {
        $this->updatedAt = $at;
    }
}
