<?php

/** Enforce the semantic package's closed source ownership and dependency boundary. @since 0.1.0 */

declare(strict_types=1);

$root = dirname(__DIR__);

/**
 * Inspect executable tokens; comments may document the explicitly retained host responsibilities.
 * @param string $source PHP source bytes.
 * @return list<string> Boundary violations.
 * @since 0.1.0
 */
function definitionBoundaryViolations(string $source): array
{
    $errors = [];
    $tokens = token_get_all($source);
    $forbiddenFunctions = ['class_alias', 'eval', 'exec', 'shell_exec', 'system', 'passthru', 'proc_open',
        'file_get_contents', 'file_put_contents', 'getenv', 'putenv', 'unserialize'];
    foreach ($tokens as $token) {
        if (!is_array($token)) {
            continue;
        }
        [$id, $text] = $token;
        if (in_array($id, [T_EVAL, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE], true)) {
            $errors[] = 'Runtime execution or loading is forbidden.';
        }
        if (!in_array($id, [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE], true)) {
            continue;
        }
        $name = strtolower(ltrim($text, '\\'));
        $parts = explode('\\', $name);
        $basename = $parts[count($parts) - 1];
        if (in_array($basename, $forbiddenFunctions, true)) {
            $errors[] = 'Forbidden execution/global operation: ' . $name;
        }
        if (
            in_array(
                $basename,
                ['expressionevaluator', 'decimalvalue', 'frozenevaluator', 'frozendecimal',
                'evaluate',
                'issatisfied'],
                true
            )
        ) {
            $errors[] = 'A runtime formula executor or arithmetic owner was reintroduced.';
        }
        if (str_starts_with($name, 'kumwe\\')) {
            $allowed = false;
            foreach (['kumwe\\businessdefinition\\', 'kumwe\\localization\\', 'kumwe\\sequence\\'] as $prefix) {
                $allowed = $allowed || str_starts_with($name . '\\', $prefix);
            }
            if (!$allowed || str_contains($name, '\\tests\\')) {
                $errors[] = 'Forbidden Kumwe dependency: ' . $name;
            }
        }
        foreach (['doctrine\\', 'twig\\', 'symfony\\', 'illuminate\\', 'ffi'] as $prefix) {
            if (str_starts_with($name, $prefix)) {
                $errors[] = 'Forbidden host/native dependency: ' . $name;
            }
        }
    }

    return array_values(array_unique($errors));
}

$errors = [];
$metadata = json_decode((string) file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
$manifest = json_decode(
    (string) file_get_contents($root . '/resources/public-api/v1.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
$ownership = json_decode(
    (string) file_get_contents($root . '/resources/native-ownership/v1.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
if (!is_array($metadata) || !is_array($manifest) || !is_array($ownership)) {
    throw new RuntimeException('Boundary metadata must be JSON objects.');
}
$symbols = $manifest['symbols'] ?? null;
$owners = $ownership['owners'] ?? null;
$expected = is_array($owners) ? ($owners['kumwe/business-definition'] ?? null) : null;
if (!is_array($symbols) || !is_array($expected) || array_keys($symbols) !== $expected) {
    $errors[] = 'The reviewed canonical ownership list differs from the public API.';
}
$require = $metadata['require'] ?? null;
if (
    !is_array($require) || $require !== [
    'php' => '^8.5', 'php-64bit' => '*', 'kumwe/localization' => '0.1.0',
    'kumwe/sequence' => 'dev-main', 'psr/container' => '^2.0', 'ramsey/uuid' => '^4.9',
    ]
) {
    $errors[] = 'The reviewed dependency ceiling or draft pin changed without boundary review.';
}
if (($metadata['autoload'] ?? null) !== ['psr-4' => ['Kumwe\\BusinessDefinition\\' => 'src/']]) {
    $errors[] = 'Production autoload must expose only the canonical package namespace.';
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
$count = 0;
foreach ($files as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    ++$count;
    $relative = substr($file->getPathname(), strlen($root) + 1);
    $name = 'Kumwe\\BusinessDefinition\\' . str_replace('/', '\\', substr($relative, 4, -4));
    if (!is_array($symbols) || !array_key_exists($name, $symbols)) {
        $errors[] = 'Unreviewed runtime source file: ' . $relative;
    }
    foreach (definitionBoundaryViolations((string) file_get_contents($file->getPathname())) as $error) {
        $errors[] = $relative . ': ' . $error;
    }
}
if ($count !== 41) {
    $errors[] = 'The reviewed 41-type source closure changed.';
}
$negative = [
    '<?php eval("return 1;");',
    '<?php class_alias(A::class, B::class);',
    '<?php \\class_alias(A::class, B::class);',
    '<?php new \\Kumwe\\App\\Site\\SiteContext();',
    '<?php new \\Kumwe\\Engine\\Runtime();',
    '<?php new \\Kumwe\\Extension\\Spi\\Metadata();',
    '<?php require "oracle.php";',
    '<?php $expression->evaluate([]);',
    '<?php new FrozenEvaluator();',
    '<?php new \\FFI();',
    '<?php new \\kUmWe\\App\\Site\\SiteContext();',
    '<?php new \\ffi();',
    '<?php $expression->Evaluate([]);',
    '<?php new \\Kumwe\\BusinessDefinition\\Domain\\DecimalValue();',
    '<?php new namespace\\ExpressionEvaluator();',
    '<?php namespace\\class_alias(A::class, B::class);',
];
foreach ($negative as $fixture) {
    if (definitionBoundaryViolations($fixture) === []) {
        $errors[] = 'A forbidden architecture fixture was accepted.';
    }
}
if ($errors !== []) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}
echo 'Architecture verified: 41 semantic types, closed dependencies, ', count($negative), " refusal fixtures.\n";
