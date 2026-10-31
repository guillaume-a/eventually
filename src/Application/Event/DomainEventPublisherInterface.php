<?php

namespace Codr\Eventually\Application\Event;

interface DomainEventPublisherInterface
{
    /**
     * @param list<RecordedEvent> $events
     */
    public function publish(array $events): void;
}
