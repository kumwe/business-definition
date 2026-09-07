<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Kumwe\BusinessDefinition\Domain\Expression;
use Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition;
use Kumwe\BusinessDefinition\Tests\Oracle\FrozenEvaluator;

$literal = static fn (string $type, mixed $value): array => ['op' => 'literal', 'type' => $type, 'value' => $value];
$field = static fn (string $type, string $name): array => ['op' => 'field', 'type' => $type, 'field' => $name];
$node = static fn (string $op, string $type, array $args): array => compact('op', 'type', 'args');
$cases = [];
$add = static function (string $id, array $expression, array $fields = [], array $lines = []) use (&$cases): void {
    $cases[] = compact('id', 'expression', 'fields', 'lines');
};
foreach (
    ['null' => null, 'boolean' => true, 'integer' => 42, 'decimal' => '-0.00', 'string' => 'é',
    'date' => '2026-09-07', 'time' => '12:34:56', 'datetime' => '2026-09-07T12:34:56Z'] as $type => $value
) {
    $add('literal-' . $type, $literal($type, $value));
    $add('field-' . $type, $field($type, 'value'), ['value' => $value]);
    $add('missing-' . $type, $field($type, 'value'));
}
foreach (['eq', 'ne', 'lt', 'lte', 'gt', 'gte'] as $op) {
    $add($op . '-decimal-spelling', $node($op, 'boolean', [$literal('decimal', '1.10'), $literal('decimal', '1.1')]));
    $add($op . '-integer', $node($op, 'boolean', [$literal('integer', -2), $literal('integer', 3)]));
}
foreach (['add', 'subtract', 'multiply', 'divide'] as $op) {
    $add($op . '-integer', $node($op, 'integer', [$literal('integer', -7), $literal('integer', 3)]));
    $decimal = $node($op, 'decimal', [$literal('decimal', '-10.50'), $literal('decimal', '6')]);
    if ($op === 'divide') {
        $decimal['scale'] = 4;
    }
    $add($op . '-decimal', $decimal);
}
$add('add-big-decimal', $node('add', 'decimal', [$literal('decimal', str_repeat('9', 40)), $literal('decimal', '1')]));
$add(
    'multiply-exact',
    $node(
        'multiply',
        'decimal',
        [$field(
            'decimal',
            'quantity'
        ),
        $field('decimal', 'price')]
    ),
    ['quantity' => '2.00', 'price' => '12345678901234567890.00']
);
$add('negative-zero-arithmetic', $node('add', 'decimal', [$literal('decimal', '-0.00'), $literal('decimal', '0')]));
$add(
    'division-negative-half-away',
    $node('divide', 'decimal', [$literal('decimal', '-1'), $literal(
        'decimal',
        '2'
    )]) + ['scale' => 0]
);
$add('division-zero', $node('divide', 'integer', [$literal('integer', 1), $literal('integer', 0)]));
$add('integer-overflow-add', $node('add', 'integer', [$literal('integer', PHP_INT_MAX), $literal('integer', 1)]));
$add(
    'integer-overflow-divide',
    $node('divide', 'integer', [$literal('integer', PHP_INT_MIN), $literal(
        'integer',
        -1
    )])
);
$add(
    'integer-overflow-multiply',
    $node('multiply', 'integer', [$literal('integer', PHP_INT_MAX), $literal(
        'integer',
        2
    )])
);
$add(
    'decimal-4096-digits',
    $node('add', 'decimal', [$field('decimal', 'value'), $literal(
        'decimal',
        '0'
    )]),
    ['value' => str_repeat('9', 4096)]
);
$add(
    'decimal-4097-digits-refused',
    $node('add', 'decimal', [$field('decimal', 'value'), $literal(
        'decimal',
        '0'
    )]),
    ['value' => str_repeat('9', 4097)]
);
foreach (['and' => false, 'or' => true] as $op => $value) {
    $add($op . '-value', $node($op, 'boolean', [$literal('boolean', $value), $literal('boolean', true)]));
    $add($op . '-eager-missing', $node($op, 'boolean', [$literal('boolean', $value), $field('boolean', 'missing')]));
}
$add('not-value', $node('not', 'boolean', [$literal('boolean', false)]));
$add(
    'if-value',
    $node('if', 'string', [$literal('boolean', true), $literal('string', 'yes'),
    $literal('string', 'no')])
);
$add(
    'if-eager-unused-branch',
    $node('if', 'string', [$literal('boolean', true), $literal('string', 'yes'),
    $field('string', 'missing')])
);
$add(
    'coalesce-value',
    $node('coalesce', 'string', [$field('null', 'value'), $literal(
        'string',
        'fallback'
    )]),
    ['value' => null]
);
$add(
    'coalesce-eager-unused-branch',
    $node('coalesce', 'string', [$literal('string', 'present'), $field(
        'string',
        'missing'
    )])
);
$add('is-null-value', $node('is_null', 'boolean', [$field('any', 'value')]), ['value' => null]);
$add('is-null-missing', $node('is_null', 'boolean', [$field('any', 'value')]));
$add('concat-value', $node('concat', 'string', [$literal('string', 'é'), $literal('string', '💡')]));
$add('contains-value', $node('contains', 'boolean', [$literal('string', 'café'), $literal('string', 'fé')]));
$add(
    'in-decimal-spelling',
    $node('in', 'boolean', [$literal('decimal', '1.00'), $literal(
        'decimal',
        '2'
    ),
    $literal('decimal', '1')])
);
foreach (['integer', 'decimal'] as $type) {
    $sum = ['op' => 'line_aggregate', 'type' => $type, 'lines' => 'lines', 'aggregate' => 'sum', 'field' => 'amount'];
    $add('sum-empty-' . $type, $sum, [], ['lines' => []]);
    $add('sum-missing-' . $type, $sum);
    $add(
        'sum-skip-null-' . $type,
        $sum,
        [],
        ['lines' => [['amount' => $type === 'integer' ? 5 : '5.00'], [],
        ['amount' => null]]]
    );
    $add('sum-invalid-' . $type, $sum, [], ['lines' => [['amount' => 'twelve']]]);
}
$count = ['op' => 'line_aggregate', 'type' => 'integer', 'lines' => 'lines', 'aggregate' => 'count'];
$add('count-empty', $count, [], ['lines' => []]);
$add('count-two', $count, [], ['lines' => [[], []]]);
$add('count-missing', $count);
$sum = ['op' => 'line_aggregate', 'type' => 'decimal', 'lines' => 'lines', 'aggregate' => 'sum', 'field' => 'amount'];
$invariant = $node('eq', 'boolean', [$field('decimal', 'total'), $sum]);
$add(
    'invariant-total-agrees',
    $invariant,
    ['total' => '30.750'],
    ['lines' => [['amount' => '10.25'], ['amount' => '20.50']]]
);
$add(
    'invariant-total-disagrees',
    $invariant,
    ['total' => '30.76'],
    ['lines' => [['amount' => '10.25'], ['amount' => '20.50']]]
);
$add('sum-thousand-exact-lines', $sum, [], ['lines' => array_fill(0, 1000, ['amount' => '0.10'])]);
$add('wrong-runtime-type', $field('integer', 'value'), ['value' => '1']);
$add('float-runtime-refused', $field('decimal', 'value'), ['value' => 1.25]);
foreach (['eval', 'php', 'sql', 'twig', 'javascript', 'shell_exec'] as $op) {
    $add('forbidden-' . $op, $node($op, 'string', []));
}
$add('unknown-property', $literal('integer', 1) + ['php' => 'return 1;']);
$add('mixed-arithmetic-types', $node('add', 'integer', [$literal('integer', 1), $literal('decimal', '2')]));
$add('divide-missing-scale', $node('divide', 'decimal', [$literal('decimal', '1'), $literal('decimal', '2')]));
$add(
    'divide-scale-overflow',
    $node('divide', 'decimal', [$literal('decimal', '1'), $literal(
        'decimal',
        '2'
    )]) + ['scale' => 31]
);
$add('wrong-arity', $node('not', 'boolean', [$literal('boolean', true), $literal('boolean', false)]));
$add('unsupported-reduction', array_replace($sum, ['aggregate' => 'average']));
$add('count-field-forbidden', $count + ['field' => 'amount']);
$add('literal-float-refused', $literal('decimal', 12.5));
$add('literal-oversized', $literal('string', str_repeat('x', 4097)));
$deep = $literal('boolean', true);
for ($i = 0; $i < 13; ++$i) {
    $deep = $node('not', 'boolean', [$deep]);
}
$add('depth-refused', $deep);
$wide = $literal('boolean', true);
for ($i = 0; $i < 8; ++$i) {
    $wide = $node('and', 'boolean', [$wide, $wide]);
}
$add('node-budget-refused', $wide);
foreach ($cases as &$case) {
    $phase = 'parse';
    try {
        $expression = Expression::fromArray($case['expression']);
        $case['canonical'] = $expression->toArray();
        $case['dependencies'] = $expression->dependencies();
        $case['line_dependencies'] = (object) $expression->lineDependencies();
        $phase = 'evaluate';
        $case['expected'] = ['value' => FrozenEvaluator::evaluate($expression, $case['fields'], $case['lines'])];
    } catch (InvalidBusinessDefinition $error) {
        $case['expected'] = ['refusal' => $phase === 'parse' ? 'invalid_ast' : 'evaluation_refused',
            'phase' => $phase, 'message' => $error->getMessage()];
    }
    $case['fields'] = (object) $case['fields'];
    $case['lines'] = (object) $case['lines'];
}
unset($case);
$document = ['schema' => 'kumwe-business-definition-formula-corpus/v1',
    'profile' => 'kumwe.business-definition.formula.v1', 'integer_bits' => 64,
    'vectors' => $cases];
file_put_contents(
    dirname(
        __DIR__,
        2
    ) . '/resources/corpus/formula-v1.json',
    json_encode(
        $document,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    ) . "\n"
);
echo count($cases), " frozen formula vectors recorded.\n";
