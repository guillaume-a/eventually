# codr/eventually

Framework-agnostic event-sourcing and CQRS core (`Codr\Eventually\Domain`, `Codr\Eventually\Application`). It depends on nothing but PHP.

## Aggregates

- An aggregate extends `AggregateRoot` and carries `#[Aggregate('<type>', revision: n)]`. The type string is persisted, so keep it unique and stable.
- `AggregateRoot` declares an abstract `id()`, so `AggregateRepositoryInterface::save($aggregate)` takes no type or id, and `load(Class::class, $id)` takes no type.
- Behaviour methods only call `recordEvent(new SomethingHappened(...))`. A protected `apply()` mutates the private state from each event, on first record and on replay.
- `captureState()` returns the state as an array and `restoreState()` rebuilds it. Changing what `captureState()` returns needs a bump of `revision` in `#[Aggregate]` (default 1): older snapshots are ignored and the aggregate is rebuilt from its events.
- A class without `#[Aggregate]` cannot be stored: the store throws a `LogicException`.

## Events

- Every event implements `EventInterface` and carries `#[DomainEvent('<type>', revision: n)]` (default revision 1). The type is persisted: keep it unique and stable.
- A payload change needs a `revision` bump in the event's `#[DomainEvent]` and an `UpcasterInterface` implementation that lifts the previous revision one step. A missing upcaster fails explicitly on replay. Keep golden fixtures of every stored payload in your tests.
- Events never hold secrets or credentials: the event store is append-only and replayed forever.
- An event's `aggregateId()` must match its stream, otherwise the store rejects it.

## Repository and snapshots

- Handlers depend on `AggregateRepositoryInterface` (`load`, `save`), never on the event store ports.
- `EventSourcedAggregateRepository` loads from the latest snapshot plus the events after it, and saves by appending the recorded events.
- A snapshot is written when a save crosses a multiple of the snapshot frequency. `SnapshotStoreInterface::load` returns `null` when none exists.
- Reading events that do not start right after the snapshot version, or that have a hole, throws `EventStreamGap`.
- Optimistic concurrency: two writers on the same aggregate version make one of them fail with `ConcurrencyException`.
- `src/Testing` holds an in-memory aggregate repository: handler unit tests need no kernel and no database.

## Event publication

- The repository publishes each appended event through `DomainEventPublisherInterface`, in version order, wrapped in a `RecordedEvent` (event, version, metadata, `recordedAt`).
- Listeners declare `#[ListensTo(<EventClass>::class, 'method')]` (default method `__invoke`, repeatable). They run synchronously, inside the command transaction, so a throwing listener rolls the events back. They must be idempotent.

## Read models

- A read model is a derived, rebuildable view, written by a projector (`ProjectorInterface`: `handle(RecordedEvent)` and `reset()`), never by a command handler.
- Views are mutable `final` classes extending the abstract `View` (`id`, `position`, `createdAt`, `updatedAt`, `deletedAt`) and are stored through a view repository interface exposing only `find`, `paginate`, `save` and `clear`.
- `created_at` is the `recordedAt` of the first event and `updated_at` that of the last applied one, never the clock at projection time, so a rebuild gives identical rows.
- Put any arithmetic or rule shared by an aggregate and its projector in one domain value object that both call, so the aggregate and its view cannot drift.
- `paginate` takes a `PageRequest` (page, limit, default 20, max 100) and returns a `Page` with the total.

## Buses

- `CommandBusInterface` and `QueryBusInterface` are the only way in. A command handler implements `CommandHandlerInterface`, a query handler `QueryHandlerInterface`, each with one `__invoke`.
- Commands and queries are `final readonly` classes with public promoted properties and no getters.
- `CommandBusInterface::dispatchAfterCommit()` holds a command until the outer command succeeded, and drops it if the outer command fails.

## Rules for consumers

- Domain depends on nothing, Application on Domain only: keep the framework out of both, and wire scalar constructor arguments in the container configuration.
- Tag-by-interface goes in the framework adapter, never as an attribute on a domain interface.
