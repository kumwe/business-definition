<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ValueImmutabilityTest extends TestCase
{
    public function testFieldDefinitionDetachesNestedDefaultsConfigurationAndValidators(): void
    {
        $value = 'approved';
        $field = new \Kumwe\BusinessDefinition\Domain\FieldDefinition(
            'payload',
            'Payload',
            'core.json',
            default: ['state' => &$value],
            configuration: ['choices' => [&$value]],
            validators: [['rule' => 'length', 'value' => &$value]],
        );
        $before = $field->toArray();
        $value = 'changed';
        self::assertSame($before, $field->toArray());
    }

    public function testCanonicalEncodingNeverMutatesReferencedCallerValues(): void
    {
        $nested = ['z' => 2, 'a' => 1];
        $input = ['value' => &$nested];
        $before = $nested;
        self::assertSame(
            '{"value":{"a":1,"z":2}}',
            \Kumwe\BusinessDefinition\Domain\CanonicalDefinitionJson::encode($input)
        );
        self::assertSame($before, $nested);
    }

    public function testFieldTypeKeysCannotChangeAfterAdmission(): void
    {
        $key = 'choices';
        $type = new \Kumwe\BusinessDefinition\Domain\FieldTypeDefinition(
            'acme.custom',
            'Custom',
            'Custom field',
            'string',
            'string',
            [&$key]
        );
        $key = 'sql';
        self::assertSame(['choices'], $type->configurationKeys);
    }
}
