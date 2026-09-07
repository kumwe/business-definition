# Release gates

This PR remains a draft while Sequence and Localization lack independently verified immutable release evidence.
resources/release-readiness.json keeps publication blocked before tag mutation. CI quality passing does not override
this gate.

A maintainer must protect main and enable immutable releases, verify selected upstream releases independently, update
exact dependency pins/evidence, and review final package gates before merging. Release-on-record checks protected
main, records a version, and asserts the published stable release is immutable. Initial Packagist registration is
maintainer-owned. Agents do not merge or tag.

A fresh independent session verifies the exact package archive, manifests, provenance and authoritative no-dev
consumer and creates the external RELEASE-ATTESTATION.yaml. Only that permits dependent publication or App adoption.
