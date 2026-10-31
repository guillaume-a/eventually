<?php

namespace Codr\Eventually\Application\ReadModel;

final readonly class PageRequest
{
    public const int DEFAULT_LIMIT = 20;
    public const int MAX_LIMIT = 100;

    public int $page;
    public int $limit;

    public function __construct(int $page = 1, int $limit = self::DEFAULT_LIMIT)
    {
        $this->page = max(1, $page);
        $this->limit = min(self::MAX_LIMIT, max(1, $limit));
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
