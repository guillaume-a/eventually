<?php

namespace Codr\Eventually\Application\Event;

interface RecordedEventReaderInterface
{
    /**
     * @return iterable<RecordedEvent>
     */
    public function readAll(): iterable;
}
