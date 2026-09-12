# Public API

This package exports immutable domain values, structural validation and operation-local registries.

All source parameters, return types, invariants and exceptions below are the documented contracts. Existing
@since 2.0.0 tags record App history; package availability begins at 0.1.0. Removed runtime methods are documented in
[semantic-boundaries.md](semantic-boundaries.md). No public operation performs I/O, starts a transaction or grants authority. Immutable values are
process-safe; mutable registries require host operation/container isolation. Factories are explicit and fail on
missing or incompatible host collaborators.

## `Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer`

Prices what publishing a draft definition would do to the version already in service.

Publishing a business definition is irreversible for the data behind it, so nothing is written before
every difference between the published head and the draft has been named and classified. This is the
only producer of `CompatibilityPlan`: it walks identity, fields, relationships, views, actions, the
workflow binding and the record invariants, emits one classified `CompatibilityChange` per difference,
and leaves the plan to put them in order. The draft is advanced to its next version before anything is
compared, so the status and version fields that publication itself moves never register as differences.
`BusinessDefinitionService` reads the resulting plan to decide whether publication needs an explicit
confirmation, and stores it beside the version it published.

@since  2.0.0

### `analyze(?Kumwe\BusinessDefinition\Domain\EntityTypeDefinition $before, Kumwe\BusinessDefinition\Domain\EntityTypeDefinition $draft): Kumwe\BusinessDefinition\Domain\CompatibilityPlan`

Compare the published head against the draft that would replace it and classify every difference.

A null `$before` is the first publication of the handle: there is nothing to compare against, so the
plan carries the single additive change that records the creation itself.

@param   ?EntityTypeDefinition  $before  Published version currently in service, or null when this
         handle has never been published.
@param   EntityTypeDefinition   $draft   Draft being assessed; it is advanced to the next version
         before comparison, so it must still carry draft status.

@return  CompatibilityPlan  Both version numbers, both canonical checksums, and the classified changes.

@throws  \Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition  When the draft is not in draft
         status and therefore cannot be advanced to the next published version.

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\BusinessDefinitionContributionRegistry`

Collects the entity types core and extensions contribute during bootstrap, to be checked as one graph.

A contributed definition may reference entity types and field types another package ships, so no single
provider can be validated on its own; contributions accumulate here and are checked together once the
last provider has run. Registration is where ownership is enforced — a contributor may claim only
handles inside its own owner namespace, its definition must name the same owner that is registering it,
and a handle already claimed is refused rather than overwritten — which is what stops one extension from
shadowing another's entity type. Entries are kept in handle order so the validated set does not depend
on the order providers happened to run in, and `remove()` withdraws an owner's contributions when its
extension is disabled, uninstalled or loses trust.

@since  2.0.0

### `__construct(Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator $validator): `

Bind the registry to the validator that will check the assembled graph.

@param  BusinessDefinitionValidator  $validator  Checks the contributed set once every provider ran.

@since  2.0.0

### `register(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner, Kumwe\BusinessDefinition\Domain\EntityTypeDefinition $definition): void`

Take one contributed entity type into the set, refusing anything the contributor may not claim.

Both ownership checks matter: the handle has to sit inside the contributor's namespace, and the owner
recorded inside the definition has to be the contributor itself, so a package cannot ship a
definition attributed to somebody else. Nothing about the definition's references is checked yet —
that waits for `validate()`, when the rest of the graph exists.

@param   DefinitionOwner       $owner       Contributor registering the definition, core or extension.
@param   EntityTypeDefinition  $definition  Entity type being contributed this process.

@return  void

@throws  InvalidBusinessDefinition  When the handle falls outside the contributor's owner namespace,
         the definition names a different owner, or the handle is already registered.

@since   2.0.0

### `validate(): void`

Check everything contributed so far as a single graph.

Cross-package references resolve only once every provider has run, so this is driven by whoever owns
the contribution phase rather than folded into `register()`. A process that contributed nothing does
no work here, because the validator treats an empty graph as an error rather than a trivial pass.

@return  void

@throws  InvalidBusinessDefinition  When the contributed set is not a valid graph — an unresolvable
         entity or field-type reference, unsupported field configuration, or more than 128 entities.

@since   2.0.0

### `all(): array`

Return every entity type contributed this process, whoever contributed it.

@return  list<EntityTypeDefinition>  In handle order, so the order providers ran in does not leak into
         the validated graph or anything derived from it.

@since   2.0.0

### `ownedBy(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner): array`

Return only the entity types one contributor registered.

@param   DefinitionOwner  $owner  Contributor to report on, matched on owner type and identifier.

@return  list<EntityTypeDefinition>  In handle order; empty when that contributor registered nothing.

@since   2.0.0

### `remove(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner): void`

Withdraw everything one contributor registered.

Driven when an extension is disabled, uninstalled or loses trust, so its entity types stop reaching
the graph the rest of the process validates and reads. Removing an owner that holds nothing is a
silent no-op, which lets lifecycle code withdraw a package without first asking what it contributed.

@param   DefinitionOwner  $owner  Contributor whose entity types are being withdrawn.

@return  void

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator`

Checks the business-definition rules no single declaration can answer for itself.

`FieldDefinition` and `RelationshipDefinition` settle what one declaration can decide alone, and
`EntityTypeDefinition` settles what is internal to one entity. What is left needs the whole set at once:
whether a declared field type is registered and the configuration keys behind it belong to that type,
whether a referenced entity exists, sits in the same site and scope, and names a reciprocal inverse, and
whether ownership edges stay acyclic so cascade deletion terminates. The same pass applies the limits that
keep a definition portable across the supported database engines — declared lengths, sortable columns
bounded for keyset pagination, and defaults the emitted column could actually hold.

Callers hand over a self-contained set: `BusinessDefinitionContributionRegistry` passes everything core and
the enabled extensions contributed at bootstrap, and `BusinessDefinitionService` closes a draft over its
dependency graph before saving or publishing it. A reference leaving that set is a failure, not a deferral,
so nothing reaches the schema compiler with a dangling target.

@since  2.0.0

### `__construct(Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver $fieldTypes, Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission $fieldConfiguration): `

Bind the validator to the resolver that supplies field-type structure.

@param  FieldTypeDefinitionResolver  $fieldTypes  Answers what a declared field-type identifier means,
        including for a type whose owning extension is no longer running.

@param FieldConfigurationAdmission $fieldConfiguration Host-selected presentation contract admission.

@since  2.0.0

### `validateGraph(array $definitions): void`

Check a set of entity definitions as one graph, raising on the first rule it breaks.

The set has to be self-contained and bounded: an empty graph and one above 128 entities are both
refused, a handle may appear only once, and every entity a field or relationship targets has to be
present here. Beyond resolving references this is where the runtime's own restrictions are applied —
`runtime_relation_evidence` is reserved as a field handle, a required relationship is refused because
creating both sides atomically is not supported, cascade deletion is reserved for owned line
collections, and set-null for singular associations. Nothing is collected into a report; the first
failure raises.

@param   list<EntityTypeDefinition>  $definitions  The complete set to check, in any order.

@return  void

@throws  InvalidBusinessDefinition  When the set is empty or above 128 entities, a handle is
         duplicated, a field type or a targeted entity cannot be resolved, a field carries
         configuration its type does not register, an entity declares more than one posting date
         field or a fiscal-period number sequence without one, a reference crosses site or scope,
         a declared inverse is missing, ambiguous or not reciprocal, a delete behaviour does not
         suit its cardinality, or owned collections form a cycle.

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\DefinitionCatalogEntry`

Where one business-definition handle stands in a site's catalog, without any of its definition bytes.

`BusinessDefinitionRepository::catalog()` and `entry()` answer with these heads, and every operation on a
definition starts from one: the service turns a caller-supplied UUID or handle into an entry, authorizes
against its `$id`, and only then loads the draft or a published version. Separating the head from the bytes
is what makes that first step cheap, and the head is also what a caller reads to decide its next write —
`$draftRevision` is the token an optimistic `saveDraft()` or `publish()` has to quote, and
`$publishedVersion` says whether the handle is serving anything yet.

@since  2.0.0

- `$id` (string): Readonly constructor state; see the constructor parameter contract.

- `$siteIdentifier` (string): Readonly constructor state; see the constructor parameter contract.

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$owner` (Kumwe\BusinessDefinition\Domain\DefinitionOwner): Readonly constructor state; see the constructor
  parameter contract.

- `$ownerActive` (bool): Readonly constructor state; see the constructor parameter contract.

- `$draftRevision` (int): Readonly constructor state; see the constructor parameter contract.

- `$publishedVersion` (?int): Readonly constructor state; see the constructor parameter contract.

- `$status` (Kumwe\BusinessDefinition\Domain\DefinitionStatus): Readonly constructor state; see the constructor
  parameter contract.

- `$updatedAt` (DateTimeImmutable): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $id, string $siteIdentifier, string $handle, Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner, bool $ownerActive, int $draftRevision, ?int $publishedVersion, Kumwe\BusinessDefinition\Domain\DefinitionStatus $status, DateTimeImmutable $updatedAt): `

Capture where one handle stands, as the catalog head records it.

@param  string             $id                Definition UUID, which is also the identity that
        authorization resources and audit entries are keyed on.
@param  string             $siteIdentifier    Site whose catalog holds the handle.
@param  string             $handle            Namespaced entity handle this head stands for.
@param  DefinitionOwner    $owner             Who introduced the definition: core, an extension, or the
        site itself. Settled when the entry was created and never moved afterwards.
@param  bool               $ownerActive       Whether an extension-owned definition is currently usable;
        core- and site-owned entries are always active.
@param  int                $draftRevision     Revision of the stored draft, which the next write has to
        quote; zero once a publication consumed the draft.
@param  ?int               $publishedVersion  Version the head currently serves, or null when the handle
        has never been published.
@param  DefinitionStatus   $status            Publication state of the head, as its last write left it.
@param  DateTimeImmutable  $updatedAt         Instant the head last moved.

@since  2.0.0

## `Kumwe\BusinessDefinition\Application\DefinitionDraft`

A business definition's work in progress, checked against itself on the way out of storage.

Between publications a handle carries at most one draft, and this is the shape every reader of it gets:
`BusinessDefinitionRepository::draft()` and `saveDraft()` both answer with one, and the administrator, REST
and console surfaces render it. Two things travel beside the bytes. The revision is the optimistic token the
next `saveDraft()` or `publish()` has to quote, so a caller can prove it composed its change against the
state it read. The checksum is the digest stored alongside those bytes, and the constructor re-derives it
from the definition rather than trusting it, so a draft row that was hand-edited or corrupted in the
database is refused here instead of reaching the compatibility analyzer as though it were canonical.

@since  2.0.0

- `$definition` (Kumwe\BusinessDefinition\Domain\EntityTypeDefinition): Readonly constructor state; see the
  constructor parameter contract.

- `$revision` (int): Readonly constructor state; see the constructor parameter contract.

- `$checksum` (string): Readonly constructor state; see the constructor parameter contract.

- `$updatedBy` (string): Readonly constructor state; see the constructor parameter contract.

- `$updatedAt` (DateTimeImmutable): Readonly constructor state; see the constructor parameter contract.

### `__construct(Kumwe\BusinessDefinition\Domain\EntityTypeDefinition $definition, int $revision, string $checksum, string $updatedBy, DateTimeImmutable $updatedAt): `

Capture a stored draft and assert that its bytes, revision and checksum describe the same save.

@param   EntityTypeDefinition  $definition  Draft definition as it was last saved.
@param   int                   $revision    Draft revision, counting from one, that the next write to
         this handle has to quote.
@param   string                $checksum    Digest stored beside the bytes, which has to equal the
         definition's own canonical checksum.
@param   string                $updatedBy   Actor recorded as having last saved the draft.
@param   DateTimeImmutable     $updatedAt   Instant of that save.

@throws  InvalidBusinessDefinition  When the revision is below one, or the stored checksum does not
         match the definition's canonical bytes.

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\DefinitionVersionRecord`

One version of a business definition as it was published, beside the compatibility plan that produced it.

Every read of definition history — the published head, the version list, the document a lifecycle change
answers with — arrives as this record, so its constructor is where a row coming out of storage is checked
against itself: the definition must carry published status, its bytes must hash to the checksum the plan
names as its target, its version number must be the one the plan produced, and the record's own lifecycle
status must have left draft. A row failing any of those is refused rather than served, which keeps a
corrupted or hand-edited catalog from reaching the schema compiler as though it were canonical.

@since  2.0.0

- `$definition` (Kumwe\BusinessDefinition\Domain\EntityTypeDefinition): Readonly constructor state; see the
  constructor parameter contract.

- `$compatibility` (Kumwe\BusinessDefinition\Domain\CompatibilityPlan): Readonly constructor state; see the
  constructor parameter contract.

- `$status` (Kumwe\BusinessDefinition\Domain\DefinitionStatus): Readonly constructor state; see the constructor
  parameter contract.

