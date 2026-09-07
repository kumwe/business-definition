---
schema: "kumwe-migration-handoff/v2"
artifact_kind: "framework_php"
migration_id: "KUMWE-MIG-2026-010"
change_set: "KUMWE-CS-2026-010"
state: "draft_pr_open"
source:
  app:
    repository: "https://github.com/kumwe/app"
    baseline_commit: "960ce8ec00cf724a7cae03e5ba09c4852c9ab54e"
    examined_paths:
      - "src/BusinessDefinition"
      - "tests/Unit/BusinessDefinition"
      - "src/Kernel/ContainerFactory.php"
      - "composer.lock"
      - "docs/architecture/capability-index.md"
      - "AGENTS.md"
      - "docs/architecture/governance/decisions.md"
    old_namespace_roots:
      - "Kumwe\\App\\BusinessDefinition\\Domain\\"
      - "Kumwe\\App\\BusinessDefinition\\Application\\"
    capability_index_sha256: "87ded886f35f74878ca9eb8db4c36e23d681c4a49891f76dfc3210f385a7ce39"
  semantic_inputs: []
  examined_dependencies:
    - "App locked kumwe/conversion 0.1.2, extension-sdk 0.2.4, producer 0.2.0"
    - "Sequence 0.2.1 and Localization 0.1.1 exact published stable pins; final independent verification remains separate"
    - "SDK FieldPresentationConfiguration profile is outside allowed ceiling; mandatory host admission port retains this owner"
    - "ContributionOwner does not preserve site owners or the persisted definition extension grammar; no unused dependency"
  active_related_pull_requests:
    - "https://github.com/kumwe/app/pull/135"
    - "https://github.com/kumwe/computation/pull/1"
    - "https://github.com/kumwe/engine/pull/1"
target:
  repository: "https://github.com/kumwe/business-definition"
  artifact_identity: "kumwe/business-definition"
  canonical_namespace_or_abi: "Kumwe\\BusinessDefinition"
  branch: "codex/extraction-readiness-20260907"
  pull_request: "https://github.com/kumwe/business-definition/pull/7"
ownership:
  responsibility: "Immutable definition metadata, bounded semantic ASTs, canonical profiles, structural validation, compatibility and registries."
  non_responsibilities:
    - "Database publication and immutable version history across MariaDB, MySQL and PostgreSQL"
    - "Authorization, approval, step-up, audit, transaction and trusted extension generation"
    - "SDK presentation configuration profile adapter and integration"
    - "Compiled-plan invalidation, runtime native readiness, recovery and backup/restore"
    - "Handlers and graphical adapters; active App PHP executor tests until native cutover"
  allowed_dependency_ceiling:
    - "kumwe/contribution"
    - "kumwe/localization"
    - "kumwe/sequence"
  implementation_owner: "kumwe/business-definition"
  next_consumer: "kumwe/app"
  public_manifests:
    - 
      path: "resources/public-api/v1.json"
      sha256: "1dc8eee145c2ceadc8cb56f2ec20981c404fef6c1b1b9864940386ce98598a91"
    - 
      path: "resources/capabilities/v1.json"
      sha256: "bf58d886792915c0a33e236713dda587c85a3a64bcea3c77359193faf45ba2f4"
    - 
      path: "resources/service-map/v1.json"
      sha256: "3e77901220b9b54e2cf9297ea038343325c692d4fa60edb13967861a4636789a"
    - 
      path: "resources/native-ownership/v1.json"
      sha256: "eb0ed6131fd12b32d4fa73991bdfdd53ba42a0614587317108e05cd0687a13b7"
    - 
      path: "resources/corpus/formula-v1.json"
      sha256: "11033679b018fdc9a192e954ef11089444a00a1d89c6279d3c192be9252cf42f"
    - 
      path: "resources/corpus/definition-v1.json"
      sha256: "3d5b5c2d218e95aa9f8310d88b88b373ccd31c16b03d303fa1432c72dc533b4e"
    - 
      path: "resources/corpus/provenance-v1.json"
      sha256: "74c05b98eb9d13ced22092d819b5a1ed54e986f2502800192bd5397a3118dd7f"
  intentionally_excluded:
    - "App source ExpressionEvaluator and DecimalValue remain until native runtime adoption"
    - "NumberSequenceFormat/Reset/Scope already belong to Sequence; no duplicate definitions"
    - "BusinessDefinitionService/Repository/ContractAdmission/PackageDefinitionSynchronizer and persistence exceptions"
    - "All Infrastructure, Administrator and Delivery directories"
    - "SDK FieldPresentationConfiguration implementation; host adapter required"
