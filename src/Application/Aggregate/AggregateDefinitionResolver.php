<?php

namespace Codr\Eventually\Application\Aggregate;

use Codr\Eventually\Domain\Attribute\Aggregate;
use LogicException;
use ReflectionClass;

use function sprintf;

final class AggregateDefinitionResolver
{
    /** @var array<class-string, Aggregate> */
    private static array $definitions = [];

    /**
     * @param class-string $aggregateClass
     */
    public static function typeOf(string $aggregateClass): string
    {
        return self::definitionOf($aggregateClass)->type;
    }

    /**
     * @param class-string $aggregateClass
     */
    public static function revisionOf(string $aggregateClass): int
    {
        return self::definitionOf($aggregateClass)->revision;
    }

    /**
     * @param class-string $aggregateClass
     */
    private static function definitionOf(string $aggregateClass): Aggregate
    {
        if (!isset(self::$definitions[$aggregateClass])) {
            $attributes = new ReflectionClass($aggregateClass)->getAttributes(Aggregate::class);

            if ([] === $attributes) {
                throw new LogicException(sprintf('Class "%s" must carry the #[%s] attribute.', $aggregateClass, Aggregate::class));
            }

            self::$definitions[$aggregateClass] = $attributes[0]->newInstance();
        }

        return self::$definitions[$aggregateClass];
    }
}
