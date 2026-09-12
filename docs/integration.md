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

Core consumers verify exact package releases, use canonical package types and retain persistence, publication, trust
and recovery tests. Source-to-package mappings and test transfers are recorded in [the release record](release-record.md).
Replace runtime evaluation only with the required verified native computation boundary; retain runtime tests until
that composition is verified.

## Released dependency coordinates

Composer requires Localization 0.1.1 and Sequence 0.2.1, both published stable releases.
Use `composer install` for source development and `composer clean-consumer` to prove the built archive with runtime
dependencies only. No root VCS overrides or development constraints are needed.

Exact pins keep pre-1.0 compatibility reviewable. Dependency update pull requests run the complete package gate
before maintainers merge them. Consumers independently verify releases and run their composed Core integration tests after dependency updates.