framework_php:
  composer_package: "kumwe/business-definition"
  canonical_namespace: "Kumwe\\BusinessDefinition"
  public_api_manifest: "resources/public-api/v1.json"
  capability_manifest: "resources/capabilities/v1.json"
  service_map: "resources/service-map/v1.json"
  extracted_symbols:
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionCompatibilityAnalyzer"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionCompatibilityAnalyzer"
      source_path: "src/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzer.php"
      target_path: "src/Application/BusinessDefinitionCompatibilityAnalyzer.php"
      kind: "class"
      public_methods:
        - "analyze"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionContributionRegistry"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionContributionRegistry"
      source_path: "src/BusinessDefinition/Application/BusinessDefinitionContributionRegistry.php"
      target_path: "src/Application/BusinessDefinitionContributionRegistry.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "all"
        - "ownedBy"
        - "register"
        - "remove"
        - "validate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionValidator"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionValidator"
      source_path: "src/BusinessDefinition/Application/BusinessDefinitionValidator.php"
      target_path: "src/Application/BusinessDefinitionValidator.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "validateGraph"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Mandatory FieldConfigurationAdmission replaces SDK coupling; other validation semantics preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionCatalogEntry"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\DefinitionCatalogEntry"
      source_path: "src/BusinessDefinition/Application/DefinitionCatalogEntry.php"
      target_path: "src/Application/DefinitionCatalogEntry.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "draftRevision"
        - "handle"
        - "id"
        - "owner"
        - "ownerActive"
        - "publishedVersion"
        - "siteIdentifier"
        - "status"
        - "updatedAt"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionDraft"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\DefinitionDraft"
      source_path: "src/BusinessDefinition/Application/DefinitionDraft.php"
      target_path: "src/Application/DefinitionDraft.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "checksum"
        - "definition"
        - "revision"
        - "updatedAt"
        - "updatedBy"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionVersionRecord"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\DefinitionVersionRecord"
      source_path: "src/BusinessDefinition/Application/DefinitionVersionRecord.php"
      target_path: "src/Application/DefinitionVersionRecord.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "compatibility"
        - "definition"
        - "publishedAt"
        - "publishedBy"
        - "status"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\FieldTypeDefinitionResolver"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\FieldTypeDefinitionResolver"
      source_path: "src/BusinessDefinition/Application/FieldTypeDefinitionResolver.php"
      target_path: "src/Application/FieldTypeDefinitionResolver.php"
      kind: "interface"
      public_methods:
        - "get"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Application\\FieldTypeRegistry"
      new_fqcn: "Kumwe\\BusinessDefinition\\Application\\FieldTypeRegistry"
      source_path: "src/BusinessDefinition/Application/FieldTypeRegistry.php"
      target_path: "src/Application/FieldTypeRegistry.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "all"
        - "get"
        - "has"
        - "ownedBy"
        - "register"
        - "remove"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\ActionDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\ActionDefinition"
      source_path: "src/BusinessDefinition/Domain/ActionDefinition.php"
      target_path: "src/Domain/ActionDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "toArray"
      public_properties:
        - "administrator"
        - "bulk"
        - "capability"
        - "condition"
        - "handle"
        - "handler"
        - "highImpact"
        - "label"
        - "portal"
        - "public"
        - "schema"
        - "transition"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\BuiltInFieldTypes"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\BuiltInFieldTypes"
      source_path: "src/BusinessDefinition/Domain/BuiltInFieldTypes.php"
      target_path: "src/Domain/BuiltInFieldTypes.php"
      kind: "class"
      public_methods:
        - "all"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\CanonicalDefinitionJson"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\CanonicalDefinitionJson"
      source_path: "src/BusinessDefinition/Domain/CanonicalDefinitionJson.php"
      target_path: "src/Domain/CanonicalDefinitionJson.php"
      kind: "class"
      public_methods:
        - "checksum"
        - "encode"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityChange"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\CompatibilityChange"
      source_path: "src/BusinessDefinition/Domain/CompatibilityChange.php"
      target_path: "src/Domain/CompatibilityChange.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "toArray"
      public_properties:
        - "classification"
        - "message"
        - "path"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityClassification"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\CompatibilityClassification"
      source_path: "src/BusinessDefinition/Domain/CompatibilityClassification.php"
      target_path: "src/Domain/CompatibilityClassification.php"
      kind: "enum"
      public_methods:
        - "requiresConfirmation"
      public_properties: []
      public_constants:
        - "Additive"
        - "BehaviorChanging"
        - "CompatibleConstraintTightening"
        - "DataMigrationRequired"
        - "Destructive"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityPlan"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\CompatibilityPlan"
      source_path: "src/BusinessDefinition/Domain/CompatibilityPlan.php"
      target_path: "src/Domain/CompatibilityPlan.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "changes"
        - "destructive"
        - "requiresConfirmation"
        - "toArray"
      public_properties:
        - "fromChecksum"
        - "fromVersion"
        - "toChecksum"
        - "toVersion"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\ComputationMode"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\ComputationMode"
      source_path: "src/BusinessDefinition/Domain/ComputationMode.php"
      target_path: "src/Domain/ComputationMode.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Stored"
        - "Virtual"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionOwner"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\DefinitionOwner"
      source_path: "src/BusinessDefinition/Domain/DefinitionOwner.php"
      target_path: "src/Domain/DefinitionOwner.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "assertOwns"
        - "core"
        - "extension"
        - "namespace"
        - "site"
        - "toArray"
      public_properties:
        - "identifier"
        - "type"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionOwnerType"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\DefinitionOwnerType"
      source_path: "src/BusinessDefinition/Domain/DefinitionOwnerType.php"
      target_path: "src/Domain/DefinitionOwnerType.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Core"
        - "Extension"
        - "Site"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionStatus"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\DefinitionStatus"
      source_path: "src/BusinessDefinition/Domain/DefinitionStatus.php"
      target_path: "src/Domain/DefinitionStatus.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Deprecated"
        - "Draft"
        - "Published"
        - "Rejected"
        - "Superseded"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\DeleteBehavior"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\DeleteBehavior"
      source_path: "src/BusinessDefinition/Domain/DeleteBehavior.php"
      target_path: "src/Domain/DeleteBehavior.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Cascade"
        - "Restrict"
        - "SetNull"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\DocumentViewDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\DocumentViewDefinition"
      source_path: "src/BusinessDefinition/Domain/DocumentViewDefinition.php"
      target_path: "src/Domain/DocumentViewDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fieldHandles"
        - "fromArray"
        - "toArray"
      public_properties:
        - "groups"
        - "identity"
        - "lines"
        - "parties"
        - "totals"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\EntityTypeDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\EntityTypeDefinition"
      source_path: "src/BusinessDefinition/Domain/EntityTypeDefinition.php"
      target_path: "src/Domain/EntityTypeDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "actions"
        - "allowsPortalOperation"
        - "checksum"
        - "compatibilityMetadata"
        - "dependencyGraph"
        - "fields"
        - "fromArray"
        - "invariantLineDependencies"
        - "labelTranslations"
        - "pluralLabelIn"
        - "portalOperations"
        - "postingDateField"
        - "published"
        - "recordInvariants"
        - "relationships"
        - "runtimeRelationship"
        - "singularLabelIn"
        - "toArray"
        - "views"
        - "withStatus"
      public_properties:
        - "administratorExposure"
        - "auditEnabled"
        - "definitionVersion"
        - "handle"
        - "id"
        - "identityStrategy"
        - "owner"
        - "pluralLabel"
        - "portalExposure"
        - "publicExposure"
        - "revisionsEnabled"
        - "scope"
        - "singularLabel"
        - "siteIdentifier"
        - "softDeleteEnabled"
        - "status"
        - "storageMode"
        - "workflow"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\Expression"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\Expression"
      source_path: "src/BusinessDefinition/Domain/Expression.php"
      target_path: "src/Domain/Expression.php"
      kind: "class"
      public_methods:
        - "arguments"
        - "dependencies"
        - "fromArray"
        - "lineDependencies"
        - "toArray"
      public_properties:
        - "aggregate"
        - "field"
        - "lines"
        - "literal"
        - "operator"
        - "scale"
        - "type"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Intentional first-package API break: execution methods removed; AST/metadata preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\FieldDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\FieldDefinition"
      source_path: "src/BusinessDefinition/Domain/FieldDefinition.php"
      target_path: "src/Domain/FieldDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "descriptionIn"
        - "fromArray"
        - "helpTextIn"
        - "labelIn"
        - "toArray"
      public_properties:
        - "computationMode"
        - "computed"
        - "configuration"
        - "createVisible"
        - "default"
        - "description"
        - "editabilityCondition"
        - "exportable"
        - "filterable"
        - "formGroup"
        - "formula"
        - "handle"
        - "helpText"
        - "immutableAfterCreate"
        - "indexed"
        - "label"
        - "length"
        - "localized"
        - "normalizers"
        - "nullable"
        - "order"
        - "placements"
        - "precision"
        - "readOnly"
        - "readVisible"
        - "reportable"
        - "required"
        - "scale"
        - "searchable"
        - "sensitivity"
        - "serverOnly"
        - "sortable"
        - "textTranslations"
        - "type"
        - "unique"
        - "updateVisible"
        - "validators"
        - "visibilityCondition"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\FieldTypeDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\FieldTypeDefinition"
      source_path: "src/BusinessDefinition/Domain/FieldTypeDefinition.php"
      target_path: "src/Domain/FieldTypeDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "toArray"
      public_properties:
        - "configurationKeys"
        - "description"
        - "id"
        - "label"
        - "storageType"
        - "valueType"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\IdentityStrategy"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\IdentityStrategy"
      source_path: "src/BusinessDefinition/Domain/IdentityStrategy.php"
      target_path: "src/Domain/IdentityStrategy.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Reference"
        - "Uuid"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\InvalidBusinessDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\InvalidBusinessDefinition"
      source_path: "src/BusinessDefinition/Domain/InvalidBusinessDefinition.php"
      target_path: "src/Domain/InvalidBusinessDefinition.php"
      kind: "class"
      public_methods: []
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\LocalizedDefinitionText"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\LocalizedDefinitionText"
      source_path: "src/BusinessDefinition/Domain/LocalizedDefinitionText.php"
      target_path: "src/Domain/LocalizedDefinitionText.php"
      kind: "class"
      public_methods:
        - "normalize"
        - "resolve"
      public_properties: []
      public_constants:
        - "MAXIMUM_LOCALES"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\PortalOperation"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\PortalOperation"
      source_path: "src/BusinessDefinition/Domain/PortalOperation.php"
      target_path: "src/Domain/PortalOperation.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Action"
        - "Approval"
        - "Archive"
        - "Browse"
        - "Create"
        - "Delete"
        - "Export"
        - "History"
        - "Read"
        - "Relation"
        - "Reorder"
        - "Report"
        - "Restore"
        - "Status"
        - "Update"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\RecordInvariantDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\RecordInvariantDefinition"
      source_path: "src/BusinessDefinition/Domain/RecordInvariantDefinition.php"
      target_path: "src/Domain/RecordInvariantDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "lineDependencies"
        - "toArray"
      public_properties:
        - "condition"
        - "handle"
        - "message"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Intentional first-package API break: execution methods removed; AST/metadata preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\RelationshipDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\RelationshipDefinition"
      source_path: "src/BusinessDefinition/Domain/RelationshipDefinition.php"
      target_path: "src/Domain/RelationshipDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "toArray"
      public_properties:
        - "handle"
        - "inverse"
        - "kind"
        - "label"
        - "onDelete"
        - "ordered"
        - "required"
        - "target"
        - "unique"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\RelationshipKind"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\RelationshipKind"
      source_path: "src/BusinessDefinition/Domain/RelationshipKind.php"
      target_path: "src/Domain/RelationshipKind.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "ManyToMany"
        - "ManyToOne"
        - "OneToMany"
        - "OneToOne"
        - "OwnedLineCollection"
        - "Reversal"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\ScopeMode"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\ScopeMode"
      source_path: "src/BusinessDefinition/Domain/ScopeMode.php"
      target_path: "src/Domain/ScopeMode.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Installation"
        - "Organization"
        - "Site"
        - "SiteOrganization"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\Sensitivity"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\Sensitivity"
      source_path: "src/BusinessDefinition/Domain/Sensitivity.php"
      target_path: "src/Domain/Sensitivity.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Confidential"
        - "Internal"
        - "Public"
        - "Restricted"
        - "Secret"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\StorageMode"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\StorageMode"
      source_path: "src/BusinessDefinition/Domain/StorageMode.php"
      target_path: "src/Domain/StorageMode.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "Relational"
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\ViewDefinition"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\ViewDefinition"
      source_path: "src/BusinessDefinition/Domain/ViewDefinition.php"
      target_path: "src/Domain/ViewDefinition.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "toArray"
      public_properties:
        - "administrator"
        - "document"
        - "fields"
        - "filters"
        - "handle"
        - "handler"
        - "kind"
        - "label"
        - "portal"
        - "public"
        - "schema"
        - "sorts"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
    - 
      old_fqcn: "Kumwe\\App\\BusinessDefinition\\Domain\\WorkflowBinding"
      new_fqcn: "Kumwe\\BusinessDefinition\\Domain\\WorkflowBinding"
      source_path: "src/BusinessDefinition/Domain/WorkflowBinding.php"
      target_path: "src/Domain/WorkflowBinding.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "fromArray"
        - "immutableIn"
        - "toArray"
      public_properties:
        - "immutableStates"
        - "initialState"
        - "states"
        - "transitions"
      public_constants: []
      exceptions: []
      serialization_contract: "Canonical toArray profile and persisted metadata are unchanged where exposed; no serialized PHP names migrate."
      compatibility: "Namespace-only portable extraction; approved behavior preserved."
  consumers:
    app_code:
      - "src/BusinessIntegration/Infrastructure/ContributedScheduleSynchronizer.php"
      - "src/BusinessRecord/Application/BusinessRecordMutationPublication.php"
      - "src/BusinessRecord/Application/BusinessRecordReadRepository.php"
      - "src/BusinessRecord/Application/BusinessRecordRelationshipCoordinator.php"
      - "src/BusinessRecord/Application/BusinessRecordRevisionView.php"
      - "src/BusinessRecord/Application/BusinessRecordService.php"
      - "src/BusinessRecord/Application/BusinessRecordView.php"
      - "src/BusinessRecord/Application/BusinessRecordWriteRepository.php"
      - "src/BusinessRecord/Application/InstalledBusinessRecordDefinitionResolver.php"
      - "src/BusinessRecord/Application/OwnedLineFormResult.php"
      - "src/BusinessRecord/Application/OwnedLineMutationIntent.php"
      - "src/BusinessRecord/Application/PlannedFieldEncoding.php"
      - "src/BusinessRecord/Application/PostingPeriodLock.php"
      - "src/BusinessRecord/Application/RecordFieldVisibility.php"
      - "src/BusinessRecord/Application/RecordRuleValidator.php"
      - "src/BusinessRecord/Application/RecordValueCodec.php"
      - "src/BusinessRecord/Application/RelatedRecordBrowseResult.php"
      - "src/BusinessRecord/Application/ResolvedBusinessDefinition.php"
      - "src/BusinessRecord/Domain/RecordScope.php"
      - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordQueryCompiler.php"
      - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordReadRepository.php"
      - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordWriteRepository.php"
      - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessSchemaRecordRepinGateway.php"
      - "src/BusinessRecord/Infrastructure/Persistence/DoctrineRecordSecretRotation.php"
      - "src/BusinessReporting/Application/ExportService.php"
      - "src/BusinessReporting/Application/JournalProjectionEvent.php"
      - "src/BusinessReporting/Application/RecordExportReportProvider.php"
      - "src/BusinessReporting/Application/ReportService.php"
      - "src/BusinessReporting/Domain/ExportArtifact.php"
      - "src/BusinessReporting/Domain/ReportDefinition.php"
      - "src/BusinessReporting/Domain/ReportFormulaDefinition.php"
      - "src/BusinessReporting/Infrastructure/BusinessRecordExportPolicySnapshotProvider.php"
      - "src/BusinessReporting/Infrastructure/BusinessRecordReportScopeResolver.php"
      - "src/BusinessReporting/Infrastructure/DoctrineExportArtifactRepository.php"
      - "src/BusinessReporting/Infrastructure/DoctrineProjectionStore.php"
      - "src/BusinessReporting/Infrastructure/FilesystemExportArtifactRepository.php"
      - "src/BusinessSchema/Application/BusinessSchemaExecutor.php"
      - "src/BusinessSchema/Application/BusinessSchemaLifecycleManager.php"
      - "src/BusinessSchema/Application/BusinessSchemaPlanner.php"
      - "src/BusinessSchema/Application/BusinessSchemaRecordRepinGateway.php"
      - "src/BusinessSchema/Application/BusinessSchemaService.php"
      - "src/BusinessSchema/Application/DefinitionPhysicalSchemaCompiler.php"
      - "src/BusinessSchema/Application/PublishedDefinitionSchemaObserver.php"
      - "src/BusinessSchema/Application/SchemaChunkResult.php"
      - "src/BusinessSchema/Domain/PhysicalColumnBlueprint.php"
      - "src/BusinessSchema/Domain/PhysicalIndexBlueprint.php"
      - "src/BusinessSchema/Domain/PhysicalSchemaBlueprint.php"
      - "src/BusinessSchema/Domain/PhysicalTableBlueprint.php"
      - "src/BusinessSchema/Domain/SchemaEvolutionHints.php"
      - "src/BusinessSchema/Domain/SchemaInstallation.php"
      - "src/BusinessSchema/Domain/SchemaOperation.php"
      - "src/BusinessSchema/Domain/SchemaPlan.php"
      - "src/BusinessSchema/Domain/SchemaPlanStep.php"
      - "src/BusinessSchema/Domain/SchemaRecoveryEvidence.php"
      - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaInstallationRepository.php"
      - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaPlanRepository.php"
      - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaRecoveryEvidenceRepository.php"
      - "src/BusinessSchema/Infrastructure/Schema/CanonicalDefinitionPhysicalSchemaCompiler.php"
      - "src/BusinessSchema/Infrastructure/Schema/DoctrinePhysicalSchemaGateway.php"
      - "src/BusinessSecurity/Application/Administration/BusinessSecurityAdministrationService.php"
      - "src/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessController.php"
      - "src/BusinessSurface/Application/BusinessBulkMutation.php"
      - "src/BusinessSurface/Application/BusinessMutationPlanService.php"
      - "src/BusinessSurface/Application/BusinessOperationStatusService.php"
      - "src/BusinessSurface/Application/BusinessSurfaceCatalog.php"
      - "src/BusinessSurface/Application/BusinessSurfaceService.php"
      - "src/BusinessSurface/Application/Custom/CustomBusinessActionHandlerRegistry.php"
      - "src/BusinessSurface/Application/Custom/CustomBusinessActionLedgerResult.php"
      - "src/BusinessSurface/Application/Custom/CustomBusinessReferenceRegistry.php"
      - "src/BusinessSurface/Application/Custom/CustomBusinessSurfaceDispatcher.php"
      - "src/BusinessSurface/Application/Custom/CustomBusinessViewHandlerRegistry.php"
      - "src/BusinessSurface/Application/CustomBusinessActionExecutor.php"
      - "src/BusinessSurface/Application/FieldModelPresenter.php"
      - "src/BusinessSurface/Presentation/Field/CoreFieldPresenter.php"
      - "src/BusinessSurface/Presentation/Field/FieldPresentationCoverage.php"
      - "src/BusinessSurface/Presentation/Field/FieldPresentationInputFactory.php"
      - "src/BusinessSurface/Presentation/Field/FieldPresentationRegistry.php"
      - "src/BusinessSurface/Presentation/Field/RegistryFieldModelPresenter.php"
      - "src/Delivery/Console/Command/ManageBusinessDefinitionsCommand.php"
      - "src/Delivery/Console/Command/ManageBusinessRecordsCommand.php"
      - "src/Delivery/Http/Api/Business/BusinessApiResponder.php"
      - "src/Delivery/Http/Api/Business/BusinessRecordApiHandler.php"
      - "src/Demo/Application/DemoBusinessTemplateProjector.php"
      - "src/Demo/Application/VdmBusinessManifestProjector.php"
      - "src/Demo/Application/VdmBusinessOperationGuard.php"
      - "src/Demo/Infrastructure/DemoBusinessProfileExporter.php"
      - "src/Demo/Infrastructure/DemoContentProfileInstaller.php"
      - "src/Demo/Infrastructure/DemoProfileExporter.php"
      - "src/Demo/Infrastructure/DemoProfileInstaller.php"
      - "src/Demo/Infrastructure/FilesystemDemoManifestCatalog.php"
      - "src/Demo/Infrastructure/VdmBusinessDemoInstaller.php"
      - "src/Extension/Application/Trust/TrustStore.php"
      - "src/Extension/Contribution/BusinessContributionSurface.php"
      - "src/Extension/Contribution/CanonicalManifestActivator.php"
      - "src/Extension/Contribution/CanonicalManifestInterpreter.php"
      - "src/Extension/Contribution/CoreContributionRegistrar.php"
      - "src/Extension/Contribution/CoreExtensionContributions.php"
      - "src/Extension/Contribution/ExtensionContributionRegistrySet.php"
      - "src/Extension/Contribution/OwnedExtensionBindingRegistrar.php"
      - "src/Extension/Infrastructure/DoctrineExtensionManager.php"
      - "src/Extension/Runtime/ActiveExtensionSet.php"
      - "src/Extension/Runtime/ExtensionRuntimeLoader.php"
      - "src/Infrastructure/Mcp/KumweMcpHandlers.php"
      - "src/Infrastructure/Mcp/McpToolErrorVocabulary.php"
      - "src/Infrastructure/Persistence/Migration/BusinessTransactionalRuntimeMigration.php"
      - "src/Kernel/ContainerFactory.php"
      - "src/Kernel/DeferredBusinessSchemaObserver.php"
      - "src/OpenApi/Application/OpenApiComponentClaimAdmission.php"
      - "src/OpenApi/Application/OpenApiExtensionActivationAdmission.php"
    configuration_and_di:
      - "src/Kernel/ContainerFactory.php"
    reflection_and_string_references:
      - "docs/architecture/governance/core-growth-baseline.json"
    fixtures_and_examples:
      - "tests/Architecture/ConvertedMoneySurfaceCoverageTest.php"
      - "tests/Architecture/MoneyConversionBoundaryTest.php"
      - "tests/Architecture/UnitConversionBoundaryTest.php"
      - "tests/Functional/BusinessSurface/GeneratedBusinessAdapterParityTest.php"
      - "tests/Functional/BusinessSurface/GeneratedBusinessDataEntryRetentionTest.php"
      - "tests/Functional/Extension/LiveSurfaceContractParityTest.php"
      - "tests/Integration/BusinessDefinition/BusinessDefinitionRuntimeIntegrationTest.php"
      - "tests/Integration/BusinessDefinition/OpenApiDefinitionPublicationAdmissionIntegrationTest.php"
      - "tests/Integration/BusinessRecord/AggregateDocumentConcurrencyIntegrationTest.php"
      - "tests/Integration/BusinessRecord/AggregateDocumentIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessNumberSequenceContentionIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessNumberSequenceHotPathIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessNumberSequenceIdentityIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordClientReferenceIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordEvolutionIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordInverseRelationshipIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordMutationGenerationIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordPolicyCompilerIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordPolicyEnforcementIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordRelationshipIntegrationTest.php"
      - "tests/Integration/BusinessRecord/BusinessRecordRuntimeIntegrationTest.php"
      - "tests/Integration/BusinessRecord/CorrectionAfterPeriodCloseIntegrationTest.php"
      - "tests/Integration/BusinessRecord/DocumentCommitInstrumentationIntegrationTest.php"
      - "tests/Integration/BusinessRecord/FiscalPeriodSequenceIntegrationTest.php"
      - "tests/Integration/BusinessRecord/ImmutableRecordReversalIntegrationTest.php"
      - "tests/Integration/BusinessRecord/RecordSecretRotationIntegrationTest.php"
      - "tests/Integration/BusinessSchema/BusinessSchemaColumnRelaxationIntegrationTest.php"
      - "tests/Integration/BusinessSchema/BusinessSchemaExecutionStateGuardIntegrationTest.php"
      - "tests/Integration/BusinessSchema/BusinessSchemaRecoveryIntegrationTest.php"
      - "tests/Integration/BusinessSchema/BusinessSchemaRuntimeIntegrationTest.php"
      - "tests/Integration/BusinessSchema/BusinessSchemaSourceBindingRecoveryIntegrationTest.php"
      - "tests/Integration/BusinessSurface/GeneratedBusinessActionExposureIntegrationTest.php"
      - "tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php"
      - "tests/Integration/BusinessSurface/GeneratedBusinessQueryBudgetIntegrationTest.php"
      - "tests/Integration/BusinessSurface/GeneratedBusinessRelatedPolicyIntegrationTest.php"
      - "tests/Integration/Demo/Infrastructure/DemoBusinessProfileExportInstallIntegrationTest.php"
      - "tests/Integration/Demo/Infrastructure/DemoBusinessProfileExporterTest.php"
      - "tests/Integration/Extension/AssetInspectionCustomViewIntegrationTest.php"
      - "tests/Integration/Performance/HotPlanRegressionIntegrationTest.php"
      - "tests/Integration/Presentation/McpThemeIntegrationTest.php"
      - "tests/Support/AssetInspectionDeploymentAcceptance.php"
      - "tests/Support/BusinessRuntimeBackupAcceptance.php"
      - "tests/Support/McpHandlersFixture.php"
      - "tests/Support/NeutralBusinessFixture.php"
      - "tests/Support/TestKernelFactory.php"
      - "tests/Support/TransientBusinessDefinitionFixtureScope.php"
      - "tests/Support/prepare-browser-contribution.php"
      - "tests/Unit/Application/Authorization/AdapterAuthorizationParityTest.php"
      - "tests/Unit/BusinessDefinition/Administrator/BusinessDefinitionFormMapperTest.php"
      - "tests/Unit/BusinessDefinition/Application/AllocatedNumberFieldRuleTest.php"
      - "tests/Unit/BusinessDefinition/Application/BoundedJsonDefaultBudgetTest.php"
      - "tests/Unit/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzerTest.php"
      - "tests/Unit/BusinessDefinition/Application/FiscalPeriodSequenceCoherenceTest.php"
      - "tests/Unit/BusinessDefinition/Application/NumberSequenceScopeCoherenceTest.php"
      - "tests/Unit/BusinessDefinition/Application/ReversalRelationshipValidationTest.php"
      - "tests/Unit/BusinessDefinition/Domain/AggregateInvariantDeclarationTest.php"
      - "tests/Unit/BusinessDefinition/Domain/CustomBusinessDefinitionReferenceTest.php"
      - "tests/Unit/BusinessDefinition/Domain/DocumentViewDefinitionTest.php"
      - "tests/Unit/BusinessDefinition/Domain/EntityTypeDefinitionTest.php"
      - "tests/Unit/BusinessDefinition/Domain/ExpressionLineAggregateTest.php"
      - "tests/Unit/BusinessDefinition/Domain/ExpressionPropertyTest.php"
      - "tests/Unit/BusinessDefinition/Domain/ExpressionTest.php"
      - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
      - "tests/Unit/BusinessDefinition/Domain/NumberSequenceFormatTest.php"
      - "tests/Unit/BusinessDefinition/Domain/WorkflowBindingTest.php"
      - "tests/Unit/BusinessDefinition/Infrastructure/DoctrineBusinessDefinitionRepositoryTest.php"
      - "tests/Unit/BusinessDefinition/Infrastructure/DoctrinePersistedFieldTypeDefinitionResolverTest.php"
      - "tests/Unit/BusinessRecord/Application/AggregateInvariantValidationTest.php"
      - "tests/Unit/BusinessRecord/Application/BusinessRecordMutationPublicationTest.php"
      - "tests/Unit/BusinessRecord/Application/BusinessRecordRelationshipCoordinatorTest.php"
      - "tests/Unit/BusinessRecord/Application/BusinessRecordViewTest.php"
      - "tests/Unit/BusinessRecord/Application/PostingPeriodLockTest.php"
      - "tests/Unit/BusinessRecord/Application/RecordRuleValidatorTest.php"
      - "tests/Unit/BusinessRecord/Domain/ExactValueCodecTest.php"
      - "tests/Unit/BusinessRecord/Domain/RecordIntegrityTest.php"
      - "tests/Unit/BusinessRecord/Domain/RecordScopeTest.php"
      - "tests/Unit/BusinessRecord/NeutralBusinessFixtureTest.php"
      - "tests/Unit/BusinessReporting/ExportArtifactTest.php"
      - "tests/Unit/BusinessReporting/RecordExportPipelineTest.php"
      - "tests/Unit/BusinessReporting/RecordExportReportProviderTest.php"
      - "tests/Unit/BusinessSchema/Domain/SchemaEvolutionHintsTest.php"
      - "tests/Unit/BusinessSchema/Infrastructure/CanonicalDefinitionPhysicalSchemaCompilerTest.php"
      - "tests/Unit/BusinessSecurity/Application/BusinessRecordAccessPlanTest.php"
      - "tests/Unit/BusinessSecurity/Application/BusinessSecurityAdministrationServiceTest.php"
      - "tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessControllerTest.php"
      - "tests/Unit/BusinessSurface/Application/BusinessMutationPlanServiceTest.php"
      - "tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php"
      - "tests/Unit/BusinessSurface/Application/BusinessSurfaceServiceTest.php"
      - "tests/Unit/BusinessSurface/Application/Custom/CustomBusinessActionExecutorTest.php"
      - "tests/Unit/BusinessSurface/Application/Custom/CustomBusinessHandlerRegistryTest.php"
      - "tests/Unit/BusinessSurface/Presentation/Field/CoreFieldPresenterTest.php"
      - "tests/Unit/BusinessSurface/Presentation/FieldPresentationRegistryTest.php"
      - "tests/Unit/BusinessSurface/Presentation/RegistryFieldModelPresenterTest.php"
      - "tests/Unit/BusinessSurface/Presentation/SeverityFieldPresenterTest.php"
      - "tests/Unit/Delivery/Http/Api/Business/BusinessOperationStatusApiHandlerTest.php"
      - "tests/Unit/Delivery/Http/Api/Business/BusinessRecordApiHandlerTest.php"
      - "tests/Unit/Demo/Application/DemoBusinessTemplateProjectorTest.php"
      - "tests/Unit/Demo/Application/VdmBusinessManifestProjectorTest.php"
      - "tests/Unit/Demo/Application/VdmBusinessOperationGuardTest.php"
      - "tests/Unit/Demo/Infrastructure/FilesystemDemoManifestCatalogTest.php"
      - "tests/Unit/Demo/Infrastructure/VdmBusinessDefinitionSchemaTest.php"
      - "tests/Unit/Demo/Infrastructure/VdmBusinessDemoInstallerTest.php"
      - "tests/Unit/Extension/Contribution/CustomBusinessHandlerBindingTest.php"
      - "tests/Unit/OpenApi/Application/OpenApiComponentClaimAdmissionTest.php"
      - "tests/Unit/OpenApi/Application/OpenApiExtensionActivationAdmissionTest.php"
      - "tests/Unit/Support/TransientBusinessDefinitionFixtureScopeTest.php"
    external:
      - "Engine consumes verified formula/canonical semantic corpus; reviewed draft development is not stable release evidence."
  dependency_injection:
    mode: "config-provider"
    provider: "Kumwe\\BusinessDefinition\\ConfigProvider"
    factories:
      - "Kumwe\\BusinessDefinition\\Container\\BusinessDefinitionContributionRegistryFactory"
      - "Kumwe\\BusinessDefinition\\Container\\BusinessDefinitionValidatorFactory"
      - "Kumwe\\BusinessDefinition\\Container\\FieldTypeRegistryFactory"
    aliases:
      - "FieldTypeDefinitionResolver -> FieldTypeRegistry"
    service_lifetimes:
      - "FieldTypeRegistry shared inside an operation-owned host container; validator and contribution registry non-shared."
      - "Host supplies FieldConfigurationAdmission explicitly; immutable values/stateless analyzer use direct construction."
    configuration_keys: []
    provider_absence_reason: null
