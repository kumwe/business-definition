<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Internal;

/**
 * Detach admitted arrays from caller-owned PHP references without changing value types or ordering.
 *
 * @internal
 * @since 0.1.1
 */
final class ValueSnapshot
{
    /**
     * Copy a tree whose structural and runtime-type bounds were already validated by its owner.
     *
     * Immutable domain objects remain shared; each array and scalar reference is copied by value.
     *
     * @template T
     * @param T $value Admitted value or collection.
     * @return T Independent value with the same keys, order and immutable objects.
     * @since 0.1.1
     */
    public static function copy(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }
        /** @var T&array<array-key, mixed> $copy Shape is unchanged by value-only recursion. */
        $copy = array_map(self::copy(...), $value);
        return $copy;
    }

    /** Prevent construction of the internal stateless helper. @since 0.1.1 */
    private function __construct()
    {
    }
}
