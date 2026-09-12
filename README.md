# Kumwe Business Definition

[![Packagist version](https://img.shields.io/packagist/v/kumwe/business-definition)](https://packagist.org/packages/kumwe/business-definition)
[![CI](https://github.com/kumwe/business-definition/actions/workflows/ci.yml/badge.svg?branch=main&event=push)](https://github.com/kumwe/business-definition/actions/workflows/ci.yml)
[![PHP requirement](https://img.shields.io/packagist/php-v/kumwe/business-definition)](composer.json)
[![License](https://img.shields.io/packagist/l/kumwe/business-definition)](LICENSE)

Immutable business definitions, typed bounded formula ASTs, canonical profiles,
structural validation, compatibility plans and portable registries under `Kumwe\BusinessDefinition`.

## Installation

Requires 64-bit PHP 8.5 with `ext-mbstring`. Install an exact pre-1.0 release:

```sh
composer require kumwe/business-definition:0.1.2
```

Composer declares the exact Kumwe dependency versions. Published versions are linked
from the badge; default-branch CI reports package checks, not Core integration or
independent release verification.

## Usage and Core contract

Construct `BusinessDefinitionValidator($fieldTypes, $fieldConfigurationAdmission)`
with a host-owned `FieldConfigurationAdmission` implementation, or register the
package's `ConfigProvider` and bind that mandatory port before resolving a validator.
It must enforce the selected SDK presentation profile; there is no permissive default.

The [standalone definition example](examples/definition.php) constructs and validates
a definition, checks canonical bytes and compatibility, and demonstrates an explicit
fixture admission policy. Run it from a source checkout with `composer examples`.
The [container example](examples/container.php) shows actual Laminas composition.

Core retains publication, persistence, trusted generation selection, authorization,
lifecycle, invalidation and recovery. This package describes formula semantics; it
does not execute PHP formulas. Runtime execution uses the separately verified
Computation/Engine/PHP extension chain. See [host integration](docs/integration.md)
and [semantic boundaries](docs/semantic-boundaries.md).

## Documentation

- [Public API](docs/public-api.md) and [architecture](docs/architecture.md)
- [Formula profile](docs/formula-profile.md) and [security/compatibility](docs/security.md)
- [Test ownership](docs/test-ownership.md) and [consumer release record](docs/release-record.md)
- [Release process](docs/releasing.md) and [changelog](CHANGELOG.md)

## Development

```sh
composer install
composer check
```

The complete gate covers dependency identity, security audit, syntax/docblocks,
architecture, manifests/API, production autoload, examples, coding standards,
static analysis, behavior/conformance, test ownership and a clean no-dev archive
consumer. CI runs PHP 8.5 on Linux. Dependabot proposes grouped weekly Composer
updates; each update runs the same gate before review and rebase merge.

Published tags remain fixed. Consumers independently verify exact releases and their
dependency graph, then run their own integration checks. Changes on `main` are shipped
in a subsequent release. Licensed under [Apache-2.0](LICENSE).