native_cpp: null
php_extension: null
tests:
  moved_or_added:
    - "tests/run.php"
    - "tests/ownership.json"
    - "tests/Unit/AdmissionAndRegistryTest.php"
    - "tests/Unit/Application/AllocatedNumberFieldRuleTest.php"
    - "tests/Unit/Application/BoundedJsonDefaultBudgetTest.php"
    - "tests/Unit/Application/BusinessDefinitionCompatibilityAnalyzerTest.php"
    - "tests/Unit/Application/FiscalPeriodSequenceCoherenceTest.php"
    - "tests/Unit/Application/NumberSequenceScopeCoherenceTest.php"
    - "tests/Unit/Application/ReversalRelationshipValidationTest.php"
    - "tests/Unit/ConformanceTest.php"
    - "tests/Unit/Domain/AggregateInvariantDeclarationTest.php"
    - "tests/Unit/Domain/CustomBusinessDefinitionReferenceTest.php"
    - "tests/Unit/Domain/DocumentViewDefinitionTest.php"
    - "tests/Unit/Domain/EntityTypeDefinitionTest.php"
    - "tests/Unit/Domain/ExpressionLineAggregateTest.php"
    - "tests/Unit/Domain/ExpressionPropertyTest.php"
    - "tests/Unit/Domain/ExpressionTest.php"
    - "tests/Unit/Domain/LocalizedDefinitionLabelTest.php"
    - "tests/Unit/Domain/WorkflowBindingTest.php"
    - "tools/verify-architecture.php"
    - "tools/verify-test-ownership.php"
    - "tools/verify-clean-consumer.php"
  remain_in_app_or_consumer:
    - "Database publication and immutable version history across MariaDB, MySQL and PostgreSQL"
    - "Authorization, approval, step-up, audit, transaction and trusted extension generation"
    - "SDK presentation configuration profile adapter and integration"
    - "Compiled-plan invalidation, runtime native readiness, recovery and backup/restore"
    - "Handlers and graphical adapters; active App PHP executor tests until native cutover"
  split_tests:
    - "tests/Unit/BusinessDefinition/Domain/EntityTypeDefinitionTest.php: Keep SDK presentation-profile budget and FieldPresentationInput integration methods, plus runtime invariant assertions until native cutover."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionLineAggregateTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionPropertyTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
  prohibited_duplicates:
    - "tests/Unit/BusinessDefinition/Application/AllocatedNumberFieldRuleTest.php"
    - "tests/Unit/BusinessDefinition/Application/BoundedJsonDefaultBudgetTest.php"
    - "tests/Unit/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzerTest.php"
    - "tests/Unit/BusinessDefinition/Application/FiscalPeriodSequenceCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/NumberSequenceScopeCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/ReversalRelationshipValidationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/AggregateInvariantDeclarationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/CustomBusinessDefinitionReferenceTest.php"
    - "tests/Unit/BusinessDefinition/Domain/DocumentViewDefinitionTest.php"
    - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
    - "tests/Unit/BusinessDefinition/Domain/WorkflowBindingTest.php"
  corpora:
    - "resources/corpus/formula-v1.json"
    - "resources/corpus/definition-v1.json"
    - "resources/corpus/provenance-v1.json"
