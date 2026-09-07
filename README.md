# Kumwe Business Definition

Immutable business definitions, typed bounded formula ASTs, canonical profiles, structural validation, compatibility
and registries. Requires PHP 8.5 on 64-bit platforms with mbstring for Unicode text default validation.

Version 0.1.0 has been published. This branch prepares the 0.1.1 maintenance release.
App adoption remains a separate task after verification of the final release and dependency closure.

Run `composer install` and `composer check`. The package owns behavior, boundary and conformance tests. Use `php
examples/definition.php` for an independent definition consumer.

Every validator needs a `FieldConfigurationAdmission` implementation from the host. The ConfigProvider supplies real
factories but never substitutes a permissive admission policy. See [integration](docs/integration.md), [public
API](docs/public-api.md), [test ownership](docs/test-ownership.md), [formula profile](docs/formula-profile.md) and
[handoff](MIGRATION-HANDOFF.md).

Maintenance release: Detach admitted defaults, configuration, validators, workflow and document collections from
caller references. Canonical JSON encoding no longer mutates referenced caller arrays.

Direct Kumwe dependencies use exact stable versions. Dependabot proposes grouped weekly Composer updates; review
and merge only after the complete package gate passes. The downstream App consumes a verified exact release, never
an unreviewed moving `latest` constraint.
