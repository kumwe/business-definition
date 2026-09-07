# Changelog

## 0.1.0 — unreleased extraction candidate

- Correct definition admission: field visibility/editability conditions require boolean results, and field reads
  must match statically known scalar families. Previously accepted contradictory definitions now fail validation.
- Classify entity label translation changes as behavior-changing and require mbstring for text default admission.
  Public signatures, canonical formula bytes and the frozen native formula corpus remain unchanged.

- Unify PR and post-rebase release gates, dynamic release identity and tested publication retries.
  Normal publication verifies stable dependency tag/source/dist identity; independent attestations remain optional.

- Extract 36 portable definition types, canonical semantics, validation and compatibility.
- Require explicit host FieldConfigurationAdmission; remove PHP evaluation methods from the semantic package.
- Preserve definition ownership including site owners and approved extension grammar.
- Package owns behavioral, boundary and language-neutral conformance evidence; App adoption is separate.
- MIG-2026-010 / CS-2026-010 / NRM-2026-012. Roadmap impact: None.
