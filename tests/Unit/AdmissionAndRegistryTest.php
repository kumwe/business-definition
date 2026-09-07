<?php

declare(strict_types=1);

namespace Kumwe\BusinessDefinition\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionCompatibilityAnalyzer;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionContributionRegistry;
use Kumwe\BusinessDefinition\Application\BusinessDefinitionValidator;
use Kumwe\BusinessDefinition\Application\DefinitionCatalogEntry;
use Kumwe\BusinessDefinition\Application\DefinitionDraft;
use Kumwe\BusinessDefinition\Application\DefinitionVersionRecord;
use Kumwe\BusinessDefinition\Application\FieldConfigurationAdmission;
use Kumwe\BusinessDefinition\Application\FieldTypeDefinitionResolver;
use Kumwe\BusinessDefinition\Application\FieldTypeRegistry;
use Kumwe\BusinessDefinition\ConfigProvider;
use Kumwe\BusinessDefinition\Container\BusinessDefinitionContributionRegistryFactory;
use Kumwe\BusinessDefinition\Container\BusinessDefinitionValidatorFactory;
use Kumwe\BusinessDefinition\Container\FieldTypeRegistryFactory;
use Kumwe\BusinessDefinition\Domain\DefinitionOwner;
use Kumwe\BusinessDefinition\Domain\DefinitionStatus;
use Kumwe\BusinessDefinition\Domain\EntityTypeDefinition;
use Kumwe\BusinessDefinition\Domain\FieldTypeDefinition;
use Kumwe\BusinessDefinition\Domain\InvalidBusinessDefinition;
use Kumwe\BusinessDefinition\Tests\Support\AcceptedConfiguration;
use Kumwe\BusinessDefinition\Tests\Unit\Domain\EntityTypeDefinitionTest;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

/** Proves explicit host composition, registry ownership and immutable record consistency. @since 0.1.0 */
#[CoversClass(BusinessDefinitionValidator::class)]
#[CoversClass(BusinessDefinitionContributionRegistry::class)]
#[CoversClass(FieldTypeRegistry::class)]
#[CoversClass(FieldTypeDefinition::class)]
#[CoversClass(DefinitionDraft::class)]
#[CoversClass(DefinitionVersionRecord::class)]
#[CoversClass(DefinitionCatalogEntry::class)]
#[CoversClass(DefinitionOwner::class)]
#[CoversClass(ConfigProvider::class)]
#[CoversClass(BusinessDefinitionValidatorFactory::class)]
#[CoversClass(BusinessDefinitionContributionRegistryFactory::class)]
#[CoversClass(FieldTypeRegistryFactory::class)]
final class AdmissionAndRegistryTest extends TestCase
{
    /** Mandatory admission sees every field and its refusal survives as the definition cause. @since 0.1.0 */
    public function testAdmissionIsMandatoryInvokedAndFailClosed(): void
    {
        $definition = EntityTypeDefinition::fromArray(EntityTypeDefinitionTest::document());
        $admission = new class implements FieldConfigurationAdmission {
            public int $calls = 0;
            public ?InvalidArgumentException $failure = null;
            public function assertAdmissible(array $configuration): void
            {
                ++$this->calls;
                if ($this->failure !== null) {
                    throw $this->failure;
                }
            }
        };
        $validator = new BusinessDefinitionValidator(new FieldTypeRegistry(), $admission);
        $validator->validateGraph([$definition]);
        self::assertSame(count($definition->fields()), $admission->calls);
        $admission->failure = new InvalidArgumentException('Selected presentation profile refuses the field.');
        try {
            $validator->validateGraph([$definition]);
            self::fail('A refused presentation contract reached semantic validation.');
        } catch (InvalidBusinessDefinition $error) {
            self::assertSame($admission->failure, $error->getPrevious());
            self::assertStringContainsString(
                'Business field id has non-portable presentation configuration',
                $error->getMessage()
            );
        }
        self::assertSame(
            2,
            (new \ReflectionMethod(BusinessDefinitionValidator::class, '__construct'))->getNumberOfRequiredParameters()
        );
    }

    /**
     * Real Laminas construction requires host admission and keeps mutable definition registries local.
     * @since 0.1.0
     */
    public function testRealContainerHasExplicitPortsAndServiceLifetimes(): void
    {
        $configuration = (new ConfigProvider())()['dependencies'];
        self::assertSame($configuration, (new ConfigProvider())()['dependencies']);
        $container = new ServiceManager($configuration);
        try {
            $container->get(BusinessDefinitionValidator::class);
            self::fail('Missing admission must fail composition.');
        } catch (NotFoundExceptionInterface) {
            self::assertTrue(true);
        }
        $container = new ServiceManager($configuration + [
            'services' => [FieldConfigurationAdmission::class => new AcceptedConfiguration()],
        ]);
        self::assertSame(
            $container->get(FieldTypeRegistry::class),
            $container->get(FieldTypeDefinitionResolver::class)
        );
        self::assertNotSame(
            $container->get(BusinessDefinitionValidator::class),
            $container->get(BusinessDefinitionValidator::class)
        );
        self::assertNotSame(
            $container->get(BusinessDefinitionContributionRegistry::class),
            $container->get(BusinessDefinitionContributionRegistry::class)
        );
        $other = new ServiceManager($configuration + [
            'services' => [FieldConfigurationAdmission::class => new AcceptedConfiguration()],
        ]);
        self::assertNotSame($container->get(FieldTypeRegistry::class), $other->get(FieldTypeRegistry::class));
        $this->expectException(InvalidArgumentException::class);
        (new BusinessDefinitionValidatorFactory())(new ServiceManager([
            'services' => [FieldTypeDefinitionResolver::class => new \stdClass()],
        ]));
    }