- `$publishedBy` (string): Readonly constructor state; see the constructor parameter contract.

- `$publishedAt` (DateTimeImmutable): Readonly constructor state; see the constructor parameter contract.

### `__construct(Kumwe\BusinessDefinition\Domain\EntityTypeDefinition $definition, Kumwe\BusinessDefinition\Domain\CompatibilityPlan $compatibility, Kumwe\BusinessDefinition\Domain\DefinitionStatus $status, string $publishedBy, DateTimeImmutable $publishedAt): `

Capture a stored definition version and assert that its parts describe the same publication.

@param   EntityTypeDefinition  $definition     Canonical published bytes of this version.
@param   CompatibilityPlan     $compatibility  Plan analysed when this version replaced the previous one.
@param   DefinitionStatus      $status         Lifecycle state the version sits in now; never `Draft`.
@param   string                $publishedBy    Identifier of the actor who published the version.
@param   DateTimeImmutable     $publishedAt    Instant at which the publication was recorded.

@throws  InvalidBusinessDefinition  When bytes, plan and status do not describe one published version.

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission`

Host-supplied admission of field configuration against the selected presentation contract.

Definition rules and field-type configuration-key checks remain in BusinessDefinitionValidator.
The host must supply its actual presentation-profile validator; there is no default implementation.
Implementations must neither mutate input nor grant authorization or select a trusted generation.

@since 0.1.0

### `assertAdmissible(array $configuration): void`

Refuse configuration the composed presentation contract cannot safely transport.

@param array<string, mixed> $configuration Immutable field configuration to inspect.
@return void
@throws InvalidArgumentException When the selected contract refuses this configuration.
@since 0.1.0

## `Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver`

Resolves immutable field-type structure without implying that its owner is executable.

Validating a definition and compiling its physical schema both need the shape of a field type long after
the extension that contributed it stopped running, because rows already written under that type still
have to be read and migrated. Implementations therefore answer from whichever authoritative record they
hold — the in-memory contribution set, or checksum-verified persisted history — and never treat "not
currently active" as "not resolvable". Structure resolved here confers no permission to execute the
owning extension's code.

@since  2.0.0

### `get(string $identifier): Kumwe\BusinessDefinition\Domain\FieldTypeDefinition`

Resolve the structure registered under a field-type identifier.

Absence is a failure rather than a null result: a definition that names a type nobody can vouch for
must not validate, so implementations raise instead of degrading to a default shape.

@param   string  $identifier  Namespaced field-type identifier, such as `core.text`.

@return  FieldTypeDefinition  The structure the identifier was registered with.

@throws  \Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition  When the identifier is unresolvable.

@since   2.0.0

## `Kumwe\BusinessDefinition\Application\FieldTypeRegistry`

Mutable set of the field types the running process may build business definitions from.

The registry is filled once per process — core built-ins first, then one extension's contributions at a
time — and is what `BusinessDefinitionValidator` resolves field references against, so a definition can
only name a type some present owner vouches for. Registration is ownership-checked and write-once: an
identifier belongs to exactly one owner and is never redefined in place, which is what lets `remove()`
withdraw an extension's contributions during uninstall without disturbing anyone else's. It resolves the
active set only; structure for a withdrawn owner comes from the persisted resolver instead.

@since  2.0.0

### `__construct(bool $withCore = default): `

Start a registry, optionally seeded with the core built-in field types.

@param  bool  $withCore  Whether to seed the `core.*` built-ins; false leaves it empty for contributions.

@since  2.0.0

### `register(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner, Kumwe\BusinessDefinition\Domain\FieldTypeDefinition $definition): void`

Claim an identifier for one owner and make its structure resolvable.

@param   DefinitionOwner      $owner       Contributor claiming the type; its namespace must cover the id.
@param   FieldTypeDefinition  $definition  Structure to expose under its own `id`.

@return  void

@throws  InvalidBusinessDefinition  When the id sits outside the owner namespace or is already claimed.

@since   2.0.0

### `get(string $identifier): Kumwe\BusinessDefinition\Domain\FieldTypeDefinition`

Resolve a field type from the set currently registered in this process.

Only the in-memory set is consulted, so a type whose owner has been withdrawn reads as absent here
even though its persisted structure still exists.

@param   string  $identifier  Namespaced field-type identifier, such as `core.text`.

@return  FieldTypeDefinition  The structure its present owner registered.

@throws  InvalidBusinessDefinition  When no present owner has claimed that identifier.

@since   2.0.0

### `has(string $identifier): bool`

Report whether an identifier is claimed right now.

@param   string  $identifier  Namespaced field-type identifier to look for.

@return  bool  True when `get()` would resolve it instead of raising.

@since   2.0.0

### `all(): array`

List every registered structure, in identifier order.

@return  list<FieldTypeDefinition>  Structures only; the owner behind each one is dropped.

@since   2.0.0

### `ownedBy(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner): array`

List the structures one owner contributed, in identifier order.

Owners are compared by type and identifier rather than by instance, so a rebuilt owner value matches.

@param   DefinitionOwner  $owner  Contributor whose claims are being inventoried.

@return  list<FieldTypeDefinition>  Empty when that owner has claimed nothing.

@since   2.0.0

### `remove(Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner): void`

Withdraw every structure one owner contributed.

Uninstall and a rolled-back install both end here, so removing an owner that holds nothing is a
no-op, and entries claimed by other owners are left untouched.

@param   DefinitionOwner  $owner  Contributor whose claims leave the registry.

@return  void

@since   2.0.0

## `Kumwe\BusinessDefinition\ConfigProvider`

Explicit runtime services; host admission must be registered separately. @since 0.1.0

### `__invoke(): array`

Return deterministic Mezzio configuration without consulting runtime state.
@return array{dependencies: array{factories: array<class-string,
    class-string<FieldTypeRegistryFactory>|class-string<BusinessDefinitionValidatorFactory>
    |class-string<BusinessDefinitionContributionRegistryFactory>>,
    aliases: array<class-string, class-string>, shared: array<class-string, bool>}}
@since 0.1.0

## `Kumwe\BusinessDefinition\Container\BusinessDefinitionContributionRegistryFactory`

Explicit factory; no host authority or process-global state is installed. @since 0.1.0

### `__invoke(Psr\Container\ContainerInterface $container): Kumwe\BusinessDefinition\Application\BusinessDefinitionContributionRegistry`

Resolve the documented collaborators and construct an operation-local service.
@param ContainerInterface $container Host composition container.
@return BusinessDefinitionContributionRegistry
@throws InvalidArgumentException When a supplied service has the wrong contract.
@since 0.1.0

## `Kumwe\BusinessDefinition\Container\BusinessDefinitionValidatorFactory`

Explicit factory; no host authority or process-global state is installed. @since 0.1.0

### `__invoke(Psr\Container\ContainerInterface $container): Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator`

Resolve the documented collaborators and construct an operation-local service.
@param ContainerInterface $container Host composition container.
@return BusinessDefinitionValidator
@throws InvalidArgumentException When a supplied service has the wrong contract.
@since 0.1.0

## `Kumwe\BusinessDefinition\Container\FieldTypeRegistryFactory`

Explicit factory; no host authority or process-global state is installed. @since 0.1.0

### `__invoke(Psr\Container\ContainerInterface $container): Kumwe\BusinessDefinition\Application\FieldTypeRegistry`

Resolve the documented collaborators and construct an operation-local service.
@param ContainerInterface $container Host composition container.
@return FieldTypeRegistry
@since 0.1.0

## `Kumwe\BusinessDefinition\Domain\ActionDefinition`

One named operation a business entity offers on its records, together with the capability guarding it.

Actions are declared inside an entity definition and travel into the immutable published payload, so
the runtime resolves them from the version a record is pinned to rather than from live configuration.
`BusinessRecordService` looks the action up by handle, demands `$capability`, evaluates `$condition`
against the record's current values, and then performs `$transition` on the entity's workflow — an
action naming no transition has nothing the generated runtime can execute. An optional owner-scoped
handler/schema pair instead binds the action to a typed custom application handler and signed contract;
it is mutually exclusive with a workflow transition. The surface flags decide only where an action may
be offered; they never grant permission on their own. This constructor remains the validation point.

@since  2.0.0

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$label` (string): Readonly constructor state; see the constructor parameter contract.

- `$capability` (string): Readonly constructor state; see the constructor parameter contract.

- `$bulk` (bool): Readonly constructor state; see the constructor parameter contract.

- `$administrator` (bool): Readonly constructor state; see the constructor parameter contract.

- `$portal` (bool): Readonly constructor state; see the constructor parameter contract.

- `$public` (bool): Readonly constructor state; see the constructor parameter contract.

- `$highImpact` (bool): Readonly constructor state; see the constructor parameter contract.

- `$condition` (?Kumwe\BusinessDefinition\Domain\Expression): Readonly constructor state; see the constructor
  parameter contract.

- `$transition` (?string): Readonly constructor state; see the constructor parameter contract.

- `$handler` (?string): Readonly constructor state; see the constructor parameter contract.

- `$schema` (?string): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $handle, string $label, string $capability, bool $bulk = default, bool $administrator = default, bool $portal = default, bool $public = default, bool $highImpact = default, ?Kumwe\BusinessDefinition\Domain\Expression $condition = default, ?string $transition = default, ?string $handler = default, ?string $schema = default): `

Declare an action, validating its identity, guard, surfaces, and precondition.

@param   string       $handle         Lowercase snake-case name the action is invoked by.
@param   string       $label          Operator-facing name shown wherever the action is offered.
@param   string       $capability     Dotted capability an actor must hold to run the action.
@param   bool         $bulk           Whether the action may be offered against a selection of records.
@param   bool         $administrator  Whether the administrator surface may offer the action.
@param   bool         $portal         Whether the portal surface may offer the action.
@param   bool         $public         Always rejected when true; actions are never anonymous.
@param   bool         $highImpact     Marks the action consequential enough to warrant confirmation.
@param   ?Expression  $condition      Boolean precondition on the record; null leaves it unconditional.
@param   ?string      $transition     Workflow transition handle the action performs, or null for none.
@param   ?string      $handler        Owner-scoped custom handler reference, or null for generated behavior.
@param   ?string      $schema         Owner-scoped signed schema reference paired with `$handler`.

@throws  InvalidBusinessDefinition  When the handle or label is malformed, the capability is not a dotted
         identifier, public execution is requested, neither the administrator nor the portal surface is
         declared, the transition or custom references are malformed, both execution mechanisms are
         declared, or the condition does not produce boolean.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\ActionDefinition`

Rebuild an action from its canonical document, rejecting any property the contract does not name.

The unknown-property check runs before anything is read, so a document written against a newer or
hand-edited schema fails at the import boundary rather than being silently truncated on the way in.

@param   array<string, mixed>  $document  Decoded action document keyed by canonical snake-case name.

@return  self  The validated action, having passed the same invariants as direct construction.

@throws  InvalidBusinessDefinition  When the document carries an unknown property, a condition that is
         not a non-empty JSON object, a property of the wrong type, or values the constructor rejects.

@since   2.0.0

### `toArray(): array`

Export the action as the document that becomes part of a published definition's canonical bytes.

Every declared property is emitted, including the defaults, so the document round-trips through
`fromArray()` unchanged. Key order is irrelevant: `CanonicalDefinitionJson` sorts before hashing.

@return  array<string, mixed>  Declared properties under their snake-case canonical keys, with the
         condition rendered as a nested document or null.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\BuiltInFieldTypes`

The field-type catalogue every site can build definitions from without installing an extension.

`FieldTypeRegistry` seeds itself from this list under `DefinitionOwner::core()`, which is what makes
these the identifiers a definition may name out of the box; anything else has to be contributed by a
package under its own namespace. Each entry fixes three things a field cannot renegotiate later: the
value family definitions work with, the physical storage family the schema compiler emits for it, and
the configuration keys a field of that type is allowed to set. The identifiers are the stable half of
the contract: published versions reference a type by name, so renaming one would strand every version
that already names it, and the catalogue grows by addition.

@since  2.0.0

### `all(): array`

Build the complete core catalogue, grouped by family in declaration order.

A fresh list is constructed on every call, so this is a source of truth rather than a lookup:
`FieldTypeRegistry` consumes it once at construction, re-keys it by identifier, and answers
every later question about which types are active.

@return  list<FieldTypeDefinition>  Every core type, each identifier carrying the `core.` prefix.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\CanonicalDefinitionJson`

Byte-stable JSON encoder that every business-definition checksum and document comparison runs through.

A published definition version is immutable and identified by a SHA-256 over these bytes, and package
synchronization decides whether a declaration changed by comparing a stored checksum against a freshly
encoded one. Those comparisons cross processes and outlive the request that made them, so the encoding
cannot depend on the order keys happen to sit in: string-keyed arrays are sorted, while lists keep
their positions because order carries meaning there. The accepted value space is deliberately narrow —
floats, resources, and objects are refused outright rather than digested into something that will not
reproduce — and nesting depth and collection size are bounded so a definition cannot carry unbounded
structure into the checksum. `FieldDefinition` calls `encode()` purely for that rejection, proving a
default, a configuration map, or a validator is representable before it is allowed into a definition,
and `Expression` measures the encoded length against its own byte budget.

