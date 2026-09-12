# Definition semantics and runtime boundaries

Business Definition exports 41 types: 28 domain values, eight application types, one mandatory admission port,
one provider and three factories. Sequence supplies number format, scope and reset types; Localization supplies
locale semantics. Exact published dependency pins are declared in Composer and independently verified by consumers.

## Admission and ownership

`FieldConfigurationAdmission` is mandatory and has no permissive production default. Core supplies its SDK-backed
adapter, retaining presentation-profile enforcement and integration tests. Definition validation enforces its own
semantic rules. Missing or incompatible admission configuration fails construction.

`DefinitionOwner` retains core, extension and site dimensions plus the persisted grammar. It is not interchangeable
with `ContributionOwner`. Field visibility/editability conditions require boolean results; expression reads must
match statically known scalar field families. Contradictory definitions fail validation. Decimal/temporal string
representations and dynamic any/null checks retain their defined semantics. Entity label translation changes are
behavior-changing compatibility changes. Text default validation requires `ext-mbstring`.

Admitted defaults, configuration, validators, workflows and document collections are detached from caller references.
Canonical encoding does not write through caller arrays. These are immutable value guarantees.

## Formula execution

`Expression::evaluate` and `RecordInvariantDefinition::isSatisfied` are absent from this package API. Core must use
the verified Computation/Engine/PHP extension execution chain before removing its existing runtime executor. Missing
native readiness fails; this package supplies no production evaluator fallback.

The non-distributed frozen evaluator preserves the baseline for the language-neutral formula corpus. Formula decimal
semantics support 4,096 digits; another package's conversion budgets cannot replace that limit without parity evidence.
The formula profile and native ownership manifest define the execution contract.

## Conformance evidence

The versioned corpus contains 101 formula, 18 definition and six compatibility vectors replayed against baseline App
source, including SDK presentation admission. Corpus provenance binds exact source hashes and frozen oracle files.
Preserve canonical documents, dependencies, exact results, ordered plans and first refusals when evolving the package.
The package's architecture gate rejects case-insensitive and qualified-name boundary bypasses. Corpus equivalence
and source tests remain distinct from independent release and native execution qualification.
