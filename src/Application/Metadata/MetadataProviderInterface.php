<?php

namespace Codr\Eventually\Application\Metadata;

use Codr\Eventually\Domain\EventMetadata;

interface MetadataProviderInterface
{
    public function current(): EventMetadata;
}
