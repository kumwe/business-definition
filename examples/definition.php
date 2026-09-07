<?php

/** Construct and validate a definition using only the consumer's installed package. @since 0.1.0 */

declare(strict_types=1);

use Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;

require $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

$admission = new class implements FieldConfigurationAdmission {
    /**
     * Admit only the empty configuration used by this isolated example.
     * @param array<string, mixed> $configuration Fixture input, not a general production policy.
     * @return void
     * @throws InvalidArgumentException For any non-empty configuration.
     * @since 0.1.0
     */
    public function assertAdmissible(array $configuration): void
    {
        if ($configuration !== []) {
            throw new InvalidArgumentException('This example admits only empty fixture configuration.');
        }
    }
};
$definition = EntityTypeDefinition::fromArray([
    'id' => '018f4f24-98d8-7ad4-8f3f-38c909178b6b',
    'owner' => ['type' => 'site', 'identifier' => 'example'],
    'site' => 'example',
    'handle' => 'site.example.asset',
    'singular_label' => 'Asset',
    'plural_label' => 'Assets',
    'status' => 'draft', 'definition_version' => 0, 'storage_mode' => 'relational',
    'identity_strategy' => 'uuid', 'scope' => 'site',
    'fields' => [[
        'handle' => 'id', 'label' => 'ID', 'type' => 'core.uuid',
        'required' => true, 'nullable' => false, 'unique' => true,
        'immutable_after_create' => true, 'server_only' => true, 'read_only' => true,
    ]],
]);
(new BusinessDefinitionValidator(new FieldTypeRegistry(), $admission))->validateGraph([$definition]);
$plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze(null, $definition);
if ($plan->toChecksum !== $definition->published(1)->checksum()) {
    throw new RuntimeException('Compatibility target does not match canonical definition bytes.');
}
echo 'Definition consumer verified: ', $definition->handle, ', ', $definition->checksum(), "\n";

return [$definition, $admission];
