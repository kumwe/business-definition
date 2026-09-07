# Kumwe Business Definition

Immutable business definitions, typed bounded formula ASTs, canonical profiles, structural validation, compatibility
and registries. Requires PHP 8.5 on 64-bit platforms with mbstring for Unicode text default validation.

This is an extraction candidate. Exact draft dependencies are installable but not independently release-verified;
publication and App adoption are blocked. No release is claimed.

Run `composer install` and `composer check`. The package owns behavior, boundary and conformance tests. Use `php
examples/definition.php` for an independent definition consumer.

Every validator needs a `FieldConfigurationAdmission` implementation from the host. The ConfigProvider supplies real
factories but never substitutes a permissive admission policy. See [integration](docs/integration.md), [public
API](docs/public-api.md), [test ownership](docs/test-ownership.md), [formula profile](docs/formula-profile.md) and
[handoff](MIGRATION-HANDOFF.md).
