<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Unit\Application;

use Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer;
use Kumwe\BusinessDefinition\Domain\CompatibilityClassification;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Kumwe\BusinessDefinition\Tests\Unit\Domain\EntityTypeDefinitionTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BusinessDefinitionCompatibilityAnalyzer::class)]
final class BusinessDefinitionCompatibilityAnalyzerTest extends TestCase
{
    public function testEntityTranslationChangesAreClassifiedAndCanonicalReorderingIsUnchanged(): void
    {
        $initial = EntityTypeDefinitionTest::document();
        $translated = $initial;
        $translated['label_translations'] = ['singular_label' => ['fr' => 'Actif', 'de' => 'Anlage']];
        foreach ([[$initial, $translated], [$translated, $initial]] as [$old, $new]) {
            $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze(
                EntityTypeDefinition::fromArray($old)->published(1),
                EntityTypeDefinition::fromArray($new),
            );
            self::assertTrue($plan->requiresConfirmation());
            self::assertCount(1, $plan->changes());
            self::assertSame('/definition/behavior', $plan->changes()[0]->path);
            self::assertSame(CompatibilityClassification::BehaviorChanging, $plan->changes()[0]->classification);
        }
        $reordered = $translated;
        $reordered['label_translations']['singular_label'] = ['de' => 'Anlage', 'fr' => 'Actif'];
        $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze(
            EntityTypeDefinition::fromArray($translated)->published(1),
            EntityTypeDefinition::fromArray($reordered),
        );
        self::assertSame([], $plan->changes());
        self::assertFalse($plan->requiresConfirmation());
    }

    public function testFirstPublicationIsAdditiveAndDeterministic(): void
    {
        $draft = EntityTypeDefinition::fromArray(EntityTypeDefinitionTest::document());
        $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze(null, $draft);

        self::assertSame(1, $plan->toVersion);
        self::assertFalse($plan->requiresConfirmation());
        self::assertSame(CompatibilityClassification::Additive, $plan->changes()[0]->classification);
        self::assertSame($draft->published(1)->checksum(), $plan->toChecksum);
    }

    public function testRequiredFieldWithoutDefaultRequiresDataMigration(): void
    {
        $document = EntityTypeDefinitionTest::document();
        $before = EntityTypeDefinition::fromArray($document)->published(1);
        $document['fields'][] = [
            'handle' => 'code',
            'label' => 'Code',
            'type' => 'core.text',
            'required' => true,
            'nullable' => false,
            'length' => 80,
        ];
        $draft = EntityTypeDefinition::fromArray($document);
        $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze($before, $draft);

        self::assertTrue($plan->requiresConfirmation());
        self::assertContains(
            CompatibilityClassification::DataMigrationRequired,
            array_map(static fn ($change) => $change->classification, $plan->changes()),
        );
    }

    /**
     * Proves changing the portal operation allowlist requires an explicit behavior-change review.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function testPortalOperationChangesAreExplicitBehaviorChanges(): void
    {
        $document = EntityTypeDefinitionTest::document();
        $document['portal_exposure'] = true;
        $before = EntityTypeDefinition::fromArray($document)->published(1);
        $document['portal_operations'] = ['read', 'browse'];
        $draft = EntityTypeDefinition::fromArray($document);

        $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze($before, $draft);
        $change = array_values(array_filter(
            $plan->changes(),
            static fn ($candidate): bool => $candidate->path === '/exposure/portal_operations',
        ));

        self::assertCount(1, $change);
        self::assertSame(CompatibilityClassification::BehaviorChanging, $change[0]->classification);
        self::assertTrue($plan->requiresConfirmation());
    }
}
