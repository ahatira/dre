<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the feature import resolver.
 */
#[CoversClass(FeatureImportResolver::class)]
#[Group('ps_migrate')]
final class FeatureImportResolverTest extends UnitTestCase {

  /**
   * Ensures definition IDs are built from CODE_ELEMENT only.
   */
  public function testBuildDefinitionIdUsesFeatureCodeOnly(): void {
    $resolver = $this->createResolver();

    self::assertSame('tec_hall_daccueil', $resolver->buildDefinitionId('TEC_HALL_DACCUEIL'));
    self::assertSame('tec_climatisation', $resolver->buildDefinitionId('TEC_CLIMATISATION'));
  }

  /**
   * Ensures existing definitions take precedence for group resolution.
   */
  public function testResolveGroupIdUsesExistingDefinitionGroup(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getGroup')->willReturn('prestations_de_service');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('tec_hall_daccueil')->willReturn($definition);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $resolver = $this->createResolver($entityTypeManager);

    self::assertSame(
      'prestations_de_service',
      $resolver->resolveGroupId('TEC_HALL_DACCUEIL', 'AM_NAGEMENTS'),
    );
  }

  /**
   * Ensures CRM group codes map to canonical groups when no definition exists.
   */
  public function testResolveGroupIdUsesCrmGroupCode(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn(NULL);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $resolver = $this->createResolver($entityTypeManager);

    self::assertSame('equipements', $resolver->resolveGroupId('TEC_UNKNOWN', 'AM_NAGEMENTS'));
    self::assertSame('equipements', $resolver->resolveGroupId('TEC_UNKNOWN', 'EQUIPEMENTS'));
  }

  /**
   * Ensures empty CRM group codes fall back to informations_complementaires.
   */
  public function testResolveGroupIdFallsBackWhenGroupCodeMissing(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn(NULL);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $resolver = $this->createResolver($entityTypeManager);

    self::assertSame(
      'informations_complementaires',
      $resolver->resolveGroupId('TEC_UNKNOWN', ''),
    );
  }

  /**
   * Ensures loadDefinition returns the entity when present.
   */
  public function testLoadDefinition(): void {
    $definition = $this->createMock(FeatureDefinition::class);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('tec_hall_daccueil')->willReturn($definition);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $resolver = $this->createResolver($entityTypeManager);

    self::assertSame($definition, $resolver->loadDefinition('TEC_HALL_DACCUEIL'));
    self::assertNull($resolver->loadDefinition(''));
  }

  /**
   * Builds a resolver for tests.
   */
  private function createResolver(?EntityTypeManagerInterface $entityTypeManager = NULL): FeatureImportResolver {
    return new FeatureImportResolver(
      new FeatureCanonicalGroupRegistry(),
      new FeatureMigrationKeyBuilder(),
      $entityTypeManager ?? $this->createMock(EntityTypeManagerInterface::class),
    );
  }

}
