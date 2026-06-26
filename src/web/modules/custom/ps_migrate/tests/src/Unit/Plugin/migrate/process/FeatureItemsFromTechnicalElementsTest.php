<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Plugin\migrate\process;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\ps_core\Service\ImportGovernancePolicyManager;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;
use Drupal\ps_migrate\Plugin\migrate\process\FeatureItemsFromTechnicalElements;
use Drupal\ps_migrate\Service\CanonicalCountryLanguageResolver;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\ps_migrate\Service\FeatureOfferValueImportHandler;
use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer;
use Drupal\ps_migrate\Service\FeatureTechnicalElementParser;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Drupal\Tests\ps_migrate\Unit\Support\TestCatalogueImportPolicyStub;
use Drupal\Tests\ps_migrate\Unit\Support\TestFeatureCatalogueImportPolicy;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * Tests the technical element to feature item process plugin.
 */
#[CoversClass(FeatureItemsFromTechnicalElements::class)]
#[Group('ps_migrate')]
final class FeatureItemsFromTechnicalElementsTest extends UnitTestCase {

  /**
   * Ensures technical elements become feature field items.
   */
  public function testTransformBuildsFeatureFieldItems(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getTypeDriver')->willReturn('numeric');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('tec_hall_daccueil')
      ->willReturn($definition);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $plugin = $this->createPlugin($entityTypeManager);

    $xml = new \SimpleXMLElement('<TECHNICAL_ELEMENT><CODE_GROUP>AM_NAGEMENTS</CODE_GROUP><CODE_ELEMENT>TEC_HALL_DACCUEIL</CODE_ELEMENT><VALUE>532.00</VALUE><UNIT>M2</UNIT><ML_COMPLEMENT>Lobby principal</ML_COMPLEMENT></TECHNICAL_ELEMENT>');

    $result = $plugin->transform([$xml], $this->createMock(MigrateExecutableInterface::class), new Row([], []), 'field_features');

    self::assertCount(1, $result);
    self::assertSame('tec_hall_daccueil', $result[0]['feature_definition_id']);
    self::assertSame(532.0, json_decode($result[0]['payload'], TRUE)['value']);
    self::assertSame('M2', json_decode($result[0]['payload'], TRUE)['unit']);
    self::assertSame('Lobby principal', json_decode($result[0]['payload'], TRUE)['complement']);
  }

  /**
   * Ensures missing definitions are skipped.
   */
  public function testTransformSkipsMissingDefinitions(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->willReturn(NULL);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $plugin = $this->createPlugin($entityTypeManager);

    $xml = new \SimpleXMLElement('<TECHNICAL_ELEMENT><CODE_GROUP>AM_NAGEMENTS</CODE_GROUP><CODE_ELEMENT>TEC_UNKNOWN</CODE_ELEMENT><VALUE>Example</VALUE></TECHNICAL_ELEMENT>');

    $result = $plugin->transform([$xml], $this->createMock(MigrateExecutableInterface::class), new Row([], []), 'field_features');

    self::assertSame([], $result);
  }

  /**
   * Ensures elements without CODE_GROUP still resolve when definition exists.
   */
  public function testTransformAcceptsMissingGroupCode(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getTypeDriver')->willReturn('text');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('tec_no_group')
      ->willReturn($definition);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $plugin = $this->createPlugin($entityTypeManager);

    $xml = new \SimpleXMLElement('<TECHNICAL_ELEMENT><CODE_GROUP></CODE_GROUP><CODE_ELEMENT>TEC_NO_GROUP</CODE_ELEMENT><VALUE>Example</VALUE></TECHNICAL_ELEMENT>');

    $result = $plugin->transform([$xml], $this->createMock(MigrateExecutableInterface::class), new Row([], []), 'field_features');

    self::assertCount(1, $result);
    self::assertSame('tec_no_group', $result[0]['feature_definition_id']);
  }

  private function createPlugin(EntityTypeManagerInterface $entityTypeManager): FeatureItemsFromTechnicalElements {
    $cataloguePolicy = new TestCatalogueImportPolicyStub();
    $policy = new TestFeatureCatalogueImportPolicy($cataloguePolicy);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->with('features')->willReturn($policy);

    $registry = new ImportGovernanceRegistry($policyManager);

    $importResolver = new FeatureImportResolver(
      new FeatureCanonicalGroupRegistry(),
      new FeatureMigrationKeyBuilder(),
      $entityTypeManager,
      $registry,
    );

    $offerValueImportHandler = new FeatureOfferValueImportHandler(
      $importResolver,
      $entityTypeManager,
      $registry,
      new FeaturePayloadDefaultsNormalizer(),
      $this->createMock(LanguageConfigFactoryOverride::class),
      $this->createMock(LanguageManagerInterface::class),
      $this->createMock(LoggerInterface::class),
    );

    return new FeatureItemsFromTechnicalElements(
      [],
      'feature_items_from_technical_elements',
      [],
      new FeatureTechnicalElementParser(),
      $importResolver,
      new FeatureTechnicalElementValidator(),
      $offerValueImportHandler,
      new CanonicalCountryLanguageResolver(),
      $this->createMock(LoggerInterface::class),
    );
  }

}