documentation:
  charter: "CHARTER.md"
  readme: "README.md"
  public_api: "docs/public-api.md"
  architecture: "docs/architecture.md"
  integration_or_consumer: "docs/integration.md"
  examples:
    - "examples/definition.php"
    - "examples/container.php"
  changelog_record: "CHANGELOG.md / 0.1.1 (maintenance release record; human merge triggers publication)"
release_expectations:
  version_policy: "Exact pre-1.0 pins; publication verifies dependency tag/source/dist identity. Independent final release verification precedes App adoption."
  expected_artifact_types:
    - "Composer package ZIP"
    - "GitHub source archive"
  required_checks:
    - "composer check on 64-bit PHP 8.5"
    - "Exact source/API/test ownership and language-neutral corpus parity"
    - "Built ZIP no-dev classmap-authoritative dependency consumer"
    - "resources/release-readiness.json records published dependency readiness; final release verification remains separate"
    - "Independent package release/archive/manifest/registry verification after human merge"
  required_registry_or_installer: "Packagist + Composer"
  required_external_attestation: true
next_task:
  phase_name: "Verify upstream and package releases, then Business Definition App adoption"
  permitted_only_when:
    - "Every selected upstream release independently verified; exact published pins updated with evidence"
    - "Human merge, immutable package publication and independent RELEASE-ATTESTATION.yaml"
    - "Reconcile every portable source/test with exact App baseline before adoption"
    - "Runtime execution-method removals adopted only with required verified Computation/Engine/extension readiness"
  consumer_repository: "https://github.com/kumwe/app"
  dependency_or_native_change: "Require exact verified kumwe/business-definition version; regenerate Composer lock through Composer."
  namespace_or_api_replacements:
    - "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionCompatibilityAnalyzer -> Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionCompatibilityAnalyzer"
    - "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionContributionRegistry -> Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionContributionRegistry"
    - "Kumwe\\App\\BusinessDefinition\\Application\\BusinessDefinitionValidator -> Kumwe\\BusinessDefinition\\Application\\BusinessDefinitionValidator"
    - "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionCatalogEntry -> Kumwe\\BusinessDefinition\\Application\\DefinitionCatalogEntry"
    - "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionDraft -> Kumwe\\BusinessDefinition\\Application\\DefinitionDraft"
    - "Kumwe\\App\\BusinessDefinition\\Application\\DefinitionVersionRecord -> Kumwe\\BusinessDefinition\\Application\\DefinitionVersionRecord"
    - "Kumwe\\App\\BusinessDefinition\\Application\\FieldTypeDefinitionResolver -> Kumwe\\BusinessDefinition\\Application\\FieldTypeDefinitionResolver"
    - "Kumwe\\App\\BusinessDefinition\\Application\\FieldTypeRegistry -> Kumwe\\BusinessDefinition\\Application\\FieldTypeRegistry"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\ActionDefinition -> Kumwe\\BusinessDefinition\\Domain\\ActionDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\BuiltInFieldTypes -> Kumwe\\BusinessDefinition\\Domain\\BuiltInFieldTypes"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\CanonicalDefinitionJson -> Kumwe\\BusinessDefinition\\Domain\\CanonicalDefinitionJson"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityChange -> Kumwe\\BusinessDefinition\\Domain\\CompatibilityChange"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityClassification -> Kumwe\\BusinessDefinition\\Domain\\CompatibilityClassification"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\CompatibilityPlan -> Kumwe\\BusinessDefinition\\Domain\\CompatibilityPlan"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\ComputationMode -> Kumwe\\BusinessDefinition\\Domain\\ComputationMode"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionOwner -> Kumwe\\BusinessDefinition\\Domain\\DefinitionOwner"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionOwnerType -> Kumwe\\BusinessDefinition\\Domain\\DefinitionOwnerType"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\DefinitionStatus -> Kumwe\\BusinessDefinition\\Domain\\DefinitionStatus"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\DeleteBehavior -> Kumwe\\BusinessDefinition\\Domain\\DeleteBehavior"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\DocumentViewDefinition -> Kumwe\\BusinessDefinition\\Domain\\DocumentViewDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\EntityTypeDefinition -> Kumwe\\BusinessDefinition\\Domain\\EntityTypeDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\Expression -> Kumwe\\BusinessDefinition\\Domain\\Expression"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\FieldDefinition -> Kumwe\\BusinessDefinition\\Domain\\FieldDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\FieldTypeDefinition -> Kumwe\\BusinessDefinition\\Domain\\FieldTypeDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\IdentityStrategy -> Kumwe\\BusinessDefinition\\Domain\\IdentityStrategy"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\InvalidBusinessDefinition -> Kumwe\\BusinessDefinition\\Domain\\InvalidBusinessDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\LocalizedDefinitionText -> Kumwe\\BusinessDefinition\\Domain\\LocalizedDefinitionText"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\PortalOperation -> Kumwe\\BusinessDefinition\\Domain\\PortalOperation"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\RecordInvariantDefinition -> Kumwe\\BusinessDefinition\\Domain\\RecordInvariantDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\RelationshipDefinition -> Kumwe\\BusinessDefinition\\Domain\\RelationshipDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\RelationshipKind -> Kumwe\\BusinessDefinition\\Domain\\RelationshipKind"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\ScopeMode -> Kumwe\\BusinessDefinition\\Domain\\ScopeMode"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\Sensitivity -> Kumwe\\BusinessDefinition\\Domain\\Sensitivity"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\StorageMode -> Kumwe\\BusinessDefinition\\Domain\\StorageMode"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\ViewDefinition -> Kumwe\\BusinessDefinition\\Domain\\ViewDefinition"
    - "Kumwe\\App\\BusinessDefinition\\Domain\\WorkflowBinding -> Kumwe\\BusinessDefinition\\Domain\\WorkflowBinding"
  files_to_update:
    - "src/BusinessIntegration/Infrastructure/ContributedScheduleSynchronizer.php"
    - "src/BusinessRecord/Application/BusinessRecordMutationPublication.php"
    - "src/BusinessRecord/Application/BusinessRecordReadRepository.php"
    - "src/BusinessRecord/Application/BusinessRecordRelationshipCoordinator.php"
    - "src/BusinessRecord/Application/BusinessRecordRevisionView.php"
    - "src/BusinessRecord/Application/BusinessRecordService.php"
    - "src/BusinessRecord/Application/BusinessRecordView.php"
    - "src/BusinessRecord/Application/BusinessRecordWriteRepository.php"
    - "src/BusinessRecord/Application/InstalledBusinessRecordDefinitionResolver.php"
    - "src/BusinessRecord/Application/OwnedLineFormResult.php"
    - "src/BusinessRecord/Application/OwnedLineMutationIntent.php"
    - "src/BusinessRecord/Application/PlannedFieldEncoding.php"
    - "src/BusinessRecord/Application/PostingPeriodLock.php"
    - "src/BusinessRecord/Application/RecordFieldVisibility.php"
    - "src/BusinessRecord/Application/RecordRuleValidator.php"
    - "src/BusinessRecord/Application/RecordValueCodec.php"
    - "src/BusinessRecord/Application/RelatedRecordBrowseResult.php"
    - "src/BusinessRecord/Application/ResolvedBusinessDefinition.php"
    - "src/BusinessRecord/Domain/RecordScope.php"
    - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordQueryCompiler.php"
    - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordReadRepository.php"
    - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordWriteRepository.php"
    - "src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessSchemaRecordRepinGateway.php"
    - "src/BusinessRecord/Infrastructure/Persistence/DoctrineRecordSecretRotation.php"
    - "src/BusinessReporting/Application/ExportService.php"
    - "src/BusinessReporting/Application/JournalProjectionEvent.php"
    - "src/BusinessReporting/Application/RecordExportReportProvider.php"
    - "src/BusinessReporting/Application/ReportService.php"
    - "src/BusinessReporting/Domain/ExportArtifact.php"
    - "src/BusinessReporting/Domain/ReportDefinition.php"
    - "src/BusinessReporting/Domain/ReportFormulaDefinition.php"
    - "src/BusinessReporting/Infrastructure/BusinessRecordExportPolicySnapshotProvider.php"
    - "src/BusinessReporting/Infrastructure/BusinessRecordReportScopeResolver.php"
    - "src/BusinessReporting/Infrastructure/DoctrineExportArtifactRepository.php"
    - "src/BusinessReporting/Infrastructure/DoctrineProjectionStore.php"
    - "src/BusinessReporting/Infrastructure/FilesystemExportArtifactRepository.php"
    - "src/BusinessSchema/Application/BusinessSchemaExecutor.php"
    - "src/BusinessSchema/Application/BusinessSchemaLifecycleManager.php"
    - "src/BusinessSchema/Application/BusinessSchemaPlanner.php"
    - "src/BusinessSchema/Application/BusinessSchemaRecordRepinGateway.php"
    - "src/BusinessSchema/Application/BusinessSchemaService.php"
    - "src/BusinessSchema/Application/DefinitionPhysicalSchemaCompiler.php"
    - "src/BusinessSchema/Application/PublishedDefinitionSchemaObserver.php"
    - "src/BusinessSchema/Application/SchemaChunkResult.php"
    - "src/BusinessSchema/Domain/PhysicalColumnBlueprint.php"
    - "src/BusinessSchema/Domain/PhysicalIndexBlueprint.php"
    - "src/BusinessSchema/Domain/PhysicalSchemaBlueprint.php"
    - "src/BusinessSchema/Domain/PhysicalTableBlueprint.php"
    - "src/BusinessSchema/Domain/SchemaEvolutionHints.php"
    - "src/BusinessSchema/Domain/SchemaInstallation.php"
    - "src/BusinessSchema/Domain/SchemaOperation.php"
    - "src/BusinessSchema/Domain/SchemaPlan.php"
    - "src/BusinessSchema/Domain/SchemaPlanStep.php"
    - "src/BusinessSchema/Domain/SchemaRecoveryEvidence.php"
    - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaInstallationRepository.php"
    - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaPlanRepository.php"
    - "src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaRecoveryEvidenceRepository.php"
    - "src/BusinessSchema/Infrastructure/Schema/CanonicalDefinitionPhysicalSchemaCompiler.php"
    - "src/BusinessSchema/Infrastructure/Schema/DoctrinePhysicalSchemaGateway.php"
    - "src/BusinessSecurity/Application/Administration/BusinessSecurityAdministrationService.php"
    - "src/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessController.php"
    - "src/BusinessSurface/Application/BusinessBulkMutation.php"
    - "src/BusinessSurface/Application/BusinessMutationPlanService.php"
    - "src/BusinessSurface/Application/BusinessOperationStatusService.php"
    - "src/BusinessSurface/Application/BusinessSurfaceCatalog.php"
    - "src/BusinessSurface/Application/BusinessSurfaceService.php"
    - "src/BusinessSurface/Application/Custom/CustomBusinessActionHandlerRegistry.php"
    - "src/BusinessSurface/Application/Custom/CustomBusinessActionLedgerResult.php"
    - "src/BusinessSurface/Application/Custom/CustomBusinessReferenceRegistry.php"
    - "src/BusinessSurface/Application/Custom/CustomBusinessSurfaceDispatcher.php"
    - "src/BusinessSurface/Application/Custom/CustomBusinessViewHandlerRegistry.php"
    - "src/BusinessSurface/Application/CustomBusinessActionExecutor.php"
    - "src/BusinessSurface/Application/FieldModelPresenter.php"
    - "src/BusinessSurface/Presentation/Field/CoreFieldPresenter.php"
    - "src/BusinessSurface/Presentation/Field/FieldPresentationCoverage.php"
    - "src/BusinessSurface/Presentation/Field/FieldPresentationInputFactory.php"
    - "src/BusinessSurface/Presentation/Field/FieldPresentationRegistry.php"
    - "src/BusinessSurface/Presentation/Field/RegistryFieldModelPresenter.php"
    - "src/Delivery/Console/Command/ManageBusinessDefinitionsCommand.php"
    - "src/Delivery/Console/Command/ManageBusinessRecordsCommand.php"
    - "src/Delivery/Http/Api/Business/BusinessApiResponder.php"
    - "src/Delivery/Http/Api/Business/BusinessRecordApiHandler.php"
    - "src/Demo/Application/DemoBusinessTemplateProjector.php"
    - "src/Demo/Application/VdmBusinessManifestProjector.php"
    - "src/Demo/Application/VdmBusinessOperationGuard.php"
    - "src/Demo/Infrastructure/DemoBusinessProfileExporter.php"
    - "src/Demo/Infrastructure/DemoContentProfileInstaller.php"
    - "src/Demo/Infrastructure/DemoProfileExporter.php"
    - "src/Demo/Infrastructure/DemoProfileInstaller.php"
    - "src/Demo/Infrastructure/FilesystemDemoManifestCatalog.php"
    - "src/Demo/Infrastructure/VdmBusinessDemoInstaller.php"
    - "src/Extension/Application/Trust/TrustStore.php"
    - "src/Extension/Contribution/BusinessContributionSurface.php"
    - "src/Extension/Contribution/CanonicalManifestActivator.php"
    - "src/Extension/Contribution/CanonicalManifestInterpreter.php"
    - "src/Extension/Contribution/CoreContributionRegistrar.php"
    - "src/Extension/Contribution/CoreExtensionContributions.php"
    - "src/Extension/Contribution/ExtensionContributionRegistrySet.php"
    - "src/Extension/Contribution/OwnedExtensionBindingRegistrar.php"
    - "src/Extension/Infrastructure/DoctrineExtensionManager.php"
    - "src/Extension/Runtime/ActiveExtensionSet.php"
    - "src/Extension/Runtime/ExtensionRuntimeLoader.php"
    - "src/Infrastructure/Mcp/KumweMcpHandlers.php"
    - "src/Infrastructure/Mcp/McpToolErrorVocabulary.php"
    - "src/Infrastructure/Persistence/Migration/BusinessTransactionalRuntimeMigration.php"
    - "src/Kernel/ContainerFactory.php"
    - "src/Kernel/DeferredBusinessSchemaObserver.php"
    - "src/OpenApi/Application/OpenApiComponentClaimAdmission.php"
    - "src/OpenApi/Application/OpenApiExtensionActivationAdmission.php"
    - "tests/Architecture/ConvertedMoneySurfaceCoverageTest.php"
    - "tests/Architecture/MoneyConversionBoundaryTest.php"
    - "tests/Architecture/UnitConversionBoundaryTest.php"
    - "tests/Functional/BusinessSurface/GeneratedBusinessAdapterParityTest.php"
    - "tests/Functional/BusinessSurface/GeneratedBusinessDataEntryRetentionTest.php"
    - "tests/Functional/Extension/LiveSurfaceContractParityTest.php"
    - "tests/Integration/BusinessDefinition/BusinessDefinitionRuntimeIntegrationTest.php"
    - "tests/Integration/BusinessDefinition/OpenApiDefinitionPublicationAdmissionIntegrationTest.php"
    - "tests/Integration/BusinessRecord/AggregateDocumentConcurrencyIntegrationTest.php"
    - "tests/Integration/BusinessRecord/AggregateDocumentIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessNumberSequenceContentionIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessNumberSequenceHotPathIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessNumberSequenceIdentityIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordClientReferenceIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordEvolutionIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordInverseRelationshipIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordMutationGenerationIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordPolicyCompilerIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordPolicyEnforcementIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordRelationshipIntegrationTest.php"
    - "tests/Integration/BusinessRecord/BusinessRecordRuntimeIntegrationTest.php"
    - "tests/Integration/BusinessRecord/CorrectionAfterPeriodCloseIntegrationTest.php"
    - "tests/Integration/BusinessRecord/DocumentCommitInstrumentationIntegrationTest.php"
    - "tests/Integration/BusinessRecord/FiscalPeriodSequenceIntegrationTest.php"
    - "tests/Integration/BusinessRecord/ImmutableRecordReversalIntegrationTest.php"
    - "tests/Integration/BusinessRecord/RecordSecretRotationIntegrationTest.php"
    - "tests/Integration/BusinessSchema/BusinessSchemaColumnRelaxationIntegrationTest.php"
    - "tests/Integration/BusinessSchema/BusinessSchemaExecutionStateGuardIntegrationTest.php"
    - "tests/Integration/BusinessSchema/BusinessSchemaRecoveryIntegrationTest.php"
    - "tests/Integration/BusinessSchema/BusinessSchemaRuntimeIntegrationTest.php"
    - "tests/Integration/BusinessSchema/BusinessSchemaSourceBindingRecoveryIntegrationTest.php"
    - "tests/Integration/BusinessSurface/GeneratedBusinessActionExposureIntegrationTest.php"
    - "tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php"
    - "tests/Integration/BusinessSurface/GeneratedBusinessQueryBudgetIntegrationTest.php"
    - "tests/Integration/BusinessSurface/GeneratedBusinessRelatedPolicyIntegrationTest.php"
    - "tests/Integration/Demo/Infrastructure/DemoBusinessProfileExportInstallIntegrationTest.php"
    - "tests/Integration/Demo/Infrastructure/DemoBusinessProfileExporterTest.php"
    - "tests/Integration/Extension/AssetInspectionCustomViewIntegrationTest.php"
    - "tests/Integration/Performance/HotPlanRegressionIntegrationTest.php"
    - "tests/Integration/Presentation/McpThemeIntegrationTest.php"
    - "tests/Support/AssetInspectionDeploymentAcceptance.php"
    - "tests/Support/BusinessRuntimeBackupAcceptance.php"
    - "tests/Support/McpHandlersFixture.php"
    - "tests/Support/NeutralBusinessFixture.php"
    - "tests/Support/TestKernelFactory.php"
    - "tests/Support/TransientBusinessDefinitionFixtureScope.php"
    - "tests/Support/prepare-browser-contribution.php"
    - "tests/Unit/Application/Authorization/AdapterAuthorizationParityTest.php"
    - "tests/Unit/BusinessDefinition/Administrator/BusinessDefinitionFormMapperTest.php"
    - "tests/Unit/BusinessDefinition/Application/AllocatedNumberFieldRuleTest.php"
    - "tests/Unit/BusinessDefinition/Application/BoundedJsonDefaultBudgetTest.php"
    - "tests/Unit/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzerTest.php"
    - "tests/Unit/BusinessDefinition/Application/FiscalPeriodSequenceCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/NumberSequenceScopeCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/ReversalRelationshipValidationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/AggregateInvariantDeclarationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/CustomBusinessDefinitionReferenceTest.php"
    - "tests/Unit/BusinessDefinition/Domain/DocumentViewDefinitionTest.php"
    - "tests/Unit/BusinessDefinition/Domain/EntityTypeDefinitionTest.php"
    - "tests/Unit/BusinessDefinition/Domain/ExpressionLineAggregateTest.php"
    - "tests/Unit/BusinessDefinition/Domain/ExpressionPropertyTest.php"
    - "tests/Unit/BusinessDefinition/Domain/ExpressionTest.php"
    - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
    - "tests/Unit/BusinessDefinition/Domain/NumberSequenceFormatTest.php"
    - "tests/Unit/BusinessDefinition/Domain/WorkflowBindingTest.php"
    - "tests/Unit/BusinessDefinition/Infrastructure/DoctrineBusinessDefinitionRepositoryTest.php"
    - "tests/Unit/BusinessDefinition/Infrastructure/DoctrinePersistedFieldTypeDefinitionResolverTest.php"
    - "tests/Unit/BusinessRecord/Application/AggregateInvariantValidationTest.php"
    - "tests/Unit/BusinessRecord/Application/BusinessRecordMutationPublicationTest.php"
    - "tests/Unit/BusinessRecord/Application/BusinessRecordRelationshipCoordinatorTest.php"
    - "tests/Unit/BusinessRecord/Application/BusinessRecordViewTest.php"
    - "tests/Unit/BusinessRecord/Application/PostingPeriodLockTest.php"
    - "tests/Unit/BusinessRecord/Application/RecordRuleValidatorTest.php"
    - "tests/Unit/BusinessRecord/Domain/ExactValueCodecTest.php"
    - "tests/Unit/BusinessRecord/Domain/RecordIntegrityTest.php"
    - "tests/Unit/BusinessRecord/Domain/RecordScopeTest.php"
    - "tests/Unit/BusinessRecord/NeutralBusinessFixtureTest.php"
    - "tests/Unit/BusinessReporting/ExportArtifactTest.php"
    - "tests/Unit/BusinessReporting/RecordExportPipelineTest.php"
    - "tests/Unit/BusinessReporting/RecordExportReportProviderTest.php"
    - "tests/Unit/BusinessSchema/Domain/SchemaEvolutionHintsTest.php"
    - "tests/Unit/BusinessSchema/Infrastructure/CanonicalDefinitionPhysicalSchemaCompilerTest.php"
    - "tests/Unit/BusinessSecurity/Application/BusinessRecordAccessPlanTest.php"
    - "tests/Unit/BusinessSecurity/Application/BusinessSecurityAdministrationServiceTest.php"
    - "tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessControllerTest.php"
    - "tests/Unit/BusinessSurface/Application/BusinessMutationPlanServiceTest.php"
    - "tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php"
    - "tests/Unit/BusinessSurface/Application/BusinessSurfaceServiceTest.php"
    - "tests/Unit/BusinessSurface/Application/Custom/CustomBusinessActionExecutorTest.php"
    - "tests/Unit/BusinessSurface/Application/Custom/CustomBusinessHandlerRegistryTest.php"
    - "tests/Unit/BusinessSurface/Presentation/Field/CoreFieldPresenterTest.php"
    - "tests/Unit/BusinessSurface/Presentation/FieldPresentationRegistryTest.php"
    - "tests/Unit/BusinessSurface/Presentation/RegistryFieldModelPresenterTest.php"
    - "tests/Unit/BusinessSurface/Presentation/SeverityFieldPresenterTest.php"
    - "tests/Unit/Delivery/Http/Api/Business/BusinessOperationStatusApiHandlerTest.php"
    - "tests/Unit/Delivery/Http/Api/Business/BusinessRecordApiHandlerTest.php"
    - "tests/Unit/Demo/Application/DemoBusinessTemplateProjectorTest.php"
    - "tests/Unit/Demo/Application/VdmBusinessManifestProjectorTest.php"
    - "tests/Unit/Demo/Application/VdmBusinessOperationGuardTest.php"
    - "tests/Unit/Demo/Infrastructure/FilesystemDemoManifestCatalogTest.php"
    - "tests/Unit/Demo/Infrastructure/VdmBusinessDefinitionSchemaTest.php"
    - "tests/Unit/Demo/Infrastructure/VdmBusinessDemoInstallerTest.php"
    - "tests/Unit/Extension/Contribution/CustomBusinessHandlerBindingTest.php"
    - "tests/Unit/OpenApi/Application/OpenApiComponentClaimAdmissionTest.php"
    - "tests/Unit/OpenApi/Application/OpenApiExtensionActivationAdmissionTest.php"
    - "tests/Unit/Support/TransientBusinessDefinitionFixtureScopeTest.php"
    - "composer.json"
    - "composer.lock"
    - "src/Kernel/ContainerFactory.php"
    - "docs/architecture/capability-index.md"
  files_to_remove:
    - "src/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzer.php"
    - "src/BusinessDefinition/Application/BusinessDefinitionContributionRegistry.php"
    - "src/BusinessDefinition/Application/BusinessDefinitionValidator.php"
    - "src/BusinessDefinition/Application/DefinitionCatalogEntry.php"
    - "src/BusinessDefinition/Application/DefinitionDraft.php"
    - "src/BusinessDefinition/Application/DefinitionVersionRecord.php"
    - "src/BusinessDefinition/Application/FieldTypeDefinitionResolver.php"
    - "src/BusinessDefinition/Application/FieldTypeRegistry.php"
    - "src/BusinessDefinition/Domain/ActionDefinition.php"
    - "src/BusinessDefinition/Domain/BuiltInFieldTypes.php"
    - "src/BusinessDefinition/Domain/CanonicalDefinitionJson.php"
    - "src/BusinessDefinition/Domain/CompatibilityChange.php"
    - "src/BusinessDefinition/Domain/CompatibilityClassification.php"
    - "src/BusinessDefinition/Domain/CompatibilityPlan.php"
    - "src/BusinessDefinition/Domain/ComputationMode.php"
    - "src/BusinessDefinition/Domain/DefinitionOwner.php"
    - "src/BusinessDefinition/Domain/DefinitionOwnerType.php"
    - "src/BusinessDefinition/Domain/DefinitionStatus.php"
    - "src/BusinessDefinition/Domain/DeleteBehavior.php"
    - "src/BusinessDefinition/Domain/DocumentViewDefinition.php"
    - "src/BusinessDefinition/Domain/EntityTypeDefinition.php"
    - "src/BusinessDefinition/Domain/Expression.php"
    - "src/BusinessDefinition/Domain/FieldDefinition.php"
    - "src/BusinessDefinition/Domain/FieldTypeDefinition.php"
    - "src/BusinessDefinition/Domain/IdentityStrategy.php"
    - "src/BusinessDefinition/Domain/InvalidBusinessDefinition.php"
    - "src/BusinessDefinition/Domain/LocalizedDefinitionText.php"
    - "src/BusinessDefinition/Domain/PortalOperation.php"
    - "src/BusinessDefinition/Domain/RecordInvariantDefinition.php"
    - "src/BusinessDefinition/Domain/RelationshipDefinition.php"
    - "src/BusinessDefinition/Domain/RelationshipKind.php"
    - "src/BusinessDefinition/Domain/ScopeMode.php"
    - "src/BusinessDefinition/Domain/Sensitivity.php"
    - "src/BusinessDefinition/Domain/StorageMode.php"
    - "src/BusinessDefinition/Domain/ViewDefinition.php"
    - "src/BusinessDefinition/Domain/WorkflowBinding.php"
  tests_to_remove:
    - "tests/Unit/BusinessDefinition/Application/AllocatedNumberFieldRuleTest.php"
    - "tests/Unit/BusinessDefinition/Application/BoundedJsonDefaultBudgetTest.php"
    - "tests/Unit/BusinessDefinition/Application/BusinessDefinitionCompatibilityAnalyzerTest.php"
    - "tests/Unit/BusinessDefinition/Application/FiscalPeriodSequenceCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/NumberSequenceScopeCoherenceTest.php"
    - "tests/Unit/BusinessDefinition/Application/ReversalRelationshipValidationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/AggregateInvariantDeclarationTest.php"
    - "tests/Unit/BusinessDefinition/Domain/CustomBusinessDefinitionReferenceTest.php"
    - "tests/Unit/BusinessDefinition/Domain/DocumentViewDefinitionTest.php"
    - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
    - "tests/Unit/BusinessDefinition/Domain/WorkflowBindingTest.php"
  tests_to_retain_or_add:
    - "tests/Unit/BusinessDefinition/Domain/EntityTypeDefinitionTest.php: Keep SDK presentation-profile budget and FieldPresentationInput integration methods, plus runtime invariant assertions until native cutover."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionLineAggregateTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionPropertyTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
    - "tests/Unit/BusinessDefinition/Domain/ExpressionTest.php: App still executes the old PHP implementation until verified native computation cutover; then remove duplicated semantics tests."
    - "Database publication and immutable version history across MariaDB, MySQL and PostgreSQL"
    - "Authorization, approval, step-up, audit, transaction and trusted extension generation"
    - "SDK presentation configuration profile adapter and integration"
    - "Compiled-plan invalidation, runtime native readiness, recovery and backup/restore"
    - "Handlers and graphical adapters; active App PHP executor tests until native cutover"
  di_or_provisioning_changes:
    - "Register ConfigProvider and explicit host SDK-backed FieldConfigurationAdmission; remove old service ownership"
    - "Preserve trusted generation assembly and container/operation scope"
    - "Replace runtime evaluation through verified native Computation boundary; no PHP fallback or alias"
  capability_index_changes:
    - "Regenerate capability index and core-growth/migration ownership records from exact verified locked package"
  changelog_and_evidence_changes:
    - "KUMWE-MIG-2026-010 / KUMWE-CS-2026-010 / NRM-2026-012; no functional roadmap completion claim"
  verification_commands:
    - "bash tools/agent-setup.sh"
    - "composer qa"
    - "composer kumwe:capability-index"
    - "composer kumwe:core-growth-check"
    - "Full three-database publication/history, extension trust/disable, recovery/backup and affected delivery suites"
