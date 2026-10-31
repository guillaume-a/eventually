<?php

namespace Codr\Eventually\Application\Attribute;

use Attribute;
use Codr\Eventually\Domain\EventInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class ListensTo
{
    /**
     * @param class-string<EventInterface> $event
     */
    public function __construct(
        public string $event,
        public string $method = '__invoke',
        public int $priority = 0,
    ) {
    }
}
