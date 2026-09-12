# Architecture

Domain owns immutable metadata and semantic invariants. Application owns graph validation, compatibility and
operation-local registries. Container factories depend on PSR-11; domain values remain container-agnostic.

Sequence owns number format/scope/reset; Localization owns locale tags. Ramsey UUID preserves the original UUID
admission grammar. DefinitionOwner remains domain-specific because it includes site ownership and a persisted package
grammar distinct from ContributionOwner. There is no unused Contribution dependency.

The mandatory FieldConfigurationAdmission port keeps SDK presentation configuration semantics at the host composition
boundary. Validator failure is fail-closed: admission runs for each field before field-specific semantic checks. SDK
exceptions map to InvalidBusinessDefinition with the original exception as cause.

No repository, database, authorization, trust, active generation, event, audit, transaction, transport or runtime VM
is exported. The frozen tests/Oracle tree is excluded from Composer archives and production autoloading. Native
execution requires the verified Computation/Engine/extension chain.