concurrency:
  likely_conflict_files:
    - "composer.json"
    - "composer.lock"
    - "src/Kernel/ContainerFactory.php"
    - "docs/architecture/capability-index.md"
    - "CHANGELOG.md"
    - "BusinessDefinition/BusinessRecord/BusinessReporting runtime consumers"
  related_migrations:
    - "KUMWE-MIG-2026-002"
    - "KUMWE-MIG-2026-005"
    - "KUMWE-MIG-2026-008"
  ownership_conflicts:
    - "SDK presentation profile remains upstream; host adapter mandatory"
    - "Formula meaning is Business Definition; allocation/execution is Computation/Engine/native binding"
    - "DefinitionOwner site dimension differs from ContributionOwner"
  integration_train: null
  resolution_rule: "semantic-preservation"
governance:
  roadmap_source_sha256: "a202155ef1a65f5ab293d4f8397ebf4ac430db7f1e877c776bbe7851e6fe18d8"
  roadmap_refs: []
  non_roadmap_refs:
    - "NRM-2026-012"
  completion_claim: false
decisions:
  - "BUSDEF-001: preserve Core/Extension/Site owner and existing extension grammar; no unused Contribution dependency"
  - "BUSDEF-002: mandatory host presentation admission port, with no permissive/default implementation"
  - "BUSDEF-003: semantic package removes evaluate/isSatisfied methods; non-distributed oracle and normative corpus preserve semantics"
  - "BUSDEF-004: exact published Sequence and Localization pins do not themselves establish an independent release attestation"
  - "BUSDEF-005: 36 existing types plus one port, one provider and three factories = 41 public types"
