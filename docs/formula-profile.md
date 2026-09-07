# Formula and canonical definition profiles

`kumwe.business-definition.formula.v1` owns 22 operators: literal, field, line_aggregate, eq, ne, lt, lte, gt, gte,
and, or, not, add, subtract, multiply, divide, concat, coalesce, if, is_null, in and contains. The parser rejects
unknown properties and incorrect arity or declared types. Tree depth is 12, node count is 128 and canonical AST byte
size is 32,768. Decimal division requires an explicit scale from 0 through 30.

Execution is eager in argument order, including and, or, if and coalesce. An unused failing branch refuses the entire
expression. Missing fields and ungathered owned-line collections are errors; they are not null or empty. Supplied
values must match their declared scalar types; floats are refused. Integer arithmetic uses checked signed 64-bit
results. Division truncates integers toward zero. Decimal arithmetic is exact with half-away-from-zero division
rounding; arithmetic normalizes trailing zeroes and negative zero while literal spelling remains intact. The
historical formula arithmetic digit budget is 4,096, and is not interchangeable with Conversion's independently owned
decimal profile.

Line aggregation has exactly count and integer/decimal sum. Empty collections count/sum to zero. A sum skips missing
and null line field values. Record and line dependencies are separately deduplicated and sorted. Definition graph
validation determines whether a collection is owned, a reference exists, a type matches and cycles are absent; it
rejects aggregates in computed fields or action conditions where the baseline forbids them.

The normative corpus carries exact JSON integer tokens with `integer_bits: 64`; consumers must parse integers
losslessly. Each vector contains expression, fields and lines; accepted ASTs add canonical documents and dependency
lists. Expected outcomes are exact values or a refusal phase, stable category and baseline message. Refusal categories
distinguish invalid AST from execution refusal; they do not pretend to be a new collected finding API. Existing
definition validation reports its first failure in supplied order.

`kumwe.business-definition.canonical.v1` is distinct from generic Canonical JSON. It keeps the original depth-32,
512-member collection bound, string-key map sorting, list ordering, UTF-8 JSON flags and float/object/resource
refusal. Definition vectors freeze bytes and SHA256 digests; compatibility vectors freeze ordered classification
plans.

`resources/corpus/provenance-v1.json` binds exact App source hashes, frozen oracle hashes and corpus hashes. The
recorders under tests/tools run only during reviewed semantic updates. CI replays fixed expectations without
regenerating them. The frozen PHP oracle is test-only and excluded from archives and production autoload; Engine and
PHPT own eventual native parity. No native implementation or stable native release is claimed by this profile draft.
