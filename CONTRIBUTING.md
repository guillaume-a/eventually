# Contributing

## Setup

```bash
composer install
composer check
```

`composer check` runs `composer validate`, phpstan (level max), php-cs-fixer (`@Symfony`, `@Symfony:risky`, applies fixes), deptrac and phpunit. All must pass.

## Running without local PHP

Use the official light image, with the project mounted and your own uid so files stay yours:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app php:8.5-cli-alpine vendor/bin/phpunit
```

Swap `vendor/bin/phpunit` for `vendor/bin/deptrac analyse` or `vendor/bin/phpstan analyse` to run the other gates. `composer install` needs `composer` in the container (not in the base image), so install dependencies on the host or with `composer:2`.

## Rules

- Keep the core framework-agnostic: no framework dependency beyond `symfony/deprecation-contracts` (`trigger_deprecation()`). Deptrac enforces the `Domain` → `Application` → `Testing` layering.
- No prose comments. Names, types and tests carry the intent. PHPDoc type tags that PHPStan needs are fine.
- English only for code, docs and commit messages.
- Commits are short lowercase `type: description`, imperative, no trailing period.
- Add a test with every behavior change. Fixtures live in `tests/Fixture`.
- A public API change follows `UPGRADE.md`: deprecate first, remove in the next major.
