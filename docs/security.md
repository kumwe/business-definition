# Security and compatibility

Report vulnerabilities privately through GitHub security reporting for this repository. Unknown operators, executable
declarations, malformed types, deep/oversized trees and incompatible definition graphs fail closed. PHP source never
evaluates a formula. Inputs are declarative values; authorization remains the host responsibility.

Version 0.1.0 intentionally removes Expression::evaluate and RecordInvariantDefinition::isSatisfied from the extracted
API, and requires an explicit field admission collaborator. Canonical persisted metadata and DefinitionOwner grammar
remain preserved. Pre-1.0 dependencies require exact independently verified versions before release.
