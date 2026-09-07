<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Unit\Application;

use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Kumwe\BusinessDefinition\Domain\FieldDefinition;
use Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition;
use Kumwe\BusinessDefinition\Tests\Support\AcceptedConfiguration;
use Kumwe\BusinessDefinition\Tests\Unit\Domain\EntityTypeDefinitionTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BusinessDefinitionValidator::class)]
#[CoversClass(FieldDefinition::class)]
final class ExpressionFieldValidationTest extends TestCase
{
    public function testFieldConditionsRequireBooleanResults(): void
    {
        foreach (['visibility_condition', 'editability_condition'] as $property) {
            foreach ([['integer', 123], ['string', 'false'], ['null', null]] as [$type, $value]) {
                try {
                    FieldDefinition::fromArray([
                        'handle' => 'input', 'label' => 'Input', 'type' => 'core.text',
                        $property => ['op' => 'literal', 'type' => $type, 'value' => $value],
                    ]);
                    self::fail('A non-boolean field condition was accepted.');
                } catch (InvalidBusinessDefinition $error) {
                    self::assertSame('A business field condition must produce boolean.', $error->getMessage());
                }
            }
            $field = FieldDefinition::fromArray([
                'handle' => 'input', 'label' => 'Input', 'type' => 'core.text',
                $property => ['op' => 'literal', 'type' => 'boolean', 'value' => false],
            ]);
            self::assertSame('boolean', $field->toArray()[$property]['type']);
        }
    }

    public function testGraphRejectsIncompatibleFieldReadsInEveryExpressionContext(): void
    {
        $condition = ['op' => 'eq', 'type' => 'boolean', 'args' => [
            ['op' => 'field', 'type' => 'integer', 'field' => 'name'],
            ['op' => 'literal', 'type' => 'integer', 'value' => 1],
        ]];
        foreach (['formula', 'visibility_condition', 'editability_condition', 'action', 'invariant'] as $context) {
            $document = EntityTypeDefinitionTest::document();
            if ($context === 'action') {
                $document['actions'] = [[
                    'handle' => 'inspect', 'label' => 'Inspect', 'capability' => 'asset.inspect',
                    'condition' => $condition,
                ]];
            } elseif ($context === 'invariant') {
                $document['record_invariants'] = [[
                    'handle' => 'check', 'message' => 'Check the value.', 'condition' => $condition,
                ]];
            } else {
                $document['fields'][] = [
                    'handle' => 'probe', 'label' => 'Probe',
                    'type' => $context === 'formula' ? 'core.computed' : 'core.text',
                    'computed' => $context === 'formula', 'server_only' => true, 'read_only' => true,
                    $context => $condition,
                ];
            }
            $definition = EntityTypeDefinition::fromArray($document);
            try {
                (new BusinessDefinitionValidator(new FieldTypeRegistry(), new AcceptedConfiguration()))
                    ->validateGraph([$definition]);
                self::fail('A field read with an incompatible scalar family was accepted: ' . $context);
            } catch (InvalidBusinessDefinition $error) {
                self::assertSame(
                    'Expression field name declares integer but its field supplies string.',
                    $error->getMessage(),
                );
            }
        }
    }

    public function testGraphPreservesMatchingScalarAndNullableExpressionDeclarations(): void
    {
        foreach (
            [
            ['core.text', 'string'], ['core.text', 'any'], ['core.text', 'null'],
            ['core.integer', 'integer'], ['core.boolean', 'boolean'],
            ['core.decimal', 'decimal'], ['core.decimal', 'string'],
            ['core.date', 'date'], ['core.local_time', 'time'], ['core.instant', 'datetime'],
            ] as [$fieldType, $expressionType]
        ) {
            $document = EntityTypeDefinitionTest::document();
            $input = ['handle' => 'input', 'label' => 'Input', 'type' => $fieldType];
            if ($fieldType === 'core.decimal') {
                $input['precision'] = 18;
                $input['scale'] = 2;
            }
            $document['fields'][] = $input;
            $document['fields'][] = [
                'handle' => 'output', 'label' => 'Output', 'type' => 'core.computed',
                'computed' => true, 'server_only' => true, 'read_only' => true,
                'formula' => ['op' => 'field', 'type' => $expressionType, 'field' => 'input'],
            ];
            $definition = EntityTypeDefinition::fromArray($document);
            (new BusinessDefinitionValidator(new FieldTypeRegistry(), new AcceptedConfiguration()))
                ->validateGraph([$definition]);
            self::assertSame(['input'], $definition->dependencyGraph()['fields']['output']);
        }
    }

    public function testGraphUsesTheComputedFieldResultFamily(): void
    {
        $document = EntityTypeDefinitionTest::document();
        $document['fields'][] = [
            'handle' => 'number', 'label' => 'Number', 'type' => 'core.computed',
            'computed' => true, 'server_only' => true, 'read_only' => true,
            'formula' => ['op' => 'literal', 'type' => 'integer', 'value' => 7],
        ];
        $document['fields'][] = [
            'handle' => 'output', 'label' => 'Output', 'type' => 'core.computed',
            'computed' => true, 'server_only' => true, 'read_only' => true,
            'formula' => ['op' => 'field', 'type' => 'string', 'field' => 'number'],
        ];
        $definition = EntityTypeDefinition::fromArray($document);
        $this->expectException(InvalidBusinessDefinition::class);
        $this->expectExceptionMessage('Expression field number declares string but its field supplies integer.');
        (new BusinessDefinitionValidator(new FieldTypeRegistry(), new AcceptedConfiguration()))
            ->validateGraph([$definition]);
    }
}