blockers:
  - "The 0.1.1 maintenance branch requires maintainer review and merge before release automation"
  - "Version 0.1.0 is published; independent verification of the final 0.1.1 release and dependency closure remains separate"
  - "App runtime cutover requires verified native chain and readiness; no fallback is shipped"
---

# Business Definition migration handoff

This Phase 1 candidate changes only kumwe/business-definition. The complete original 36-type mapping and 109
production/106 test-consumer closure above identify later App adoption work; no App source or test has been removed.
The two runtime methods are an explicit native-boundary API break and cannot be adopted by simply deleting the old
executor.

The package owns 41 exported types and its behavior, boundary, conformance, source architecture, public API and
archive checks. The exact semantic corpus and original-source provenance are package-owned. Engine and PHPT must prove
execution parity independently. tests/ownership.json owns executable test transfers; docs/test-ownership.md explains
host responsibility.

Run composer check on the final PR tree. Upstream verification, protected-main immutable publication and fresh
independent package attestation remain separate gates. resources/release-readiness.json records published
dependency identities without replacing independent verification.

## Migration/implementation summary

The portable closure is 28 domain types and eight application types. One mandatory admission port, one provider and
three explicit factories bring the public surface to 41. Phase 1 changes only this repository; App still owns and runs
its current implementations until separate verified adoption.

