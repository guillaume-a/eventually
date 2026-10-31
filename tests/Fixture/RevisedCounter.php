<?php

namespace Codr\Eventually\Tests\Fixture;

use Codr\Eventually\Domain\Aggregate\AggregateId;
use Codr\Eventually\Domain\Aggregate\AggregateRoot;
use Codr\Eventually\Domain\Attribute\Aggregate;
use Codr\Eventually\Domain\EventInterface;
use LogicException;

use function is_int;

#[Aggregate('revised_counter', revision: 2)]
final class RevisedCounter extends AggregateRoot
{
    public int $count = 0;
    private ?AggregateId $id = null;

    public static function start(AggregateId $id): self
    {
        $counter = new self();
        $counter->id = $id;
        $counter->recordEvent(new CounterWasStarted($id));

        return $counter;
    }

    public function touch(): void
    {
        $this->recordEvent(new CounterWasIncremented($this->id()));
    }

    public function id(): AggregateId
    {
        return $this->id ?? throw new LogicException('Not started.');
    }

    protected function apply(EventInterface $event): void
    {
        ++$this->count;
    }

    protected function captureState(): array
    {
        return ['count' => $this->count];
    }

    protected function restoreState(array $state): void
    {
        $this->count = is_int($state['count']) ? $state['count'] : 0;
    }
}
