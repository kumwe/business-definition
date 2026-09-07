<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition;

use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\Container\FieldTypeRegistryFactory;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Container\BusinessDefinitionValidatorFactory;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionContributionRegistry;
use Kumwe\BusinessDefinition\Container\BusinessDefinitionContributionRegistryFactory;
use Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver;

/** Explicit runtime services; host admission must be registered separately. @since 0.1.0 */
final class ConfigProvider
{
    /**
     * Return deterministic Mezzio configuration without consulting runtime state.
     * @return array{dependencies: array{factories: array<class-string, class-string>,
     *     aliases: array<class-string, class-string>, shared: array<class-string, bool>}}
     * @since 0.1.0
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    FieldTypeRegistry::class => FieldTypeRegistryFactory::class,
                    BusinessDefinitionValidator::class => BusinessDefinitionValidatorFactory::class,
                    BusinessDefinitionContributionRegistry::class => BusinessDefinitionContributionRegistryFactory::class,
                ],
                'aliases' => [FieldTypeDefinitionResolver::class => FieldTypeRegistry::class],
                'shared' => [
                    FieldTypeRegistry::class => true,
                    BusinessDefinitionValidator::class => false,
                    BusinessDefinitionContributionRegistry::class => false,
                ],
            ],
        ];
    }
}