## Public API and responsibility

The source-reflected API, capability and service-map manifests plus docs/public-api.md describe every exported member.
Definition metadata, validation, compatibility and semantic profiles belong here. Native runtime classes and execution
contracts belong to their named owners in resources/native-ownership/v1.json. No PHP evaluator or decimal
implementation ships in the consumer archive.

## Capability reuse/semantic input review

Reviewed App's exact locked Conversion 0.1.2, SDK 0.2.4 and Producer 0.2.0 APIs and the prepared extraction closure.
Localization supplies locale semantics; Sequence supplies proven format/scope/reset types. Their exact published
pins do not assert independently verified releases. DefinitionOwner's site dimension and persisted grammar are
distinct from
ContributionOwner. Mandatory FieldConfigurationAdmission keeps SDK profile implementation at its existing owner and
host composition boundary.

## Consumer inventory

The front matter and docs/consumer-inventory.json enumerate 109 external production consumers and 106 test/support
references against App 960ce8ec00cf724a7cae03e5ba09c4852c9ab54e. This is an adoption inventory, not a blanket deletion
list. Re-scan imports, signatures, fully qualified strings, container entries and fixtures before adoption.

## Test ownership

The exact 15 file transfers/splits appear in tests/ownership.json and docs/test-ownership.md. Package class behavior,
boundary refusal and normative corpora are tested here. App retains real persistence/publication, authorization,
trusted lifecycle, transaction, recovery and delivery behavior. SDK profile integration and active App execution tests
remain until their corresponding composed implementation changes. Sequence's formatter matrix is not duplicated here.

