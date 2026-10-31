# Upgrade

## Backward compatibility promise

`codr/eventually` follows semantic versioning and the [Symfony backward compatibility promise](https://symfony.com/doc/current/contributing/code/bc.html). Public API is every class, interface, method and attribute that is not marked `@internal`, and every non-`final` class may be extended only through its documented extension points.

`#[Aggregate]` and `#[DomainEvent]` are public API: their `type` and `revision` are persisted, so breaking them breaks stored streams.

## Deprecation protocol

- A deprecated element carries `@deprecated since codr/eventually x.y, use <replacement> instead`.
- It calls `trigger_deprecation('codr/eventually', 'x.y', '...')` at runtime.
- It stays for one minor release and is removed in the next major release.
- `bc-check` (`roave/backward-compatibility-check`) runs against the last tag before each release.

## From 0.x

No upgrade steps yet. Breaking changes are allowed between 0.x minors and are listed here.
