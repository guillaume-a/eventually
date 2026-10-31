# codr/eventually

Framework-agnostic event sourcing and CQRS core for PHP 8.5+. It holds the contracts and the pure logic; storage and framework wiring live in sibling packages.

## What is inside

- `Codr\Eventually\Domain`: `AggregateRoot`, `AggregateId`, `EventInterface`, `EventMetadata`, the `#[Aggregate]` and `#[DomainEvent]` attributes, `EventStoreInterface`, `SnapshotStoreInterface`, `Snapshot`, `UpcasterInterface` and the concurrency and stream exceptions.
- `Codr\Eventually\Application`: `EventSourcedAggregateRepository` (snapshot, replay and event publication), command and query bus contracts, `ProjectorInterface`, the `#[ListensTo]` attribute, `View`, `Page` and `PageRequest`, `RecordedEvent`.
- `Codr\Eventually\Testing`: `InMemoryAggregateRepository` for handler unit tests with no kernel.

The only runtime dependency is `symfony/deprecation-contracts`. Nothing here imports Symfony, Doctrine or Money.

## Install

```bash
composer require codr/eventually
```

## Define an aggregate

```php
#[Aggregate('counter', revision: 1)]
final class Counter extends AggregateRoot
{
    // recordEvent() applies and records, apply() mutates state,
    // captureState() and restoreState() feed snapshots.
}
```

`#[Aggregate]` and `#[DomainEvent]` persist a type string, never a class name, so renaming a class does not touch stored data. Changing a payload or a snapshot shape requires a `revision` bump.

## Related packages

- `codr/eventually-doctrine`: event store, snapshot store and view repositories on Doctrine.
- `codr/eventually-bundle`: Symfony bundle wiring the buses, the event registry and the rebuild command.

## Quality

```bash
composer check      # validate, phpstan, php-cs-fixer, deptrac, phpunit
composer bc-check   # backward compatibility against the last tag
```

## AI docs

`ai/memory/` holds the rules of this package for AI coding agents. [`codr/eventually-bundle`](../eventually-bundle) copies it into your project with `eventually:ai:install`.

## License

MIT, see `LICENSE`.