## Next-task execution notes

First independently verify selected upstream releases and update the release-readiness gate with reviewed evidence.
After human merge and immutable publication, a fresh independent verifier must attest this package archive. The App
adoption then implements the mandatory SDK-backed admission adapter, uses canonical package types, registers explicit
provider/factories, updates Composer and ownership indexes, and deletes migrated implementations and duplicate class
tests together. Runtime method removal waits for verified Computation/native execution and fails readiness when
missing; no fallback is permitted.

## Drift check

Compare all source_path and transfer-test entries against exact App baseline 960ce8ec00cf724a7cae03e5ba09c4852c9ab54e.
New portable behavior must be brought into this owner and independently released before App adopts it. Preserve
host-only changes separately and resolve shared configuration/Composer conflicts semantically; never select an entire
side of a conflict.

## Validation recipe and observed local results

Run composer check on 64-bit PHP 8.5. Imported and new PHPUnit behavior/boundary/conformance tests, source
architecture, real discovered test-ownership checks, strict static analysis and package manifests are local gates.
Corpus provenance binds six exact App source hashes and two frozen oracle files; an independent reviewer confirmed
their bytes and namespace-only oracle transformation. The archive consumer uses a real built ZIP installed as a no-dev
authoritative dependency. Local Packagist advisory lookup times out; CI retains the mandatory online audit, and final
CI results are the authoritative security gate. The published 0.1.0 release is observed; no native performance
result or independent attestation is claimed.

Final local implementation evidence: 134 PHPUnit cases / 1,747 assertions; 41 API exports; 125 named discovered test
methods; 16 architecture, nine ownership and 92 release-integrity negative fixtures; 12 release-parser cases. The
72-file archive passes no-dev authoritative installation, canonical autoload and real Laminas composition using
the corrected runtime requirements; all 41 public symbols are present and no development paths are shipped.

## Definition admission corrections

The reviewed candidate now rejects non-boolean field visibility/editability conditions and field expression reads
whose concrete scalar declarations contradict registered or computed field families. This is an intentional
validation correction: previously admitted contradictory definitions require correction before revalidation.
Decimal/temporal string representations and dynamic any/null checks remain unchanged. Entity label translation
changes now appear as behavior-changing compatibility changes. The runtime explicitly requires ext-mbstring, and
the archived example exercises text default admission. Public signatures and the frozen native formula corpus
remain unchanged. App adoption follows independent verification of the final maintenance release.

## Maintenance review 2026-09-07

The current App baseline 24ecf956423c18933e824b43cea1bfb9127a79a9 has no additional
changes under BusinessDefinition source or unit tests compared with this extraction baseline.
The library retains the full 41-symbol public closure and frozen semantic corpus. Its internal
array snapshot helper prevents caller references from changing immutable definitions; canonical
encoding does not write through caller references. New regression tests belong to this package.

Version 0.1.0 is published at f3b86f8af1469066637fd1b4b8f71c4df634adf8. This PR prepares
0.1.1 and uses published Localization 0.1.1 and Sequence 0.2.1. These are exact pins, with
grouped update PRs for later releases. Dependent libraries remain on their coherent published
graph until this maintenance release is merged, published and independently verified.
