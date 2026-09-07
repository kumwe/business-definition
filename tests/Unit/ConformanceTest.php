<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Unit;

use Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\Domain\CanonicalDefinitionJson;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Kumwe\BusinessDefinition\Domain\Expression;
use Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition;
use Kumwe\BusinessDefinition\Tests\Oracle\FrozenEvaluator;
use Kumwe\BusinessDefinition\Tests\Support\AcceptedConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** Pins the semantic owner's language-neutral corpus, never a production PHP executor. @since 0.1.0 */
#[CoversClass(Expression::class)]
#[CoversClass(CanonicalDefinitionJson::class)]
#[CoversClass(EntityTypeDefinition::class)]
#[CoversClass(BusinessDefinitionValidator::class)]
#[CoversClass(BusinessDefinitionCompatibilityAnalyzer::class)]
final class ConformanceTest extends TestCase
{
    /** Replay exact typed values, eager failures, dependencies and canonical AST documents. @since 0.1.0 */
    public function testFormulaCorpusMatchesFrozenBaseline(): void
    {
        $corpus = json_decode(
            file_get_contents(__DIR__ . '/../../resources/corpus/formula-v1.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertCount(101, $corpus['vectors']);
        foreach ($corpus['vectors'] as $case) {
            $phase = 'parse';
            try {
                $expression = Expression::fromArray($case['expression']);
                self::assertSame($case['canonical'], $expression->toArray(), $case['id']);
                self::assertSame($case['dependencies'], $expression->dependencies(), $case['id']);
                self::assertSame($case['line_dependencies'], $expression->lineDependencies(), $case['id']);
                $phase = 'evaluate';
                $actual = ['value' => FrozenEvaluator::evaluate($expression, $case['fields'], $case['lines'])];
            } catch (InvalidBusinessDefinition $error) {
                $actual = ['refusal' => $phase === 'parse' ? 'invalid_ast' : 'evaluation_refused',
                    'phase' => $phase, 'message' => $error->getMessage()];
            }
            self::assertSame($case['expected'], $actual, $case['id']);
        }
    }

    /** Replay graph refusal order, canonical bytes/digests and ordered compatibility plans. @since 0.1.0 */
    public function testDefinitionAndCompatibilityCorpusMatchesFrozenBaseline(): void
    {
        $corpus = json_decode(
            file_get_contents(__DIR__ . '/../../resources/corpus/definition-v1.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $validator = new BusinessDefinitionValidator(new FieldTypeRegistry(), new AcceptedConfiguration());
        self::assertCount(18, $corpus['definitions']);
        foreach ($corpus['definitions'] as $case) {
            try {
                $definitions = array_map(EntityTypeDefinition::fromArray(...), $case['definitions']);
                $validator->validateGraph($definitions);
                $actual = ['definitions' => array_map(
                    static fn (EntityTypeDefinition $d): array => [
                        'canonical' => CanonicalDefinitionJson::encode($d->toArray()),
                    'checksum' => $d->checksum()],
                    $definitions
                )];
            } catch (InvalidBusinessDefinition $error) {
                $actual = ['refusal' => 'invalid_definition', 'message' => $error->getMessage()];
            }
            self::assertSame($case['expected'], $actual, $case['id']);
        }
        self::assertCount(6, $corpus['compatibility']);
        foreach ($corpus['compatibility'] as $case) {
            $before = $case['before'] === null ? null : EntityTypeDefinition::fromArray($case['before']);
            $after = EntityTypeDefinition::fromArray($case['after']);
            self::assertSame(
                $case['expected'],
                (new BusinessDefinitionCompatibilityAnalyzer())->analyze($before, $after)->toArray()
            );
        }
    }

    /** Digest changes require explicit semantic review and never silently regenerate during CI. @since 0.1.0 */
    public function testCorpusProvenanceDigestsRemainExact(): void
    {
        $root = dirname(__DIR__, 2);
        $provenance = json_decode(
            file_get_contents($root . '/resources/corpus/provenance-v1.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertSame('960ce8ec00cf724a7cae03e5ba09c4852c9ab54e', $provenance['app_commit']);
        foreach ($provenance['corpora'] as $path => $digest) {
            self::assertSame($digest, hash_file('sha256', $root . '/' . $path));
        }
        foreach ($provenance['oracle_files'] as $path => $digest) {
            self::assertSame($digest, hash_file('sha256', $root . '/' . $path));
        }
    }
}