@since  2.0.0

### `encode(mixed $value): string`

Encode a value into the canonical bytes definitions are stored, compared, and hashed as.

The value space is checked in full before anything is encoded, so an unsupported value is reported
as a definition error naming the offending shape rather than as a JSON failure after the fact.

@param   mixed  $value  Value to encode; string-keyed arrays are sorted recursively first.

@return  string  Canonical JSON with slashes and unicode left unescaped and zero fractions preserved.

@throws  InvalidBusinessDefinition  When the value nests deeper than 32 levels, holds a collection of
         more than 512 entries, contains a float, resource, or object, or cannot be JSON encoded — a
         malformed UTF-8 string being the remaining case.

@since   2.0.0

### `checksum(mixed $value): string`

Reduce a value to the digest a published version is identified and later re-verified by.

@param   mixed  $value  Value to fingerprint; canonically encoded before it is hashed.

@return  string  Lowercase hexadecimal SHA-256 of the canonical encoding, 64 characters wide.

@throws  InvalidBusinessDefinition  When the value cannot be canonically encoded.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\CompatibilityChange`

One classified difference between the published definition version and the draft that would replace it.

`BusinessDefinitionCompatibilityAnalyzer` emits one of these per difference it finds, and
`CompatibilityPlan` orders and carries them into the publication gate. The path is a slash-separated
pointer into the definition document — `/fields/title/required`, `/relationships/author`, `/workflow` —
so a reviewer reading a plan sees which part of the contract moved rather than a diff of the whole
payload, and the classification is what decides whether publishing needs an explicit confirmation. The
pointer charset and the message ceiling are enforced up front because a change, once emitted, becomes
part of an immutable published plan that no later correction can rewrite.

@since  2.0.0

- `$path` (string): Readonly constructor state; see the constructor parameter contract.

- `$classification` (Kumwe\BusinessDefinition\Domain\CompatibilityClassification): Readonly constructor state; see the
  constructor parameter contract.

- `$message` (string): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $path, Kumwe\BusinessDefinition\Domain\CompatibilityClassification $classification, string $message): `

Record one difference, validating that it can be published as part of an immutable plan.

@param   string                       $path            Pointer to the part of the definition that moved,
         for example `/fields/title/required`.
@param   CompatibilityClassification  $classification  How severe the change is for stored records.
@param   string                       $message         Operator-facing sentence describing the change.

@throws  InvalidBusinessDefinition  When the path is not a slash-led lowercase pointer, or the message
         is empty or longer than 500 characters.

@since   2.0.0

### `toArray(): array`

Export the change as the document embedded in a stored plan and returned by the definitions API.

@return  array{path: string, classification: string, message: string}  The pointer, the classification's
         backing value, and the operator message.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\CompatibilityClassification`

How much damage one definition change can do, and therefore what publication is allowed to do with it.

The compatibility analyzer assigns exactly one of these to every difference it finds between the
published head and the draft, and the publication gate reads nothing else when deciding whether to
stop. The cases run from harmless to irreversible: only `Additive` lets a publication through without
an explicit confirmation, and `Destructive` is surfaced separately again so an operator sees it before
confirming. The backing values are written into immutable published plans, so they are permanent.

@since  2.0.0

- `Additive`: The contract only widens, so every stored record and existing caller keeps working untouched.

@since  2.0.0

- `CompatibleConstraintTightening`: The contract narrows, but stored records already satisfy it, so nothing has to be
  rewritten.

@since  2.0.0

- `BehaviorChanging`: Stored data stays valid while the behaviour around it moves — exposure, computation, views,
actions, workflow, or presentation.

@since  2.0.0

- `DataMigrationRequired`: Stored records will not satisfy the new contract, so publishing commits to migrating them.

@since  2.0.0

- `Destructive`: Part of the contract is withdrawn and the data behind it can no longer be reached through the
definition.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `requiresConfirmation(): bool`

Whether a change of this class may only be published once the publisher has confirmed it.

@return  bool  False for `Additive` alone; every other classification demands confirmation.

@since   2.0.0

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\CompatibilityPlan`

The complete machine-readable account of what publishing a draft would change, and what that costs.

`BusinessDefinitionCompatibilityAnalyzer` builds one by comparing the published head against the draft;
`BusinessDefinitionService` refuses to publish when the plan requires a confirmation the caller did not
give, then stores the plan alongside the version it published. Carrying both version numbers and both
checksums is what lets a stored plan be tied back later to exactly the bytes it described, and sorting
the changes at construction is what makes the serialized plan reproducible: the same pair of
definitions yields identical bytes no matter what order the analyzer happened to discover differences
in.

@since  2.0.0

- `$fromVersion` (?int): Readonly constructor state; see the constructor parameter contract.

- `$toVersion` (int): Readonly constructor state; see the constructor parameter contract.

- `$fromChecksum` (?string): Readonly constructor state; see the constructor parameter contract.

- `$toChecksum` (string): Readonly constructor state; see the constructor parameter contract.

### `__construct(?int $fromVersion, int $toVersion, ?string $fromChecksum, string $toChecksum, array $changes): `

Assemble a plan, validating its version bounds and checksums and putting its changes in order.

Sorting is by path, then classification, then message, which is a total order over the change
documents themselves rather than over object identity, so it is stable across processes.

@param   ?int                       $fromVersion   Version being replaced, or null when this would be the
         first published version.
@param   int                        $toVersion     Version this plan would publish.
@param   ?string                    $fromChecksum  Canonical checksum of the version being replaced, or
         null when there is none.
@param   string                     $toChecksum    Canonical checksum of the definition to be published.
@param   list<CompatibilityChange>  $changes       Differences found by the analyzer, in any order.

@throws  InvalidBusinessDefinition  When the target version is below one, a replacement does not advance
         the version by exactly one, or a checksum is not 64 lowercase hexadecimal characters.

@since   2.0.0

### `changes(): array`

Return the classified differences, ordered by path, then classification, then message.

@return  list<CompatibilityChange>  Empty when nothing about the contract moved between the two
         versions; a first publication always reports at least the creation itself.

@since   2.0.0

### `requiresConfirmation(): bool`

Whether publishing this plan needs the publisher to acknowledge its consequences first.

True as soon as a single change is anything other than additive. `BusinessDefinitionService::publish()`
reads this and rejects an unconfirmed publication, so the flag is a gate rather than a hint.

@return  bool  True when at least one change requires confirmation.

@since   2.0.0

### `destructive(): bool`

Whether any change withdraws part of the contract and the data behind it.

Reported separately from `requiresConfirmation()` — which is already true for every destructive plan —
so a surface can warn more loudly; the administrator publication gate badges the plan on this flag.

@return  bool  True when at least one change is classified destructive.

@since   2.0.0

### `toArray(): array`

Export the plan as the document stored with the published version, audited, and served to clients.

The two derived flags are materialized here rather than recomputed downstream, so a stored plan keeps
the verdict that was actually acted on even if the classification rules are later revised.

@return  array<string, mixed>  Version bounds under `from_version` and `to_version`, both checksums, the
         `requires_confirmation` and `destructive` verdicts, and the ordered change documents under
         `changes`.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\ComputationMode`

Where a computed field's value comes from: derived on read, or materialized into a column.

Only a field marked computed may leave `Virtual`, and the choice decides more than where work happens.
The physical schema compiler reads it to decide whether the field gets a column at all, which in turn
decides whether the field can be indexed, made unique, or used to filter, sort, search, and report. The
mode travels in the published payload, so switching it is classified as a behaviour change and has to
be confirmed before publication.

@since  2.0.0

- `Virtual`: Evaluated from the record's other values whenever it is read, and given no column of its own.

Having no column is what makes a virtual field unable to declare uniqueness, an index, or any
query capability: there is nothing for the database to look at.

@since  2.0.0

- `Stored`: Recomputed on write and persisted into its own column, so it can be queried and constrained like
an author-supplied field.

The price is portability across the supported engines, so the formula must resolve to a portable
scalar result type, and a decimal result must additionally declare its precision and scale.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\DefinitionOwner`

Who a business definition belongs to, and the handle namespace that ownership reserves.

Every entity definition and field type is named under its owner's namespace, and that is what keeps one
supplier from declaring an identifier that belongs to another: a package's contributions are checked
against `DefinitionOwner::extension()` while its manifest is parsed, and the administrator may only
save or retire definitions whose owner is the current site. The type fixes both the shape a valid
identifier takes and how the namespace is spelled, so construction refuses a pair that disagrees and no
unvalidated owner can reach the catalog.

@since  2.0.0

- `$type` (Kumwe\BusinessDefinition\Domain\DefinitionOwnerType): Readonly constructor state; see the constructor
  parameter contract.

- `$identifier` (string): Readonly constructor state; see the constructor parameter contract.

### `__construct(Kumwe\BusinessDefinition\Domain\DefinitionOwnerType $type, string $identifier): `

Pair an ownership kind with the identifier that names the owner.

@param   DefinitionOwnerType  $type        Kind of owner, which decides the shape required below.
@param   string               $identifier  `core` for the platform, a `vendor/package` name for an
         extension, or the site identifier for a site.

@throws  InvalidBusinessDefinition  When the identifier does not match the shape its type requires.

@since   2.0.0

### `core(): Kumwe\BusinessDefinition\Domain\DefinitionOwner`

Owner that definitions shipped with the platform itself are declared under.

@return  self  The core owner, whose namespace is `core`.

@since   2.0.0

### `extension(string $identifier): Kumwe\BusinessDefinition\Domain\DefinitionOwner`

Owner for definitions an installed package contributes.

@param   string  $identifier  Package name as `vendor/package`; trimmed and lowercased first, so a
         manifest's casing does not decide which namespace it reserves.

@return  self  The extension owner, whose namespace is the package name with `/` written as `.`.

@throws  InvalidBusinessDefinition  When the name is not two identifier segments joined by a slash.

@since   2.0.0

### `site(string $identifier): Kumwe\BusinessDefinition\Domain\DefinitionOwner`

Owner for definitions authored inside one site through the administrator.

@param   string  $identifier  Site identifier, trimmed and lowercased first.

@return  self  The site owner, whose namespace is `site.` followed by that identifier.

@throws  InvalidBusinessDefinition  When the identifier does not open with a letter or digit, holds
         a character outside `a-z0-9._-`, or runs past 191 characters.

@since   2.0.0

### `namespace(): string`

Prefix that every handle this owner declares has to sit under.

@return  string  `core` for the platform, the package name with `/` replaced by `.` for an
         extension, and `site.` followed by the identifier for a site.

@since   2.0.0

### `assertOwns(string $handle): void`

Refuse a handle that falls outside this owner's namespace.

This is the check that stops a package from claiming an identifier it does not own, so it runs over
every contributed entity handle and field-type identifier before a contribution set is accepted,
and again inside `EntityTypeDefinition` so a definition can never be built with a foreign handle.

@param   string  $handle  Namespaced definition or field-type handle to test.

@return  void

@throws  InvalidBusinessDefinition  When the handle does not open with this namespace and a dot.

@since   2.0.0

### `toArray(): array`

Export the owner in the shape the catalog stores and ownership comparisons run over.

Package synchronization decides whether a stored definition still belongs to the extension being
synchronized by comparing two of these arrays, so the shape is part of the persisted contract.

@return  array{type: string, identifier: string}  The owner type's backing string and the
         identifier exactly as it was validated.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\DefinitionOwnerType`

Which authority a business definition answers to, and with it the namespace its handles live under.

The type is stored beside the owner identifier on every catalog row and travels in the canonical
payload, and it settles more than provenance. `DefinitionOwner` derives the reserved handle prefix from
it and validates the identifier against a shape chosen by it; package synchronization only ever writes
or deprecates `Extension` rows; and the administrator saves drafts and changes version status for
`Site` definitions alone, because the other two are owned by whoever supplied them.

@since  2.0.0

- `Core`: Declared by the platform itself and shipped with the release, under the `core` namespace.

@since  2.0.0

- `Extension`: Contributed by an installed package, under the namespace its `vendor/package` name reserves.

Install and upgrade write these rows inside the extension transaction, so their publication status
follows the package lifecycle rather than an operator's lifecycle command: dropping a declaration
from a manifest deprecates the definition instead of erasing it.

@since  2.0.0

- `Site`: Authored inside one site through the administrator, under a `site.` prefixed namespace.

The only kind an operator edits directly, and the only kind whose versions may be superseded,
deprecated, or rejected on request.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\DefinitionStatus`

Lifecycle state of a business definition, from the editable draft to a withdrawn version.

