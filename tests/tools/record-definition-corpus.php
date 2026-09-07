<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition;
use Kumwe\BusinessDefinition\Tests\Support\AcceptedConfiguration;
use Kumwe\BusinessDefinition\Tests\Unit\Domain\EntityTypeDefinitionTest;

$base = EntityTypeDefinitionTest::document();
$cases = [['id' => 'baseline-definition', 'definitions' => [$base]]];
$modify = static function (string $id, callable $change) use (&$cases, $base): void {
    $value = $base;
    $change($value);
    $cases[] = ['id' => $id, 'definitions' => [$value]];
};
$modify('invalid-owner', static function (array &$v): void {
    $v['owner']['identifier'] = 'other';
});
$modify('unknown-field-type', static function (array &$v): void {
    $v['fields'][1]['type'] = 'unknown.field';
});
$modify('identity-not-required', static function (array &$v): void {
    $v['fields'][0]['required'] = false;
});
$modify('identity-nullable', static function (array &$v): void {
    $v['fields'][0]['nullable'] = true;
});
$modify('secret-default-refused', static function (array &$v): void {
    $v['fields'][1]['sensitivity'] = 'secret';
    $v['fields'][1]['default'] = 'plaintext';
});
$modify('unknown-computation-dependency', static function (array &$v): void {
    $v['fields'][2]['formula']['field'] = 'missing';
});
$modify('computed-cycle', static function (array &$v): void {
    $v['fields'][2]['formula']['field'] = 'normalized_name';
});
$modify('oversized-length', static function (array &$v): void {
    $v['fields'][1]['length'] = 100000;
});
$modify('unknown-view-field', static function (array &$v): void {
    $v['views'][0]['fields'][] = 'missing';
});
$modify('unknown-portal-operation', static function (array &$v): void {
    $v['portal_operations'] = ['execute_php'];
});
$modify('bad-sequence-scope', static function (array &$v): void {
    $v['fields'][1]['type'] = 'core.sequence';
    $v['fields'][1]['configuration'] = ['scope' => 'organization'];
});
$modify('dangling-relationship', static function (array &$v): void {
    $v['relationships'] = [['handle' => 'parent',
        'kind' => 'many_to_one',
        'target' => 'site.default.missing', 'inverse' => null, 'required' => false,
        'delete_behavior' => 'restrict']];
});
$modify('canonical-translations', static function (array &$v): void {
    $v['translations'] = ['singular_label' => ['fr' => 'Actif', 'en' => 'Asset']];
});
$modify('unknown-field-key', static function (array &$v): void {
    $v['fields'][1]['configuration'] = ['unknown' => 'x'];
});
$cases[] = ['id' => 'empty-graph', 'definitions' => []];
$cases[] = ['id' => 'duplicate-graph-handle', 'definitions' => [$base, $base]];
$cases[] = ['id' => 'first-finding-input-order',
    'definitions' => [array_replace($base, ['handle' => 'bad']), array_replace(
        $base,
        ['id' => 'invalid']
    )]];
$validator = new BusinessDefinitionValidator(new FieldTypeRegistry(), new AcceptedConfiguration());
foreach ($cases as &$case) {
    try {
        $definitions = array_map(EntityTypeDefinition::fromArray(...), $case['definitions']);
        $validator->validateGraph($definitions);
        $case['expected'] = ['definitions' => array_map(
            static fn (EntityTypeDefinition $d): array => [
                        'canonical' => \Kumwe\BusinessDefinition\Domain\CanonicalDefinitionJson::encode($d->toArray()),
            'checksum' => $d->checksum()],
            $definitions
        )];
    } catch (InvalidBusinessDefinition $error) {
        $case['expected'] = ['refusal' => 'invalid_definition', 'message' => $error->getMessage()];
    }
}
unset($case);
$compatibility = [];
foreach (
    ['initial',
    'unchanged', 'label-change', 'required-without-default', 'field-removal',
    'portal-change'] as $kind
) {
    $before = $kind === 'initial' ? null : EntityTypeDefinition::fromArray($base)->published(1);
    $after = $base;
    if ($kind === 'label-change') {
        $after['fields'][1]['label'] = 'Renamed';
    }
    if ($kind === 'required-without-default') {
        $after['fields'][] = ['handle' => 'description',
            'label' => 'Description', 'type' => 'core.text', 'required' => true, 'nullable' => false,
            'length' => 100];
    }
    if ($kind === 'field-removal') {
        array_pop($after['fields']);
    }
    if ($kind === 'portal-change') {
        $after['portal_exposure'] = true;
        $after['portal_operations'] = ['browse'];
    }
    $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze($before, EntityTypeDefinition::fromArray($after));
    $compatibility[] = ['id' => $kind,
        'before' => $before?->toArray(), 'after' => $after, 'expected' => $plan->toArray()];
}
$document = ['schema' => 'kumwe-business-definition-corpus/v1',
    'profile' => 'kumwe.business-definition.canonical.v1',
    'admission' => 'fixture-already-admitted; SDK presentation profile is host-owned',
    'definitions' => $cases, 'compatibility' => $compatibility];
file_put_contents(
    dirname(
        __DIR__,
        2
    ) . '/resources/corpus/definition-v1.json',
    json_encode(
        $document,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    ) . "\n"
);
echo count($cases), ' definition and ', count($compatibility), " compatibility vectors recorded.\n";
