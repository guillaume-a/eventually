<?php

namespace Codr\Eventually\Application\Bus;

interface QueryBusInterface
{
    public function ask(object $query): mixed;
}