Publication is the hinge. `Draft` describes the version-zero working copy an author saves against an
optimistic revision, and every other case describes a published version whose canonical bytes and
SHA-256 checksum are already immutable. That is why the later states are kept in a column beside the
version row rather than inside the payload: moving a version through them must never rewrite the bytes
the checksum was taken over. The transitions are correspondingly narrow — a draft may only be published
to a positive version, publication supersedes the version before it, and an explicit status change
accepts nothing but `Superseded`, `Deprecated`, and `Rejected`.

@since  2.0.0

- `Draft`: The editable working copy, pinned to definition version zero and never stored as a version row.

A draft is the one state an author writes into directly, and it is left by publishing rather than
by a status change; nothing brings a published definition back to it.

@since  2.0.0

- `Published`: A version whose canonical payload, checksum, dependency graph, and compatibility plan are recorded.

It stays the definition's head until a successor is published, at which point it is superseded.

@since  2.0.0

- `Superseded`: Displaced by a newer published version, and kept intact for history, audit, and restore.

Existing records built on it keep working; superseding says a newer contract exists, not that this
one has been withdrawn.

@since  2.0.0

- `Deprecated`: Still on record and still serviceable, but no longer something to build on.

Package synchronization deprecates the last published version of a definition an extension has
stopped declaring, which is how a removed declaration keeps its history rather than being erased.

@since  2.0.0

- `Rejected`: Withdrawn: the version stays on record, but the record runtime refuses to serve it.

Definition resolution treats a rejected version as a schema that has gone away and fails rather
than reading records against it, so this is the state that actually takes a contract out of use.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\DeleteBehavior`

What deleting the record on the target side of a relationship does to the association.

Which behaviour a relationship may declare is deliberately narrow: `Cascade` belongs to owned line
collections alone, `SetNull` to optional singular associations, and everything else keeps `Restrict`,
because an unqualified cascade between independent entities would remove records nobody asked about.
The one place the declared value reaches SQL directly is the target foreign key of a junction table.
A singular association lives in a column on the record table, and there the physical key is pinned to
`RESTRICT` whatever was declared, so `BusinessRecordService` can do the clearing itself and leave each
affected source record with a version bump, a revision, and audit evidence rather than have rows change
underneath the runtime.

@since  2.0.0

- `Restrict`: Refuse to delete a target while any record still points at it.

The default, and what a relationship keeps unless it qualifies for one of the other two.

@since  2.0.0

- `Cascade`: Remove the dependent rows along with the target they hang from.

Reserved for an owned line collection, whose lines have no existence apart from their owner and
whose line table already cascades from it structurally. The graph validator refuses this on every
other kind, and the record runtime refuses a delete whose cascade would reach an independent
entity without an explicit bounded delete workflow.

@since  2.0.0

- `SetNull`: Clear the association and leave the record that held it standing.

Only an optional singular relationship may declare it: there has to be one column to clear, and a
required association cannot be nulled away and still satisfy its own contract.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\DocumentViewDefinition`

Typed layout metadata a `document` view uses to render a record as a business document.

The block names which declared parts of the entity play which documentary role: the field whose value
is the human document number, labelled groups of meta fields, party relationships such as the billed
client, the owned-line collection rendered as the document body table, and the fields shown as the
totals block. Every reference is proven against the owning entity by `EntityTypeDefinition`, travels
inside the canonical checksummed definition bytes, and is policy-filtered by the surface catalog like
any other projection — declaring a role here never widens disclosure. Every role is optional, so a
document view may start as a bare header and grow as the entity does.

@since  2.0.0

- `$groups` (array): Labelled meta blocks of field handles, rendered between the header and the line table.

@var    list<array{label: string, fields: list<string>}>
@since  2.0.0

- `$parties` (array): Labelled party relationships, such as the account a document is billed or addressed to.

@var    list<array{label: string, relationship: string}>
@since  2.0.0

- `$totals` (array): Field handles rendered as the totals block, in declaration order.

@var    list<string>
@since  2.0.0

- `$identity` (?string): Readonly constructor state; see the constructor parameter contract.

- `$lines` (?string): Readonly constructor state; see the constructor parameter contract.

### `__construct(?string $identity = default, array $groups = default, array $parties = default, ?string $lines = default, array $totals = default): `

Declare the documentary roles, proving each list bounded and each handle well-formed.

@param   ?string                                           $identity  Field whose value is the human
         document number, or null to fall back to the entity label and record date.
@param   list<array{label: string, fields: list<string>}>  $groups    Labelled meta field groups.
@param   list<array{label: string, relationship: string}>  $parties   Labelled party relationships.
@param   ?string                                           $lines     Owned-line relationship rendered
         as the document body table, or null for a document without lines.
@param   list<string>                                      $totals    Fields shown as the totals block.

@throws  InvalidBusinessDefinition  When a list exceeds its ceiling, a label is blank or overlong, a
         group projects no field, a handle repeats within its list, or a handle is malformed.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\DocumentViewDefinition`

Rebuild the block from its canonical document, rejecting any property the contract does not name.

@param   array<string, mixed>  $document  Decoded document-view block keyed by canonical property name.

@return  self  The validated block, having passed the same invariants as direct construction.

@throws  InvalidBusinessDefinition  When the block carries an unknown property, a property of the wrong
         type, or values the constructor rejects.

@since   2.0.0

### `toArray(): array`

Export the block as the normalized document that joins the view's canonical bytes.

All five roles are always written, defaults included, so the block round-trips through `fromArray()`
unchanged and two authors declaring the same roles produce identical canonical bytes.

@return  array<string, mixed>  Identity, groups, parties, lines and totals under their canonical keys.

@since   2.0.0

### `fieldHandles(): array`

List every field handle the block references, for the owning entity's declaration checks.

@return  list<string>  Identity, group and totals handles in declaration order, repeats included.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\EntityTypeDefinition`

The complete, self-validating contract of one business entity: its identity, shape, behaviour, and reach.

This is the aggregate the whole business stack agrees on. An author edits it as a draft, publication freezes
it at a positive version whose canonical bytes and SHA-256 identify it from then on, the physical schema
compiler derives real tables from it, and the record runtime decodes every row against the version it was
written under. Construction settles everything one entity can answer for itself — identifier and handle
shapes, ownership of the namespace it declares under, bounded labels, a version that agrees with the status,
bounded and duplicate-free collections, exactly one field matching the identity strategy, acyclic expression
dependencies, views and actions that reference only declared fields and transitions, and no view claiming a
surface the entity does not expose. What needs the rest of the catalog — a registered field type, a reachable
relationship target, an acyclic ownership graph — is `BusinessDefinitionValidator`'s job.

Every state change is a new instance: `published()` and `withStatus()` rebuild through this constructor, so
no invariant can be escaped by transitioning around it.

@since  2.0.0

- `$id` (string): Readonly constructor state; see the constructor parameter contract.

- `$owner` (Kumwe\BusinessDefinition\Domain\DefinitionOwner): Readonly constructor state; see the constructor
  parameter contract.

- `$siteIdentifier` (string): Readonly constructor state; see the constructor parameter contract.

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$singularLabel` (string): Readonly constructor state; see the constructor parameter contract.

- `$pluralLabel` (string): Readonly constructor state; see the constructor parameter contract.

- `$status` (Kumwe\BusinessDefinition\Domain\DefinitionStatus): Readonly constructor state; see the constructor
  parameter contract.

- `$definitionVersion` (int): Readonly constructor state; see the constructor parameter contract.

- `$storageMode` (Kumwe\BusinessDefinition\Domain\StorageMode): Readonly constructor state; see the constructor
  parameter contract.

- `$identityStrategy` (Kumwe\BusinessDefinition\Domain\IdentityStrategy): Readonly constructor state; see the
  constructor parameter contract.

- `$scope` (Kumwe\BusinessDefinition\Domain\ScopeMode): Readonly constructor state; see the constructor parameter
  contract.

- `$auditEnabled` (bool): Readonly constructor state; see the constructor parameter contract.

- `$revisionsEnabled` (bool): Readonly constructor state; see the constructor parameter contract.

- `$workflow` (?Kumwe\BusinessDefinition\Domain\WorkflowBinding): Readonly constructor state; see the constructor
  parameter contract.

- `$administratorExposure` (bool): Readonly constructor state; see the constructor parameter contract.

- `$portalExposure` (bool): Readonly constructor state; see the constructor parameter contract.

- `$publicExposure` (bool): Readonly constructor state; see the constructor parameter contract.

- `$softDeleteEnabled` (bool): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $id, Kumwe\BusinessDefinition\Domain\DefinitionOwner $owner, string $siteIdentifier, string $handle, string $singularLabel, string $pluralLabel, Kumwe\BusinessDefinition\Domain\DefinitionStatus $status, int $definitionVersion, Kumwe\BusinessDefinition\Domain\StorageMode $storageMode, Kumwe\BusinessDefinition\Domain\IdentityStrategy $identityStrategy, Kumwe\BusinessDefinition\Domain\ScopeMode $scope, bool $auditEnabled, bool $revisionsEnabled, array $fields, array $relationships = default, array $views = default, array $actions = default, ?Kumwe\BusinessDefinition\Domain\WorkflowBinding $workflow = default, array $compatibilityMetadata = default, bool $administratorExposure = default, bool $portalExposure = default, bool $publicExposure = default, bool $softDeleteEnabled = default, array $recordInvariants = default, array $portalOperations = default, array $labelTranslations = default): `

Assemble an entity definition and refuse one that contradicts itself.

@param   string                           $id                     Canonical UUID identifying the
         definition across all of its versions.
@param   DefinitionOwner                  $owner                  Who declares it, and whose namespace
         the handle has to sit under.
@param   string                           $siteIdentifier         Site the definition belongs to.
@param   string                           $handle                 Namespaced, dot-separated entity
         handle, unique within the site.
@param   string                           $singularLabel          Operator-facing name for one record.
@param   string                           $pluralLabel            Operator-facing name for the
         collection.
@param   DefinitionStatus                 $status                 Lifecycle state; `Draft` is the only
         one that pairs with version zero.
@param   int                              $definitionVersion      Zero while a draft, positive once
         published.
@param   StorageMode                      $storageMode            How records are physically kept.
@param   IdentityStrategy                 $identityStrategy       Which identity field the entity must
         carry, and what the record key is.
@param   ScopeMode                        $scope                  Tenancy dimensions records are
         partitioned by.
@param   bool                             $auditEnabled           Audit policy for the entity's records;
         changing it is classified as behaviour-changing.
@param   bool                             $revisionsEnabled       Whether every record write also
         appends a revision row.
@param   list<FieldDefinition>            $fields                 At least one field and at most 256.
@param   list<RelationshipDefinition>     $relationships          At most 128 declared associations.
@param   list<ViewDefinition>             $views                  At most 64 projections.
@param   list<ActionDefinition>           $actions                At most 64 operations.
@param   ?WorkflowBinding                 $workflow               State machine records move through,
         or null when they have none.
@param   array<string, mixed>             $compatibilityMetadata  Declared intent; must be canonically
         encodable, since it travels into the checksum.
@param   bool                             $administratorExposure  Whether the administrator surface may
         serve the entity.
@param   bool                             $portalExposure         Whether the portal surface may; any
         portal view requires it.
@param   bool                             $publicExposure         Whether anonymous delivery may; any
         public view requires it.
@param   bool                             $softDeleteEnabled      Whether deletion marks a record rather
         than removing it.
@param   list<RecordInvariantDefinition>  $recordInvariants       Cross-field rules, handles unique.
@param   list<PortalOperation>            $portalOperations       Explicit portal operation allowlist;
         empty denies every generated business-record operation.
@param   array<string, mixed>             $labelTranslations      Translations of `singular_label` and
         `plural_label`, keyed by member then by locale tag; empty for an entity whose labels are
         declared in one language only.

