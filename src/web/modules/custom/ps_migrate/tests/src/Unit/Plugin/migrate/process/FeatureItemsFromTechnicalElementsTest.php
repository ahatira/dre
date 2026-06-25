<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Plugin\migrate\process;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;
use Drupal\ps_migrate\Plugin\migrate\process\FeatureItemsFromTechnicalElements;
use Drupal\ps_migrate\Service\CanonicalCountryLanguageResolver;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\ps_migrate\Service\FeatureTechnicalElementParser;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
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
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('tec_hall_daccueil')
      ->willReturn(new class {
        public function getTypeDriver(): string {
          return 'numeric';
        }
      });

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
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('tec_no_group')
      ->willReturn(new class {
        public function getTypeDriver(): string {
          return 'text';
        }
      });

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $plugin = $this->createPlugin($entityTypeManager);

    $xml = new \SimpleXMLElement('<TECHNICAL_ELEMENT><CODE_GROUP></CODE_GROUP><CODE_ELEMENT>TEC_NO_GROUP</CODE_ELEMENT><VALUE>Example</VALUE></TECHNICAL_ELEMENT>');

    $result = $plugin->transform([$xml], $this->createMock(MigrateExecutableInterface::class), new Row([], []), 'field_features');

    self::assertCount(1, $result);
    self::assertSame('tec_no_group', $result[0]['feature_definition_id']);
  }

  private function createPlugin(EntityTypeManagerInterface $entityTypeManager): FeatureItemsFromTechnicalElements {
    return new FeatureItemsFromTechnicalElements(
      [],
      'feature_items_from_technical_elements',
      [],
      new FeatureTechnicalElementParser(),
      new FeatureImportResolver(
        new FeatureCanonicalGroupRegistry(),
        new FeatureMigrationKeyBuilder(),
        $entityTypeManager,
      ),
      new FeatureTechnicalElementValidator(),
      $entityTypeManager,
      $this->createMock(LanguageConfigFactoryOverride::class),
      $this->createMock(LanguageManagerInterface::class),
      new CanonicalCountryLanguageResolver(),
      $this->createMock(LoggerInterface::class),
    );
  }

}