    /** Original site and long package-owner grammar remains distinct from ContributionOwner. @since 0.1.0 */
    public function testDefinitionOwnershipPreservesPersistedGrammarAndRefusals(): void
    {
        $site = DefinitionOwner::site(' DEFAULT ');
        self::assertSame('site.default', $site->namespace());
        self::assertSame(['type' => 'site', 'identifier' => 'default'], $site->toArray());
        $site->assertOwns('site.default.asset');
        $package = DefinitionOwner::extension(str_repeat('a', 64) . '/package');
        self::assertSame(str_repeat('a', 64) . '.package', $package->namespace());
        self::assertSame('core', DefinitionOwner::core()->namespace());
        foreach (['other.asset', 'site.defaultish.asset'] as $handle) {
            try {
                $site->assertOwns($handle);
                self::fail('An owner admitted a foreign namespace.');
            } catch (InvalidBusinessDefinition) {
                self::assertTrue(true);
            }
        }
    }

    /**
     * Registries validate ownership and duplicate refusal without publication or persistence authority.
     * @since 0.1.0
     */
    public function testRegistriesKeepOwnerBoundariesAndExplicitRemoval(): void
    {
        $definition = EntityTypeDefinition::fromArray(EntityTypeDefinitionTest::document());
        $registry = new BusinessDefinitionContributionRegistry(new BusinessDefinitionValidator(
            new FieldTypeRegistry(),
            new AcceptedConfiguration()
        ));
        $registry->register($definition->owner, $definition);
        $registry->validate();
        self::assertSame([$definition], $registry->all());
        self::assertSame([$definition], $registry->ownedBy($definition->owner));
        try {
            $registry->register($definition->owner, $definition);
            self::fail('Duplicate definitions were admitted.');
        } catch (InvalidBusinessDefinition) {
            self::assertTrue(true);
        }
        $registry->remove($definition->owner);
        self::assertSame([], $registry->all());
        $types = new FieldTypeRegistry();
        self::assertTrue($types->has('core.text'));
        self::assertSame(
            $types->get('core.text'),
            $types->ownedBy(DefinitionOwner::core())[array_search(
                $types->get('core.text'),
                $types->ownedBy(DefinitionOwner::core()),
                true
            )]
        );
        $types->remove(DefinitionOwner::core());
        self::assertFalse($types->has('core.text'));
        $this->expectException(InvalidBusinessDefinition::class);
        $types->get('core.text');
    }

    /**
     * Snapshot DTOs preserve supplied state; draft/version objects refuse inconsistent canonical evidence.
     * @since 0.1.0
     */
    public function testDefinitionRecordsPreserveSnapshotsAndRefuseInconsistentEvidence(): void
    {
        $definition = EntityTypeDefinition::fromArray(EntityTypeDefinitionTest::document());
        $at = new DateTimeImmutable('2026-09-07T00:00:00+00:00');
        $draft = new DefinitionDraft($definition, 1, $definition->checksum(), 'actor', $at);
        self::assertSame($definition, $draft->definition);
        $plan = (new BusinessDefinitionCompatibilityAnalyzer())->analyze(null, $definition);
        $version = new DefinitionVersionRecord(
            $definition->published(1),
            $plan,
            DefinitionStatus::Published,
            'actor',
            $at
        );
        self::assertSame($plan, $version->compatibility);
        $entry = new DefinitionCatalogEntry(
            'opaque',
            'site',
            'opaque',
            $definition->owner,
            false,
            -1,
            null,
            DefinitionStatus::Draft,
            $at
        );
        self::assertSame(-1, $entry->draftRevision, 'Passive snapshots intentionally do not invent new validation.');
        self::assertSame($at, $entry->updatedAt);
        foreach ([0, -1] as $revision) {
            try {
                new DefinitionDraft($definition, $revision, $definition->checksum(), 'actor', $at);
                self::fail('Invalid draft revision was accepted.');
            } catch (InvalidBusinessDefinition) {
                self::assertTrue(true);
            }
        }
        $this->expectException(InvalidBusinessDefinition::class);
        new DefinitionVersionRecord($definition, $plan, DefinitionStatus::Draft, 'actor', $at);
    }
    /** Custom type round trips preserve vocabulary, and invalid storage/configuration is refused. @since 0.1.0 */
    public function testFieldTypeRoundTripsAndRefusesIncompatibleDeclarations(): void
    {
        $type = new FieldTypeDefinition(
            'vendor.package.code',
            'Code',
            'Short exact code',
            'string',
            'string',
            ['mask'],
        );
        self::assertSame($type->toArray(), FieldTypeDefinition::fromArray($type->toArray())->toArray());
        $registry = new FieldTypeRegistry(false);
        $owner = DefinitionOwner::extension('vendor/package');
        $registry->register($owner, $type);
        self::assertSame([$type], $registry->ownedBy($owner));
        foreach (
            [
            ['value_type' => 'integer'],
            ['configuration_keys' => ['mask', 'mask']],
            ['configuration_keys' => ['Bad']],
            ['unknown' => true],
            ] as $change
        ) {
            try {
                FieldTypeDefinition::fromArray(array_replace($type->toArray(), $change));
                self::fail('An incompatible field-type declaration was accepted.');
            } catch (InvalidBusinessDefinition) {
                self::assertTrue(true);
            }
        }
        $this->expectException(InvalidBusinessDefinition::class);
        $registry->register(DefinitionOwner::core(), $type);
    }
}
