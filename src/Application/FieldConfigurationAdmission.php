<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Application;

use InvalidArgumentException;

/**
 * Host-supplied admission of field configuration against the selected presentation contract.
 *
 * Definition rules and field-type configuration-key checks remain in BusinessDefinitionValidator.
 * The host must supply its actual presentation-profile validator; there is no default implementation.
 * Implementations must neither mutate input nor grant authorization or select a trusted generation.
 *
 * @since 0.1.0
 */
interface FieldConfigurationAdmission
{
    /**
     * Refuse configuration the composed presentation contract cannot safely transport.
     *
     * @param array<string, mixed> $configuration Immutable field configuration to inspect.
     * @return void
     * @throws InvalidArgumentException When the selected contract refuses this configuration.
     * @since 0.1.0
     */
    public function assertAdmissible(array $configuration): void;
}
