<?php

/** Prove the distributed provider composes in a real independent Laminas consumer. @since 0.1.0 */

declare(strict_types=1);

use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission;
use Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\ConfigProvider;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Laminas\ServiceManager\ServiceManager;

// The fixture script selects the consumer autoloader and its explicit empty-configuration admission.
require __DIR__ . '/definition.php';
/** @var FieldConfigurationAdmission $admission */
/** @var EntityTypeDefinition $definition */
$container = new ServiceManager((new ConfigProvider())()['dependencies'] + [
    'services' => [FieldConfigurationAdmission::class => $admission],
]);
$validator = $container->get(BusinessDefinitionValidator::class);
if (!$validator instanceof BusinessDefinitionValidator) {
    throw new RuntimeException('The provider did not construct the documented validator.');
}
$validator->validateGraph([$definition]);
if ($container->get(FieldTypeDefinitionResolver::class) !== $container->get(FieldTypeRegistry::class)) {
    throw new RuntimeException('The canonical field-type resolver alias did not compose.');
}
echo "Container consumer verified: mandatory admission, canonical resolver and graph validation.\n";
