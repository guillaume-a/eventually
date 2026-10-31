<?php

namespace Codr\Eventually\Tests\Fixture;

use Codr\Eventually\Application\Event\DomainEventPublisherInterface;

final class NullDomainEventPublisher implements DomainEventPublisherInterface
{
    public function publish(array $events): void
    {
    }
}
