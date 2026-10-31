<?php

namespace Codr\Eventually\Domain\Aggregate;

use Codr\Eventually\Domain\EventInterface;

abstract class AggregateRoot
{
    /** @var list<EventInterface> */
    private array $recordedEvents = [];

    private int $version = 0;

    /**
     * @return list<EventInterface>
     */
    public function pullRecordedEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function version(): int
    {
        return $this->version;
    }

    /**
     * @param iterable<EventInterface> $events
     */
    public static function reconstituteFrom(iterable $events): static
    {
        // @phpstan-ignore-next-line new.static
        $aggregate = new static();

        foreach ($events as $event) {
            $aggregate->apply($event);
            ++$aggregate->version;
        }

        return $aggregate;
    }

    /**
     * @param array<string, mixed>     $state
     * @param iterable<EventInterface> $remainingEvents
     */
    public static function reconstituteFromSnapshot(array $state, int $version, iterable $remainingEvents): static
    {
        // @phpstan-ignore-next-line new.static
        $aggregate = new static();
        $aggregate->restoreState($state);
        $aggregate->version = $version;

        foreach ($remainingEvents as $event) {
            $aggregate->apply($event);
            ++$aggregate->version;
        }

        return $aggregate;
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshotState(): array
    {
        return $this->captureState();
    }

    protected function recordEvent(EventInterface $event): void
    {
        $this->apply($event);
        ++$this->version;
        $this->recordedEvents[] = $event;
    }

    abstract public function id(): AggregateId;

    abstract protected function apply(EventInterface $event): void;

    /**
     * @return array<string, mixed>
     */
    abstract protected function captureState(): array;

    /**
     * @param array<string, mixed> $state
     */
    abstract protected function restoreState(array $state): void;
}
