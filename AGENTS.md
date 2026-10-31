# AGENTS.md

Guidance for AI agents working in `codr/eventually`.

## Layout

- `src/Domain`: aggregates, events, store ports, attributes, exceptions. Depends on nothing.
- `src/Application`: repository, buses, projector and view contracts. Depends on `Domain` only.
- `src/Testing`: in-memory helpers for consumers' tests.
- `tests/Unit`, `tests/Fixture`: PHPUnit tests and the `Counter` fixture aggregate.

## Rules

- Keep the core framework-agnostic: `trigger_deprecation()` is the only Symfony symbol allowed. Deptrac enforces layering.
- `#[Aggregate]` and `#[DomainEvent]` are persisted contracts: do not rename the attributes or change the meaning of `type` and `revision`.
- Public API is everything not `@internal`. Follow `UPGRADE.md` before removing or changing it.
- No prose comments in PHP.

## Gate

Run `composer check` before every commit.
