<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Container;

use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver;
use Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission;
use Psr\Container\ContainerInterface;
use InvalidArgumentException;

/** Explicit factory; no host authority or process-global state is installed. @since 0.1.0 */
final class BusinessDefinitionValidatorFactory
{
    /**
     * Resolve the documented collaborators and construct an operation-local service.
     * @param ContainerInterface $container Host composition container.
     * @return BusinessDefinitionValidator
     * @throws InvalidArgumentException When a supplied service has the wrong contract.
     * @since 0.1.0
     */
    public function __invoke(ContainerInterface $container): BusinessDefinitionValidator
    {
        $fieldTypeDefinitionResolver = $container->get(FieldTypeDefinitionResolver::class);
        if (!$fieldTypeDefinitionResolver instanceof FieldTypeDefinitionResolver) {
            throw new InvalidArgumentException('The FieldTypeDefinitionResolver service is incompatible.');
        }
        $fieldConfigurationAdmission = $container->get(FieldConfigurationAdmission::class);
        if (!$fieldConfigurationAdmission instanceof FieldConfigurationAdmission) {
            throw new InvalidArgumentException('The FieldConfigurationAdmission service is incompatible.');
        }

        return new BusinessDefinitionValidator($fieldTypeDefinitionResolver, $fieldConfigurationAdmission);
    }
}
