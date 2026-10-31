<?php

namespace Codr\Eventually\Application\ReadModel;

use Codr\Eventually\Application\Event\RecordedEvent;

interface ProjectorInterface
{
    public function handle(RecordedEvent $recorded): void;

    public function reset(): void;
}
