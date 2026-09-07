# Integration

Construct `BusinessDefinitionValidator($fieldTypes, $fieldConfigurationAdmission)` explicitly, or register `(new
Kumwe\BusinessDefinition\ConfigProvider())()['dependencies']` with Laminas ServiceManager. Bind the mandatory
`FieldConfigurationAdmission` to the host adapter for the selected SDK presentation contract before requesting a
validator. Missing or incompatible ports fail; there is no fallback.

FieldTypeRegistry is shared only inside the host-owned container. Validator and contribution registry are non-shared;
do not retain a process-global registry across sites or trusted generations. Assemble field types, register
definitions and validate the complete graph before exposing it. Registries are mutable operation assembly objects and
provide no concurrent writer synchronization. Immutable definitions and stateless analyzers can be shared. No service
begins a transaction or performs I/O.

The archive example uses an explicit fixture-specific admission policy that accepts only its empty configuration. It
is not a general host policy. A production adapter must delegate to the actual SDK configuration profile; its
integration tests stay in App.

Phase 2 updates all exact handoff consumers after verified releases, removes migrated implementations and duplicate
behavior tests together, preserves persistence/publication/trust/recovery tests, and replaces runtime evaluation only
with the required verified native computation boundary.

## Development dependency candidate

The `agent/candidate-sequence-dependency-v2` branch uses the actual `kumwe/sequence`
`dev-main` candidate instead of the unavailable `0.2.0` coordinate. The observed
Sequence main commit is `3cb1f63437ddec7ddbd8392e997bceb981cd73b5`; it provides
`NumberSequenceFormat::fromConfiguration`, `MAXIMUM_LENGTH`, scope and reset
values including `NumberSequenceReset::FiscalPeriod` consumed by the validator.
No Sequence behavior is copied or changed. Composer repositories are root-only,
so source consumers must declare both the Business Definition and Sequence GitHub
VCS repositories and allow their explicit development constraints.

This enables source and isolated archive verification. Publication remains blocked
until actual immutable dependency releases have independent external attestations.
The source candidate is not an alias for a stable release.
