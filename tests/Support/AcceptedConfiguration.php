<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Support;

use Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission;

/** Test double isolating definition rules from the host-owned presentation profile. @since 0.1.0 */
final class AcceptedConfiguration implements FieldConfigurationAdmission
{
    /**
     * Accept fixtures explicitly already admitted by the test's composed host.
     * @param array<string, mixed> $configuration Selected fixture configuration.
     * @return void
     * @since 0.1.0
     */
    public function assertAdmissible(array $configuration): void
    {
    }
}
