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
