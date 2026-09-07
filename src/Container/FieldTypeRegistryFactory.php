<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Container;

use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Psr\Container\ContainerInterface;

/** Explicit factory; no host authority or process-global state is installed. @since 0.1.0 */
final class FieldTypeRegistryFactory
{
    /**
     * Resolve the documented collaborators and construct an operation-local service.
     * @param ContainerInterface $container Host composition container.
     * @return FieldTypeRegistry
     * @since 0.1.0
     */
    public function __invoke(ContainerInterface $container): FieldTypeRegistry
    {

        return new FieldTypeRegistry();
    }
}