@throws  InvalidBusinessDefinition  When the id is not a UUID, the site, handle, or labels are malformed,
         the handle falls outside the owner's namespace, the version disagrees with the status, a
         collection is empty or past its ceiling, a handle is duplicated, no exposure surface is
         declared, a portal operation is invalid or enabled without portal exposure, a view claims a
         surface the entity does not expose, the metadata is not canonically encodable, a label
         translation names an untranslatable member, a malformed locale or text over its bound, or
         the internal graph is unsound.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\EntityTypeDefinition`

Rebuild a definition from the canonical document `toArray()` writes.

This is the single entry for every stored or declared payload — a version row's canonical payload, an
extension manifest's contribution, a draft assembled by the administrator form mapper — so each of them
is put through the full constructor rather than trusted. An unrecognised top-level key is refused rather
than dropped, so a document exported by a later version is never imported with part of its meaning
silently missing.

@param   array<string, mixed>  $document  Canonical definition document, keyed as it is stored.

@return  self  The definition, with every construction rule already applied.

@throws  InvalidBusinessDefinition  When the document carries an unknown property, an owner that is not a
         strict object, a required property that is missing or of the wrong type, an enum-backed property
         naming no case, a member document that fails to parse, or an assembled definition that breaks a
         construction rule.

@since   2.0.0

### `singularLabelIn(Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the name for one record of this entity in the locale an operator is working in.

@param   LocaleTag|string  $locale  Locale the surface is rendering in.

@return  string  The closest translation the entity carries, otherwise the declared singular label.

@throws  \Kumwe\Localization\Domain\InvalidLocaleTag  When the locale is a malformed tag.

@since   2.0.0

### `pluralLabelIn(Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the name for a collection of these records in the locale an operator is working in.

@param   LocaleTag|string  $locale  Locale the surface is rendering in.

@return  string  The closest translation the entity carries, otherwise the declared plural label.

@throws  \Kumwe\Localization\Domain\InvalidLocaleTag  When the locale is a malformed tag.

@since   2.0.0

### `labelTranslations(): array`

Every translation the entity declares for its own labels.

@return  array<string, array<string, string>>  Locale-keyed text under `singular_label` and
         `plural_label`; empty for an entity declared in one language.

@since   2.0.0

### `fields(): array`

Field contract of the entity, in the order it was declared.

@return  list<FieldDefinition>  Never empty; construction requires at least one field.

@since   2.0.0

### `postingDateField(): ?Kumwe\BusinessDefinition\Domain\FieldDefinition`

The date field this definition declares as the posting date the temporal lock reads.

A definition opts into the posting-period mechanism by setting `posting_date: true` in the
configuration of exactly one date-carrying field; `BusinessDefinitionValidator` refuses a second
declaration. A definition that declares none returns null and is untouched by the whole
mechanism — no period is consulted and no mutation is refused.

@return  ?FieldDefinition  The declared posting-date field, or null when the definition makes no
         declaration.

@since   2.0.0

### `relationships(): array`

Associations the definition declares explicitly, in the order they were declared.

Ordered-line fields are not folded in here; `runtimeRelationship()` is the lookup that sees both.

@return  list<RelationshipDefinition>  Empty when the entity reaches no other entity by a declared
         association.

@since   2.0.0

### `runtimeRelationship(string $handle): ?Kumwe\BusinessDefinition\Domain\RelationshipDefinition`

Resolves both explicit relationships and the legacy field-shaped ordered-line contract.

Ordered lines are always an owned, ordered collection whose lifecycle follows its owner, so a matching
`core.ordered_lines` field is answered with a relationship synthesized on the spot: owned-line kind,
ordered, cascading on delete, and pointing at the entity named in the field's `target` configuration.
This is what lets the record repositories treat a legacy line-item field and a declared association
through one code path. Declared relationships are searched first, and construction already refuses an
ordered-line field that shares a handle with one, so the two can never disagree.

@param   string  $handle  Relationship handle, or the handle of an ordered-line field, to resolve.

@return  ?RelationshipDefinition  The association, or null when the handle names neither.

@throws  InvalidBusinessDefinition  When a matching ordered-line field declares no string `target`, or
         names one that is not a valid entity handle.

@since   2.0.0

### `views(): array`

Named projections declared on the entity, in the order they were declared.

@return  list<ViewDefinition>  Empty when the entity offers no view.

@since   2.0.0

### `actions(): array`

Operations declared on the entity, in the order they were declared.

@return  list<ActionDefinition>  Empty when the entity offers no action beyond plain record writes.

@since   2.0.0

### `recordInvariants(): array`

Cross-field rules the record runtime evaluates on every create and update.

@return  list<RecordInvariantDefinition>  Empty when the entity states no rule spanning several fields.

@since   2.0.0

### `invariantLineDependencies(): array`

Name every owned-line collection the entity's invariants reduce, and the line fields they read.

This is what a write path asks before it commits: an empty map means no invariant looks past the
header, so nothing extra has to be gathered, and a non-empty one is the exact, bounded set of
collections and line fields the command must prepare for the rules to be judged once. Construction
has already proven every key is a declared owned-line collection of this entity.

@return  array<string, list<string>>  Line field handles keyed by owned-line relationship handle,
         each list sorted and deduplicated; empty when no invariant aggregates.

@since   2.0.0

### `portalOperations(): array`

Business-record operations explicitly enabled for the authenticated portal surface.

Entity-level portal exposure is only the outer surface switch. An operation absent from this list
remains denied even when a portal view or action describes how it could be presented.

@return  list<PortalOperation>  Operations in canonical backing-value order; empty denies all.

@since   2.0.0

### `allowsPortalOperation(Kumwe\BusinessDefinition\Domain\PortalOperation $operation): bool`

Decide whether one business-record operation was explicitly opted into the portal.

@param   PortalOperation  $operation  Closed operation to test.

@return  bool  True only when portal exposure is enabled and the exact operation is allowlisted.

@since   2.0.0

### `compatibilityMetadata(): array`

Declared intent the definition carries for consumers that read it back out of the payload.

@return  array<string, mixed>  Exactly as declared; `SchemaEvolutionHints::fromDefinition()` is the only
         reader today, and it takes just its four evolution families out of the map.

@since   2.0.0

### `published(int $version): Kumwe\BusinessDefinition\Domain\EntityTypeDefinition`

Advance a draft to a published version, carrying every other property across unchanged.

Publication is the only way out of `Draft`, and this instance — not the draft — is what the canonical
payload and its checksum are taken over. That is why the compatibility analyzer advances a draft to its
next version before diffing it against the published head, rather than comparing draft bytes to
published ones.

@param   int  $version  Version number to publish as; must be one or greater.

@return  self  A copy carrying `Published` status and that version.

@throws  InvalidBusinessDefinition  When this definition is not a draft, or the version is below one.

@since   2.0.0

### `withStatus(Kumwe\BusinessDefinition\Domain\DefinitionStatus $status): Kumwe\BusinessDefinition\Domain\EntityTypeDefinition`

Carry an already-published definition on to a later lifecycle status.

Nothing brings a definition back to `Draft`, and a version-zero definition has no published lifecycle to
move through, so both are refused. Note that status sits inside the canonical payload, so the copy
checksums differently from the original: a stored version's status change is recorded in its version row
instead, which is what keeps the bytes a published checksum was taken over immutable.

@param   DefinitionStatus  $status  Status to carry; any case except `Draft`.

@return  self  A copy with the new status and every other property unchanged.

@throws  InvalidBusinessDefinition  When `Draft` is requested, or this definition has no positive version.

@since   2.0.0

### `toArray(): array`

Export the definition as the canonical document it is stored, compared, and checksummed as.

The exact shape is part of the persisted contract, which is why four members are written only when they
carry meaning: `soft_delete_enabled`, `record_invariants`, `portal_operations` and `label_translations`
are left out when unset, so definitions published before those properties existed still serialize to the
bytes their stored checksum was taken over. Everything else is always present, with nested definitions
already exported in declaration order.

@return  array<string, mixed>  Every property under its stored key, with enums written as their backing
         strings and `workflow` written as null when the entity binds none.

@since   2.0.0

### `checksum(): string`

Fingerprint of the canonical document, which is how a published version is identified and re-verified.

Schema planning, execution, and package synchronization compare this value rather than the document, so
a definition that has drifted from the one a plan was built against is caught before any DDL runs.

@return  string  Lowercase hexadecimal SHA-256 of the canonical encoding, 64 characters wide.

@throws  InvalidBusinessDefinition  When the assembled document is not canonically encodable — a case
         construction leaves open only for a member validated one nesting level shallower than it sits
         here, such as compatibility metadata at the depth ceiling.

@since   2.0.0

### `dependencyGraph(): array`

Reduce the definition to the three dependency sets the catalog indexes and closes a definition set by.

The repository stores these as dependency rows beside the version. Publication and package
synchronization both walk `entities` to close a definition over the entities it reaches, so nothing is
validated or published against a target the catalog cannot produce. Every set is deduplicated and
sorted, which is what makes two definitions declaring the same dependencies produce identical rows.

@return  array{fields: array<string, list<string>>, entities: list<string>, field_types: list<string>}
         `fields` maps each field handle to the handles its formula, visibility, and editability
         conditions read; `entities` names every entity reached by a declared relationship or by an
         entity-reference or ordered-lines field; `field_types` names every field type in use.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\Expression`

Validated expression tree behind every business-definition condition and computed-field formula.

A definition document carries its conditions and formulas as nested JSON objects, and `fromArray()` is
the only way to turn one into this type: it checks the operator vocabulary, the arity, the declared
result type, and the type agreement between an operator and its arguments, while bounding nesting
depth, node count, and canonical byte size. Holding an instance therefore means the tree has already
been proven well formed and bounded, which is what lets `RecordInvariantDefinition`, `FieldDefinition`,
`ActionDefinition`, and the persisted schema backfill and transform states use it without re-checking
anything. `ExpressionEvaluator` computes the value, `dependencies()` names the field handles a caller
must supply first, and `toArray()` returns the same document shape so a tree survives a definition
checksum or a stored migration state and comes back identical.

One leaf reaches past the record's own fields. `line_aggregate` reduces an owned-line collection to a
single value — the count of its lines, or the sum of one line field — so a record invariant can state
the most fundamental document rule there is, that a header total agrees with its lines. It is
deliberately the narrowest thing that expresses that: one declared collection, one closed reduction,
one line field, inside the same byte, node and depth budget every other tree lives under.
`lineDependencies()` names what a caller must gather, and it is the caller's job to hand over the whole
prepared collection so the rule is judged once for the document rather than once per line.

@since  2.0.0

- `$operator` (string): Readonly constructor state; see the constructor parameter contract.

- `$type` (string): Readonly constructor state; see the constructor parameter contract.

- `$literal` (mixed): Readonly constructor state; see the constructor parameter contract.

- `$field` (?string): Readonly constructor state; see the constructor parameter contract.

- `$scale` (?int): Readonly constructor state; see the constructor parameter contract.

- `$lines` (?string): Readonly constructor state; see the constructor parameter contract.

- `$aggregate` (?string): Readonly constructor state; see the constructor parameter contract.

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\Expression`

Parse a canonical condition or formula document into a validated expression tree.

The document is measured against the byte budget before it is walked, and the node budget is
re-checked once the walk finishes, so an oversized definition is refused rather than parsed.

@param   array<string, mixed>  $document  Root expression object as it appears in the definition.

@return  self  Root node of the parsed tree.

@throws  InvalidBusinessDefinition  When the document holds a value the canonical encoder refuses,
         such as a float, when its encoding passes 32768 bytes, when the tree passes 128 nodes or
         12 levels, or when any node has an unsupported operator, type, shape, or arity.

@since   2.0.0

### `arguments(): array`

Return the operand nodes this expression applies its operator to.

@return  list<Expression>  Child nodes in evaluation order; empty for a `literal` or `field` leaf.

@since   2.0.0

### `dependencies(): array`

Name the field handles the whole tree reads, so a caller can gather the values before evaluating.

Callers depend on the deduplicated, sorted form: the schema gateway compares this list key for key
against the dependency column map it assembled for a backfill, and the record validator uses it to
decide when a computed field has everything it needs to be evaluated.

@return  list<string>  Field handles in ascending string order, each appearing once; empty when
         the tree reads no field at all.

@since   2.0.0

### `lineDependencies(): array`

Name the owned-line collections this tree reduces over, and which line field each reduction reads.

This is the counterpart of `dependencies()` for the collection half of a document rule. A caller
gathers the header's own values from `dependencies()` and the lines from here, so the whole rule is
evaluated once over a prepared line set rather than once per line. A `count` reduction contributes
its relationship handle with no field, because it measures the collection rather than a value in it.

@return  array<string, list<string>>  Line field handles keyed by owned-line relationship handle,
         each list deduplicated and in ascending string order and possibly empty; the map itself is
         empty when the tree reduces over nothing, which is true of every tree written before this
         leaf existed.

@since   2.0.0

### `toArray(): array`

Render the tree back into the canonical document a definition stores and checksums.

The output is accepted by `fromArray()` unchanged, which is what lets a schema plan carry an
expression inside its persisted state and rebuild the same tree in a later request.

@return  array<string, mixed>  Node keyed by `op` and `type`, plus `value` for a literal, `field`
         for a field reference, `lines` and `aggregate` — and `field` where the reduction folds
         one — for a line aggregation, or nested `args` for an operator, and `scale` where one is set.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\FieldDefinition`

One field of a business entity's contract, validated the moment it is constructed.

A field is the unit the rest of the business stack agrees on: the schema compiler turns it into a
column with its indexes, `RecordValueCodec` normalizes values through it, `RecordRuleValidator`
enforces its required, immutable, and read-only rules on every write, and the compatibility analyzer
diffs two of them to classify what a new version does to stored data. Construction refuses a field
that contradicts itself — required and nullable at once, computed without a formula, an encrypted
secret that claims to be searchable — and canonicalizes what the published checksum is taken over, so
that two fields declaring the same thing serialize to the same bytes. Rules needing the wider graph,
such as whether the declared type is registered or a referenced entity is reachable, belong to
`BusinessDefinitionValidator` instead.

@since  2.0.0

- `$normalizers` (array): Normalizer identifiers applied to a submitted value, in declared order, before validation.

@var    list<string>
@since  2.0.0

- `$validators` (array): Validation rules run after normalization, each a `rule` document with the rule's own
  arguments.

@var    list<array<string, mixed>>
@since  2.0.0

- `$placements` (array): Surfaces the field is rendered on: `list`, `detail`, `form`, `history`, or `relation`.

Stored deduplicated and sorted, so declaring the same surfaces in another order yields one
canonical document.

@var    list<string>
@since  2.0.0

- `$configuration` (array): Type-specific settings, keyed by name and sorted so the canonical document is
  deterministic.

Which keys are meaningful belongs to the declared field type; `BusinessDefinitionValidator`
rejects any key that type does not register.

@var    array<string, scalar|list<scalar|null>|null>
@since  2.0.0

- `$textTranslations` (array): Translations of the field's operator-facing wording, keyed by member then by locale
  tag.

Empty for a field declared in one language, which is what keeps such a field's canonical document
— and therefore the checksum of every published version carrying it — exactly as it was before the
locale dimension existed.

@var    array<string, array<string, string>>
@since  2.0.0

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$label` (string): Readonly constructor state; see the constructor parameter contract.

- `$type` (string): Readonly constructor state; see the constructor parameter contract.

- `$description` (string): Readonly constructor state; see the constructor parameter contract.

- `$required` (bool): Readonly constructor state; see the constructor parameter contract.

- `$nullable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$default` (mixed): Readonly constructor state; see the constructor parameter contract.

- `$length` (?int): Readonly constructor state; see the constructor parameter contract.

- `$precision` (?int): Readonly constructor state; see the constructor parameter contract.

- `$scale` (?int): Readonly constructor state; see the constructor parameter contract.

- `$unique` (bool): Readonly constructor state; see the constructor parameter contract.

- `$indexed` (bool): Readonly constructor state; see the constructor parameter contract.

- `$immutableAfterCreate` (bool): Readonly constructor state; see the constructor parameter contract.

- `$serverOnly` (bool): Readonly constructor state; see the constructor parameter contract.

- `$computed` (bool): Readonly constructor state; see the constructor parameter contract.

- `$readOnly` (bool): Readonly constructor state; see the constructor parameter contract.

- `$createVisible` (bool): Readonly constructor state; see the constructor parameter contract.

- `$updateVisible` (bool): Readonly constructor state; see the constructor parameter contract.

- `$readVisible` (bool): Readonly constructor state; see the constructor parameter contract.

- `$searchable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$filterable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$sortable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$reportable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$exportable` (bool): Readonly constructor state; see the constructor parameter contract.

- `$sensitivity` (Kumwe\BusinessDefinition\Domain\Sensitivity): Readonly constructor state; see the constructor
  parameter contract.

- `$localized` (bool): Readonly constructor state; see the constructor parameter contract.

- `$helpText` (string): Readonly constructor state; see the constructor parameter contract.

- `$formGroup` (string): Readonly constructor state; see the constructor parameter contract.

- `$order` (int): Readonly constructor state; see the constructor parameter contract.

- `$visibilityCondition` (?Kumwe\BusinessDefinition\Domain\Expression): Readonly constructor state; see the
  constructor parameter contract.

- `$editabilityCondition` (?Kumwe\BusinessDefinition\Domain\Expression): Readonly constructor state; see the
  constructor parameter contract.

- `$formula` (?Kumwe\BusinessDefinition\Domain\Expression): Readonly constructor state; see the constructor parameter
  contract.

- `$computationMode` (Kumwe\BusinessDefinition\Domain\ComputationMode): Readonly constructor state; see the
  constructor parameter contract.

### `__construct(string $handle, string $label, string $type, string $description = default, bool $required = default, bool $nullable = default, mixed $default = default, ?int $length = default, ?int $precision = default, ?int $scale = default, array $configuration = default, array $normalizers = default, array $validators = default, bool $unique = default, bool $indexed = default, bool $immutableAfterCreate = default, bool $serverOnly = default, bool $computed = default, bool $readOnly = default, bool $createVisible = default, bool $updateVisible = default, bool $readVisible = default, bool $searchable = default, bool $filterable = default, bool $sortable = default, bool $reportable = default, bool $exportable = default, Kumwe\BusinessDefinition\Domain\Sensitivity $sensitivity = default, bool $localized = default, string $helpText = default, string $formGroup = default, int $order = default, array $placements = default, ?Kumwe\BusinessDefinition\Domain\Expression $visibilityCondition = default, ?Kumwe\BusinessDefinition\Domain\Expression $editabilityCondition = default, ?Kumwe\BusinessDefinition\Domain\Expression $formula = default, Kumwe\BusinessDefinition\Domain\ComputationMode $computationMode = default, array $textTranslations = default): `

Capture a field declaration and reject one the runtime could not honour.

These rules are settled once, at construction, so every consumer downstream may treat a field it
is handed as internally consistent and already canonicalized.

@param   string                                        $handle                Stable snake_case field identifier.
@param   string                                        $label                 Operator-facing name for the field.
@param   string                                        $type                  Namespaced field-type identifier.
@param   string                                        $description           Editor-facing note on its purpose.
@param   bool                                          $required              Whether a value must be supplied.
@param   bool                                          $nullable              Whether null is an accepted value.
@param   mixed                                         $default               Value used when none is supplied.
@param   ?int                                          $length                Bounded character length, or null.
@param   ?int                                          $precision             Total digits of an exact numeric.
@param   ?int                                          $scale                 Fractional digits of the numeric.
@param   array<string, scalar|list<scalar|null>|null>  $configuration         Type-specific settings by key.
@param   list<string>                                  $normalizers           Normalizer identifiers, in order.
@param   list<array<string, mixed>>                    $validators            Validation rules to apply.
@param   bool                                          $unique                Whether values must be unique.
@param   bool                                          $indexed               Whether storage carries an index.
@param   bool                                          $immutableAfterCreate  Whether updates may not change it.
@param   bool                                          $serverOnly            Whether no caller may supply it.
@param   bool                                          $computed              Whether the server derives it.
@param   bool                                          $readOnly              Whether callers may not write it.
@param   bool                                          $createVisible         Whether create surfaces expose it.
@param   bool                                          $updateVisible         Whether update surfaces expose it.
@param   bool                                          $readVisible           Whether reads may return the value.
@param   bool                                          $searchable            Whether search may target it.
@param   bool                                          $filterable            Whether queries may filter on it.
@param   bool                                          $sortable              Whether queries may sort on it.
@param   bool                                          $reportable            Whether aggregates may cover it.
@param   bool                                          $exportable            Whether exports may include it.
@param   Sensitivity                                   $sensitivity           Handling class for redaction.
@param   bool                                          $localized             Whether the value is translated.
@param   string                                        $helpText              Short hint rendered by the form.
@param   string                                        $formGroup             Form section the field sits in.
@param   int                                           $order                 Sort weight within that group.
@param   list<string>                                  $placements            Surfaces the field renders on.
@param   ?Expression                                   $visibilityCondition   Condition gating display.
@param   ?Expression                                   $editabilityCondition  Condition gating edits.
@param   ?Expression                                   $formula               Expression deriving the value.
@param   ComputationMode                               $computationMode       Whether the result is stored.
@param   array<string, mixed>                          $textTranslations      Translations of `label`,
         `description` and `help_text`, keyed by member then by locale tag; empty for a field whose
         wording is declared in one language only.

@throws  InvalidBusinessDefinition  When an identifier, label, or numeric bound is malformed, a
         combination of flags contradicts itself, the default or the configuration is not
         canonically serializable, a computed field is missing the formula and the server-only and
         read-only rules it needs, the normalizer or validator lists are over length or repeat an
         entry, the placements are empty or name a surface that does not exist, or a translation
         names an untranslatable member, a malformed locale or text over its member's bound.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\FieldDefinition`

Rebuild a field from the canonical document `toArray()` writes.

The keys are the snake_case ones a published definition stores, and an unknown key is refused
rather than ignored, so a definition exported by a later release is never imported with part of
its meaning quietly dropped.

@param   array<string, mixed>  $document  Canonical field document, keyed as it is stored.

@return  self  The field, with every construction rule already applied.

@throws  InvalidBusinessDefinition  When a key is unknown, a member has the wrong type, the
         sensitivity or computation mode is unrecognised, or the resulting field breaks a
         construction rule.

@since   2.0.0

### `labelIn(Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the field's name in the locale an operator is working in.

@param   LocaleTag|string  $locale  Locale the surface is rendering in.

@return  string  The closest translation the field carries, otherwise the declared label.

@throws  \Kumwe\Localization\Domain\InvalidLocaleTag  When the locale is a malformed tag.

@since   2.0.0

### `descriptionIn(Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the field's editor-facing note in the locale an operator is working in.

@param   LocaleTag|string  $locale  Locale the surface is rendering in.

@return  string  The closest translation the field carries, otherwise the declared description,
         which is the empty string when the field declares none.

@throws  \Kumwe\Localization\Domain\InvalidLocaleTag  When the locale is a malformed tag.

@since   2.0.0

### `helpTextIn(Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the field's form hint in the locale an operator is working in.

@param   LocaleTag|string  $locale  Locale the surface is rendering in.

@return  string  The closest translation the field carries, otherwise the declared help text, which
         is the empty string when the field declares none.

@throws  \Kumwe\Localization\Domain\InvalidLocaleTag  When the locale is a malformed tag.

@since   2.0.0

### `toArray(): array`

Export the field as the canonical document the definition checksum is taken over.

`computation_mode` is written only for a stored computation, because a definition published
before that key existed described a virtual one implicitly and must keep its original bytes.
`text_translations` follows the same rule for the same reason: a field whose wording is declared
in one language writes nothing, so its bytes are the bytes its checksum was taken over.

@return  array<string, mixed>  Every declared property under its snake_case key, with enums and
         expressions flattened to their own document form.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\FieldTypeDefinition`

One field type a business field may declare, pairing the value family with its storage family.

Field types are the vocabulary `FieldDefinition::$type` draws from: `BuiltInFieldTypes` supplies the
`core.*` set and a schema-2 package contributes its own under its extension namespace. Construction
fixes the two halves that have to agree — the logical family a caller sees and the physical family the
schema compiler emits a column for — and refuses a pairing no conversion could serve, so an
unstorable type is rejected when it is declared rather than when a table is built. The configuration
keys are the closed set a field of this type may set; `BusinessDefinitionValidator` rejects any key
outside it. A published identifier is pinned to the bytes it shipped with, so an extension may revise
its types only by declaring new identifiers.

@since  2.0.0

- `$id` (string): Readonly constructor state; see the constructor parameter contract.

- `$label` (string): Readonly constructor state; see the constructor parameter contract.

- `$description` (string): Readonly constructor state; see the constructor parameter contract.

- `$valueType` (string): Readonly constructor state; see the constructor parameter contract.

- `$storageType` (string): Readonly constructor state; see the constructor parameter contract.

- `$configurationKeys` (array): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $id, string $label, string $description, string $valueType, string $storageType, array $configurationKeys = default): `

Declare a field type and reject a value and storage pairing no conversion could serve.

@param   string        $id                 Namespaced identifier fields declare, such as `core.text`.
@param   string        $label              Operator-facing name shown when choosing a type.
@param   string        $description        Short explanation shown beside the label.
@param   string        $valueType          Logical family callers exchange, such as `string`.
@param   string        $storageType        Physical family a column is emitted in, such as `json`.
@param   list<string>  $configurationKeys  The only configuration keys a field of this type may set.

@throws  InvalidBusinessDefinition  When the identifier is not namespaced, the metadata is empty or
         oversized, either family is unsupported, the two families cannot be converted into one
         another, or the configuration keys are duplicated, unbounded, or malformed.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\FieldTypeDefinition`

Rebuild a field type from the canonical document `toArray()` writes.

This is the boundary a package declaration and a stored field-type row both come through, so an
unknown key is refused rather than dropped: the document's bytes are the contract an existing
identifier is pinned to.

@param   array<string, mixed>  $document  Canonical field-type document, keyed as it is stored.

@return  self  The field type, with every construction rule already applied.

@throws  InvalidBusinessDefinition  When a key is unknown, the configuration keys are not a list of
         strings, or the resulting type breaks a construction rule.

@since   2.0.0

### `toArray(): array`

Export the field type as the document its published bytes are compared against.

@return  array<string, mixed>  Identifier, labels, both families, and the configuration keys under
         their snake_case keys.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\IdentityStrategy`

How a business entity addresses its records, and which identity field its definition must carry.

A definition declares exactly one strategy and must contain exactly one field of the matching type —
`core.uuid` or `core.reference_identity` — which `BusinessDefinitionValidator` then requires to be
required, non-null, unique, and immutable after creation. The choice reaches further than validation:
the schema compiler and `RecordValueCodec` read it to decide whether the identity a caller sees is
also the physical record key or a separate column beside a surrogate one.

@since  2.0.0

- `Uuid`: Records are identified by a canonical UUID that doubles as the runtime record key.

The identity field is not stored a second time: the compiler skips a column for it and the codec
fills the value back in from the record key when a row is read.

@since  2.0.0

- `Reference`: Records are identified by a validated external reference held in a column of its own.

The runtime still allocates a separate UUIDv7 record key on create, so a business reference can be
the operator-facing identity without becoming the key every relationship points at.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition`

Signals that a submitted business definition breaks the definition contract.

Every value object in this namespace validates itself as it is constructed and every one of them
raises this single type, so an assembler of definitions — the graphical form mapper, a definition
import, an extension contribution, the expression parser, or the runtime evaluator — has one class to
catch whatever rule was broken. It extends `InvalidArgumentException` because a rejected definition is
bad caller input rather than a failure of the installation, which is also what lets
`RecordRuleValidator` turn an unevaluatable invariant into a validation violation instead of a fault.
Messages name the rule that failed and stay operator-facing.

@since  2.0.0

## `Kumwe\BusinessDefinition\Domain\LocalizedDefinitionText`

The locale dimension on a business definition's operator-facing wording, and the rules it obeys.

`EntityTypeDefinition`'s singular and plural labels and `FieldDefinition`'s label, description and
help text are the only strings in a definition an operator reads, and they sit inside the document a
published version is checksummed over. A published version is immutable, so the dimension could not
be added later without migrating live documents — which is why it exists before the first extension
publishes, and why it is shaped so that a definition that does not use it is byte-identical to the
one it was before. That is the whole reason translations are held in a member of their own, written
into the document only when non-empty, rather than by widening each label into an object.

The declared text stays exactly where it was. A translation never replaces it; it stands beside it,
and the declared text is the last fallback, so a definition always has wording to show in every
locale. Resolution walks the requested locale's own fallback chain first — `pt-BR`, then `pt` — which
is the same chain the message catalogues are resolved through, so a definition label and the
interface around it agree about what "close enough" means.

@since  2.0.0

- `MAXIMUM_LOCALES`: Locales one member of one definition may be translated into.

The ceiling is the same order as the rest of a definition's collection bounds, and it exists for
the same reason: the document travels into a checksum and a stored row, so nothing in it may be
unbounded.

@var    int
@since  2.0.0

### `normalize(array $translations, array $members): array`

Validate and canonicalize a member-to-locale-to-text map declared on a definition.

Locale keys are normalised through `LocaleTag`, so `pt_br` and `PT-BR` cannot both appear as
separate translations of the same thing, and both member names and locale tags are sorted, so two
authors declaring the same translations in a different order produce one document and one
checksum. A member whose map is empty is dropped entirely rather than written as an empty object,
which is what keeps an untranslated definition's bytes unchanged.

@param   array<string, mixed>  $translations  Declared map, keyed by member name then locale tag.
@param   array<string, int>    $members       Translatable member names and the byte bound each one
         shares with the declared text it stands beside.

@return  array<string, array<string, string>>  The map with locales normalised, members and locales
         sorted, and empty members removed; empty when nothing is translated.

@throws  InvalidBusinessDefinition  When a member is not translatable, a map is not an object, a
         locale tag is malformed or duplicates another tag after normalization, a translation is
         blank or over its member's bound, or a member declares more than 64 locales.

@since   2.0.0

### `resolve(array $translations, string $member, string $declared, Kumwe\Localization\Domain\LocaleTag|string $locale): string`

Read the wording one member should be shown in, falling back to the declared text.

@param   array<string, array<string, string>>  $translations  Normalised translations of the whole
         definition member set.
@param   string                                $member        Member being rendered, such as `label`.
@param   string                                $declared      Text the definition declares, used when
         the locale and its fallbacks carry no translation.
@param   LocaleTag|string                      $locale        Locale the operator is reading in.

@return  string  The translation for the closest locale the member carries, otherwise the declared
         text — never an empty string and never the member's name.

@throws  InvalidLocaleTag  When the locale is given as a malformed tag.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\PortalOperation`

Closed set of business-record operations an entity may explicitly expose through the portal.

The values name generated surface operations rather than capabilities. This distinction is deliberate:
sharing an underlying capability never lets an enabled relation editor silently enable reordering, or
an enabled action silently enable approval requests. Every portal operation is independently opted in.

@since  2.0.0

- `Action`: Execute a definition-declared action, including an action wrapped in an approval request.

@since  2.0.0

- `Approval`: Request or inspect maker-checker approval for an action.

@since  2.0.0

- `Archive`: Mark a live record as archived.

@since  2.0.0

- `Browse`: Browse a bounded, policy-filtered record collection.

@since  2.0.0

- `Create`: Create one record through its definition contract.

@since  2.0.0

- `Delete`: Delete one record under its declared lifecycle behavior.

@since  2.0.0

- `Export`: Export an authorized record projection.

@since  2.0.0

- `History`: Read immutable revision history for one record.

@since  2.0.0

- `Read`: Read one policy-filtered record detail.

@since  2.0.0

- `Relation`: Read, add, or remove a declared relationship.

@since  2.0.0

- `Reorder`: Replace the order of an explicitly ordered relationship.

@since  2.0.0

- `Report`: Run an authorized report or aggregate projection.

@since  2.0.0

- `Restore`: Restore an archived or soft-deleted record when its lifecycle permits it.

@since  2.0.0

- `Status`: Inspect one caller-bound generated operation outcome.

@since  2.0.0

- `Update`: Apply an optimistic, validated patch to one record.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\RecordInvariantDefinition`

A named rule spanning several fields of a record — and, where it says so, its owned lines.

Field-level rules judge one value at a time; an invariant is how a definition states a rule that only
makes sense across values — an end date after its start, a total agreeing with its lines.
`RecordRuleValidator` evaluates every invariant on create and update and reports a failing one as a
violation keyed by this handle carrying this message, so the operator-facing wording lives in the
definition rather than in the runtime. The condition is a typed `Expression`, never executable code,
which is what allows an untrusted definition to declare a rule at all.

A condition carrying an `Expression` line aggregation reads the whole owned-line collection rather than
the header alone, which is what makes "the total agrees with its lines" an expressible rule rather than
an aspiration. `lineDependencies()` names the collections a caller has to gather first, and
`isSatisfied()` refuses to judge a rule whose collection was not supplied — so an aggregate rule is
never quietly reported as satisfied by a caller that did not read the lines.

@since  2.0.0

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$message` (string): Readonly constructor state; see the constructor parameter contract.

- `$condition` (Kumwe\BusinessDefinition\Domain\Expression): Readonly constructor state; see the constructor parameter
  contract.

### `__construct(string $handle, string $message, Kumwe\BusinessDefinition\Domain\Expression $condition): `

Capture an invariant and reject one that could never be evaluated as a rule.

@param   string      $handle     Stable snake_case name reported with the violation.
@param   string      $message    Operator-facing text shown when the rule fails, up to 500 bytes.
@param   Expression  $condition  Boolean-typed condition read over the record's field values, and
         over an owned-line collection wherever it carries a line aggregation.

@throws  InvalidBusinessDefinition  When the handle or message is malformed, or the condition is
         not boolean-typed.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\RecordInvariantDefinition`

Rebuild an invariant from the canonical document `toArray()` writes.

An unrecognised key is refused rather than dropped, so an invariant exported by a later version is
never silently imported with part of its meaning missing.

@param   array<string, mixed>  $document  Canonical invariant document, keyed as it is stored.

@return  self  The invariant, with every construction rule already applied.

@throws  InvalidBusinessDefinition  When the document carries an unknown key, a member of the
         wrong shape, or a condition that fails to parse.

@since   2.0.0

### `lineDependencies(): array`

Name the owned-line collections this invariant reduces, and the line field each reduction reads.

A caller uses this to decide what it must gather before judging the rule: an empty map means the
invariant reads the header alone and needs nothing else, and a non-empty one names every collection
`isSatisfied()` will insist on.

@return  array<string, list<string>>  Line field handles keyed by owned-line relationship handle,
         each list sorted and deduplicated; empty for a rule that spans the header's fields only.

@since   2.0.0

### `toArray(): array`

Export the invariant as the document the definition checksum is taken over.

@return  array{handle: string, message: string, condition: array<string, mixed>}  The invariant
         with its condition rendered as a nested expression document.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\RelationshipDefinition`

One declared association between business entities, validated the moment it is constructed.

Relationships are the part of an entity contract that reaches another entity's table: the schema
compiler turns a singular kind into a target column on the owning record table and a collection kind
into a junction or owned-line table, and the delete behaviour becomes the foreign key's action when
the target row is removed. Construction settles only what this one relationship can answer for
itself — that a required association cannot be nulled away by a delete, that an owned line collection
cascades from its owner, and that ordering is reserved for collections. Whether the target exists, is
in the same site and scope, and names a reciprocal inverse is `BusinessDefinitionValidator`'s job,
because those answers need the rest of the graph.

@since  2.0.0

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$label` (string): Readonly constructor state; see the constructor parameter contract.

- `$kind` (Kumwe\BusinessDefinition\Domain\RelationshipKind): Readonly constructor state; see the constructor
  parameter contract.

- `$target` (string): Readonly constructor state; see the constructor parameter contract.

- `$inverse` (?string): Readonly constructor state; see the constructor parameter contract.

- `$required` (bool): Readonly constructor state; see the constructor parameter contract.

- `$unique` (bool): Readonly constructor state; see the constructor parameter contract.

- `$ordered` (bool): Readonly constructor state; see the constructor parameter contract.

- `$onDelete` (Kumwe\BusinessDefinition\Domain\DeleteBehavior): Readonly constructor state; see the constructor
  parameter contract.

### `__construct(string $handle, string $label, Kumwe\BusinessDefinition\Domain\RelationshipKind $kind, string $target, ?string $inverse = default, bool $required = default, bool $unique = default, bool $ordered = default, Kumwe\BusinessDefinition\Domain\DeleteBehavior $onDelete = default): `

Capture an association and reject a combination the runtime could not honour.

@param   string            $handle    Stable snake_case name of this side of the association.
@param   string            $label     Operator-facing name for the association.
@param   RelationshipKind  $kind      Cardinality, and with it the storage the compiler emits.
@param   string            $target    Namespaced handle of the entity on the other side.
@param   ?string           $inverse   Handle of the reciprocal relationship on the target, or null
         when this side is declared alone.
@param   bool              $required  Whether a record must always name a target.
@param   bool              $unique    Whether a target may be claimed by one source only.
@param   bool              $ordered   Whether collection members carry a caller-visible position.
@param   DeleteBehavior    $onDelete  What deleting the target does to the association.

@throws  InvalidBusinessDefinition  When an identifier is malformed, a required association would be
         set to null on delete, an owned line collection does not cascade, or a singular
         relationship claims ordering.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\RelationshipDefinition`

Rebuild a relationship from the canonical document `toArray()` writes.

Unknown keys are refused rather than ignored, and an unrecognised kind or delete behaviour is
rejected here, so an import cannot land a relationship this release would misread.

@param   array<string, mixed>  $document  Canonical relationship document, keyed as it is stored.

@return  self  The relationship, with every construction rule already applied.

@throws  InvalidBusinessDefinition  When a key is unknown, a member has the wrong type, or the
         resulting relationship breaks a construction rule.

@since   2.0.0

### `toArray(): array`

Export the relationship as the document the definition checksum is taken over.

@return  array<string, mixed>  Every declared property under its snake_case key, with the kind and
         delete behaviour written as their backing strings.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\RelationshipKind`

Cardinality of a declared relationship, and with it the physical shape the schema compiler emits.

The kind decides three things at once: whether the association lives in a column on the owning record
table or in a table of its own, which kind a reciprocal `inverse` must declare, and which delete
behaviour and ordering the definition may ask for. A pair that names each other as inverses is
materialized once only: the many-to-one side carries the storage its one-to-many partner reads back,
and a symmetric pair is settled by comparing the two handles.

@since  2.0.0

- `OneToOne`: At most one record on each side, stored as a uniquely indexed target column on this entity.

@since  2.0.0

- `ManyToOne`: Many records of this entity point at one target, stored as a target column on this entity.

This is the side that carries the storage when it is paired with a `OneToMany` inverse.

@since  2.0.0

- `OneToMany`: A collection of targets that belong to this record alone, kept in a junction table.

The junction's target index is unique, so a target cannot appear under two owners. Paired with a
`ManyToOne` inverse it emits nothing of its own and reads the column that side already holds.

@since  2.0.0

- `ManyToMany`: A collection on both sides, kept in a junction table whose rows are the pairs themselves.

Reciprocal `ManyToMany` relationships must agree on ordering, since one shared table serves both.

@since  2.0.0

- `OwnedLineCollection`: Lines owned outright by this record, kept in a line table and deleted with their owner.

Ownership is exclusive and structural: the delete behaviour must be `Cascade`, the kind takes no
inverse, and the validator refuses a cycle of owned relationships.

@since  2.0.0

- `Reversal`: A typed link from a correcting record to the record of the same definition that it reverses.

This is how an immutable document is corrected: the original is never rewritten, a new record of
the same definition carries this link back to it, and both remain readable. Storage is exactly a
`ManyToOne` column on the correcting record's own table, its inverse — "what corrected this" — is
a `OneToMany` on the same definition, and deletion of the reversed record must be restricted so
the pair can never be silently unpaired. The kind is core vocabulary so every installation can ask
"what did this correct, and what corrected this" as a declared relationship rather than through a
per-vertical field name.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\ScopeMode`

Tenancy dimensions the records of one business entity are partitioned by.

A definition declares the mode once and two collaborators then have to agree on it: the physical
schema compiler emits a `site_identifier` or `organization_identifier` control column only for the
dimensions the mode names, and `RecordScope` refuses any identifier combination the mode does not
describe, so the identifiers the query compiler binds into a record statement are always the ones the
mode admits. On the request path the site half is read off the execution context rather than taken
from caller input, leaving the organization as the only dimension a caller names. Because the stored
columns follow from the mode, the compatibility analyzer classifies changing it on a published
definition as destructive.

@since  2.0.0

- `Installation`: Records belong to the installation as a whole and carry neither scope identifier.

@since  2.0.0

- `Site`: Records belong to one site, which is read off the execution context rather than requested.

@since  2.0.0

- `Organization`: Records belong to one organization branch and are not partitioned by site at all.

The organization is the one scope dimension a caller supplies, and it is mandatory under this mode.

@since  2.0.0

- `SiteOrganization`: Records belong to one organization within one site and carry both identifiers.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\Sensitivity`

Handling class a business field declares, and with it whether the runtime ever emits its values.

The two upper levels are enforced and the three lower ones are declarative. A field at `Restricted` or
`Secret` is omitted from the record read path and from revision snapshots,
is refused as a search, filter, sort, or report target by the query compiler, cannot be declared
sortable at all, and is omitted from the audit metadata listing which fields a write changed.
`Public`, `Internal`, and `Confidential` classify the data for the people running the installation
without narrowing anything the runtime returns. Fields default to `Internal`, an encrypted
`core.secret` field must declare `Secret`, and changing a published field's class is reported as a
behaviour-changing difference because the values a caller can already see change with it.

@since  2.0.0

- `Public`: Carries no handling obligation of its own beyond the visibility flags the field already declares.

@since  2.0.0

- `Internal`: The default: ordinary operational data, meant for authenticated users of the installation.

@since  2.0.0

- `Confidential`: Commercially or personally sensitive data whose exposure is an operator policy matter.

The runtime still returns these values in full. The class exists so a reviewer can find the fields
that deserve a policy decision without the definition having to redact them outright.

@since  2.0.0

- `Restricted`: The first enforced level: values are omitted on read and the field cannot be queried against.

@since  2.0.0

- `Secret`: Credential-grade data, omitted exactly as `Restricted` is and required of encrypted secret fields.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\StorageMode`

How the records of a business entity are physically kept.

Kumwe materializes a published definition as real relational tables and offers no second strategy, so
this type has exactly one case today. It stays an explicit, declared property because the mode is part
of the canonical payload a version is checksummed over: naming it keeps already-published documents
readable if another strategy is ever added, and gives the compatibility analyzer a path to classify —
a change of storage mode between two versions is reported as destructive.

@since  2.0.0

- `Relational`: Records live in the relational tables the physical schema compiler derives from the definition.

@since  2.0.0

- `$name` (string): Readonly constructor state; see the constructor parameter contract.

- `$value` (string): Readonly constructor state; see the constructor parameter contract.

### `cases(): array`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `from(string|int $value): static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

### `tryFrom(string|int $value): ?static`

Native enum operation: cases() returns the declared ordered cases; from() maps an exact backing value or raises
ValueError; tryFrom() returns null for an unknown value. No side effects.

## `Kumwe\BusinessDefinition\Domain\ViewDefinition`

One named projection of a business entity: the fields a surface shows, filters on, and sorts by.

Views are declared inside an entity definition and travel into the immutable published payload, so the
projection a surface is meant to offer is pinned to a definition version and checksummed with it rather
than read from live configuration. This constructor proves a view sound in isolation — a supported
kind, at least one delivery surface, and bounded, duplicate-free handle lists — while the owning
`EntityTypeDefinition` proves it against the entity: every handle must name a declared field, filters
must name filterable fields and sorts sortable ones, and a view may claim the portal or public surface
only where the entity exposes that surface too. An optional owner-scoped handler/schema pair binds a
custom application view to a separately declared signed contract; omitting both keeps the generated
view behavior and its legacy canonical bytes. The flags say where a view may be offered; they never
grant permission on their own.

@since  2.0.0

- `$fields` (array): Field handles the view projects, in declaration order.

@var    list<string>
@since  2.0.0

- `$filters` (array): Field handles the view offers as filters; empty when it exposes none.

@var    list<string>
@since  2.0.0

- `$sorts` (array): Field handles the view may be ordered by; empty when it offers no ordering choice.

@var    list<string>
@since  2.0.0

- `$handle` (string): Readonly constructor state; see the constructor parameter contract.

- `$label` (string): Readonly constructor state; see the constructor parameter contract.

- `$kind` (string): Readonly constructor state; see the constructor parameter contract.

- `$administrator` (bool): Readonly constructor state; see the constructor parameter contract.

- `$portal` (bool): Readonly constructor state; see the constructor parameter contract.

- `$public` (bool): Readonly constructor state; see the constructor parameter contract.

- `$handler` (?string): Readonly constructor state; see the constructor parameter contract.

- `$schema` (?string): Readonly constructor state; see the constructor parameter contract.

- `$document` (?Kumwe\BusinessDefinition\Domain\DocumentViewDefinition): Readonly constructor state; see the
  constructor parameter contract.

### `__construct(string $handle, string $label, string $kind, array $fields, array $filters = default, array $sorts = default, bool $administrator = default, bool $portal = default, bool $public = default, ?string $handler = default, ?string $schema = default, ?Kumwe\BusinessDefinition\Domain\DocumentViewDefinition $document = default): `

Declare a view, validating its identity, kind, surfaces, and field references.

@param   string                   $handle         Lowercase snake-case name the view is addressed by.
@param   string                   $label          Operator-facing name shown wherever the view is offered.
@param   string                   $kind           Presentation shape: `list`, `detail`, `form`, `history`,
         `relation`, or `document`.
@param   list<string>             $fields         Field handles to project; at least one is required.
@param   list<string>             $filters        Field handles offered as filters, each of which the
         entity must declare filterable.
@param   list<string>             $sorts          Field handles offered as sort keys, each of which the
         entity must declare sortable.
@param   bool                     $administrator  Whether the administrator surface may render the view.
@param   bool                     $portal         Whether the portal surface may render the view.
@param   bool                     $public         Whether the view may be rendered anonymously.
@param   ?string                  $handler        Owner-scoped custom handler reference, or null for
         generated behavior.
@param   ?string                  $schema         Owner-scoped signed schema reference paired with
         `$handler`.
@param   ?DocumentViewDefinition  $document       Documentary layout roles; only a `document` kind view
         may carry them, and such a view keeps the generated rendering path.

@throws  InvalidBusinessDefinition  When the handle or label is malformed, the kind is unsupported, no
         delivery surface is declared, the projection is empty, a list exceeds 128 entries or repeats a
         handle, a handle is not a bounded lowercase identifier, exactly one custom reference is set, a
         document block accompanies a non-document kind, or a document view binds a custom handler.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\ViewDefinition`

Rebuild a view from its canonical document, rejecting any property the contract does not name.

The unknown-property check runs before anything is read, so a document written against a newer or
hand-edited schema fails at the import boundary instead of being quietly truncated on the way in.

@param   array<string, mixed>  $document  Decoded view document keyed by canonical property name.

@return  self  The validated view, having passed the same invariants as direct construction.

@throws  InvalidBusinessDefinition  When the document carries an unknown property, a property of the
         wrong type, or values the constructor rejects.

@since   2.0.0

### `toArray(): array`

Export the view as the document that becomes part of a published definition's canonical bytes.

Every declared property is emitted, defaults included, so the document round-trips through
`fromArray()` unchanged. Key order carries no meaning: `CanonicalDefinitionJson` sorts before hashing.
The custom references and the document block are written only when declared, so every previously
published definition keeps its historical canonical bytes and checksum.

@return  array<string, mixed>  Handle, label, kind, the three handle lists, and the three surface
         flags, under their canonical keys.

@since   2.0.0

## `Kumwe\BusinessDefinition\Domain\WorkflowBinding`

The state machine a business entity's records move through, declared as part of its definition.

A binding is optional: an entity that declares one gains a `workflow_state` control column, records
start life in `$initialState`, and `BusinessRecordService` moves them only by running an action whose
transition both matches the record's current state and whose capability the actor holds. Because
transition handles are unique across the binding, one handle describes exactly one edge — the same
logical move from two different states needs two handles. This constructor is the only validation
point, so a binding that exists already has a bounded, closed graph: every edge names declared states,
no edge is a self-loop, and the initial state is one of the declared states. What it deliberately does
not check is reachability, which is a modelling choice rather than an integrity one.

@since  2.0.0

- `$states` (array): Every state a record of this entity may occupy, deduplicated and in declaration order.

@var    list<string>
@since  2.0.0

- `$transitions` (array): The permitted edges, each naming its handle, endpoints, and the capability that may run it.

@var    list<array{handle: string, from: string, to: string, capability: string}>
@since  2.0.0

- `$immutableStates` (array): States whose entry closes the record: from then on its fields and owned lines refuse
  every mutation.

Immutability is a property of the document, not of the state machine: a record in one of these
states still moves through declared transitions, but `BusinessRecordService` refuses to update,
archive, restore, relate, unrelate, reorder or rewrite it, on every surface, with the stable
`BusinessRecordImmutable` error. Correction happens by issuing a new record that carries a
`RelationshipKind::Reversal` link back to this one, never by editing it.

@var    list<string>
@since  2.0.0

- `$initialState` (string): Readonly constructor state; see the constructor parameter contract.

### `__construct(string $initialState, array $states, array $transitions, array $immutableStates = default): `

Declare a workflow, validating its states, its initial state, and every transition against them.

Repeated states are collapsed rather than rejected, so the stored set is the distinct one; repeated
transition handles are a hard error, since a handle has to identify a single edge. Immutable states
must be declared states other than the initial one, because a record has to start life editable —
immutability is entered through a transition, which is what makes the freeze an audited, capability
gated act instead of a property a record could be born with.

@param   string                                                                     $initialState     State
         a newly created record starts in; it must appear in `$states`.
@param   list<string>                                                               $states           Every
         state a record may occupy, at most 64 once deduplicated.
@param   list<array{handle: string, from: string, to: string, capability: string}>  $transitions      Edges
         of the machine, at most 128, each with a unique handle and a dotted guarding capability.
@param   list<string>                                                               $immutableStates  States
         whose entry makes the record immutable; each must be a declared, non-initial state.

@throws  InvalidBusinessDefinition  When the state set is empty or exceeds 64 entries, the initial state
         is not among them, a state handle is malformed, more than 128 transitions are given, a
         transition has a malformed handle or capability, names an undeclared endpoint, loops a state
         onto itself, or repeats a handle already used, or an immutable state is undeclared or is the
         initial state.

@since   2.0.0

### `immutableIn(?string $state): bool`

Answer whether a record occupying the given state is closed against every content mutation.

A null state means the record predates the binding or the entity binds no workflow, and such a
record is never immutable — only an entered, declared state can close a document.

@param   ?string  $state  Workflow state the record currently holds, or null when it holds none.

@return  bool  True when the definition declares that state immutable.

@since   2.0.0

### `fromArray(array $document): Kumwe\BusinessDefinition\Domain\WorkflowBinding`

Rebuild a binding from its canonical document, rejecting any property the contract does not name.

Each transition is rebuilt key by key into a strict four-property object before the constructor sees
it, so a document may not smuggle an extra property through, and may not supply a transition as a
JSON array. This method settles shape only; the constructor settles the graph.

@param   array<string, mixed>  $document  Decoded workflow document keyed by canonical name.

@return  self  The validated binding, having passed the same invariants as direct construction.

@throws  InvalidBusinessDefinition  When the document carries an unknown property, the initial state is
         not a string, the state, transition or immutable-state collection is not a JSON array, a state
         is not a string, a transition is a list or declares a property outside the four named ones, a
         transition property is absent or not a string, or the constructor rejects the resulting graph.

@since   2.0.0

### `toArray(): array`

Export the binding as the document that becomes part of a published definition's canonical bytes.

The compatibility analyzer compares two versions on exactly this document, so any difference in the
states or the transitions — including their order — registers as a workflow change. The immutable
state list is written only when one is declared, which keeps the canonical bytes of every binding
published before the declaration existed exactly as they were.

@return  array<string, mixed>  The initial state, the deduplicated state list, and the transitions,
         under the canonical keys `initial_state`, `states` and `transitions`, plus
         `immutable_states` when the binding declares any.

@since   2.0.0

