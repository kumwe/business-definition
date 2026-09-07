<?php

/** Discover the real PHPUnit suite for ownership checks, or execute it normally. @since 0.1.0 */

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (($argv[1] ?? null) !== '--list-json') {
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(dirname(__DIR__) . '/vendor/bin/phpunit'), $status);
    exit($status);
}
$inventory = [];
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/Unit'));
foreach ($files as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || !str_ends_with($file->getFilename(), 'Test.php')) {
        continue;
    }
    $relative = substr($file->getPathname(), strlen(__DIR__) + 1);
    $class = 'Kumwe\\BusinessDefinition\\Tests\\' . str_replace('/', '\\', substr($relative, 0, -4));
    $reflection = new ReflectionClass($class);
    if (!$reflection->isSubclassOf(PHPUnit\Framework\TestCase::class)) {
        throw new RuntimeException('Discovered file is not a PHPUnit case.');
    }
    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (str_starts_with($method->getName(), 'test') && $method->getDeclaringClass()->getName() === $class) {
            $inventory[$class . '::' . $method->getName()] = 'tests/' . $relative;
        }
    }
}
if ($inventory === []) {
    throw new RuntimeException('The actual PHPUnit suite is empty.');
}
ksort($inventory, SORT_STRING);
echo json_encode($inventory, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
