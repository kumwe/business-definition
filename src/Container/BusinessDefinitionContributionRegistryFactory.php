<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Container;

use Kumwe\BusinessDefinition\Application\BusinessDefinitionContributionRegistry;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Psr\Container\ContainerInterface;
use InvalidArgumentException;

/** Explicit factory; no host authority or process-global state is installed. @since 0.1.0 */
final class BusinessDefinitionContributionRegistryFactory
{
    /**
     * Resolve the documented collaborators and construct an operation-local service.
     * @param ContainerInterface $container Host composition container.
     * @return BusinessDefinitionContributionRegistry
     * @throws InvalidArgumentException When a supplied service has the wrong contract.
     * @since 0.1.0
     */
    public function __invoke(ContainerInterface $container): BusinessDefinitionContributionRegistry
    {
        $businessDefinitionValidator = $container->get(BusinessDefinitionValidator::class);
        if (!$businessDefinitionValidator instanceof BusinessDefinitionValidator) {
            throw new InvalidArgumentException('The BusinessDefinitionValidator service is incompatible.');
        }

        return new BusinessDefinitionContributionRegistry($businessDefinitionValidator);
    }
}
