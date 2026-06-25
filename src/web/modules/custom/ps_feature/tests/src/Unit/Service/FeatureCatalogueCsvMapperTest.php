<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\ps_feature\Service\FeatureCatalogueCsvMapper;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureCatalogueCsvMapper
 */
#[CoversClass(FeatureCatalogueCsvMapper::class)]
#[Group('ps_feature')]
final class FeatureCatalogueCsvMapperTest extends UnitTestCase {

  private FeatureCatalogueCsvMapper $mapper;

  protected function setUp(): void {
    parent::setUp();
    $this->mapper = new FeatureCatalogueCsvMapper();
  }

  public function testResolveCategory(): void {
    self::assertSame('equipements', $this->mapper->resolveCategory('Équipements'));
    self::assertSame('prestations_de_service', $this->mapper->resolveCategory('Services'));
    self::assertSame('type_etat_du_batiment', $this->mapper->resolveCategory('État du bâtiment'));
    self::assertSame('informations_complementaires', $this->mapper->resolveCategory('Informations complémentaires'));
    self::assertNull($this->mapper->resolveCategory('Unknown'));
  }

  public function testResolveTypeDriver(): void {
    self::assertSame('flag', $this->mapper->resolveTypeDriver('Indicateur'));
    self::assertSame('yes_no', $this->mapper->resolveTypeDriver('Oui/Non'));
    self::assertSame('numeric', $this->mapper->resolveTypeDriver('Nombre'));
    self::assertSame('text', $this->mapper->resolveTypeDriver('Texte'));
    self::assertSame('date', $this->mapper->resolveTypeDriver('Date'));
    self::assertNull($this->mapper->resolveTypeDriver('Liste'));
  }

  public function testNormalizeDefinitionId(): void {
    self::assertSame('tec_hall_daccueil', $this->mapper->normalizeDefinitionId('TEC_HALL_DACCUEIL'));
  }

  public function testResolveExposeAsFilter(): void {
    self::assertTrue($this->mapper->resolveExposeAsFilter('Oui'));
    self::assertFalse($this->mapper->resolveExposeAsFilter('Non'));
  }

}
