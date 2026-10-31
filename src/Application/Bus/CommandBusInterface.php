<?php

namespace Codr\Eventually\Application\Bus;

interface CommandBusInterface
{
    public function dispatch(object $command): mixed;

    public function dispatchAfterCommit(object $command): void;
}
