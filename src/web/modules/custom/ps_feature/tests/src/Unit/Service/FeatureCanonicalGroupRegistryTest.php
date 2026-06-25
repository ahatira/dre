<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry
 */
#[CoversClass(FeatureCanonicalGroupRegistry::class)]
final class FeatureCanonicalGroupRegistryTest extends UnitTestCase {

  private FeatureCanonicalGroupRegistry $registry;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->registry = new FeatureCanonicalGroupRegistry();
  }

  /**
   * @covers ::resolveGroupId
   */
  #[DataProvider('legacyGroupIdProvider')]
  public function testResolveLegacyGroupId(string $legacy, string $canonical): void {
    self::assertSame($canonical, $this->registry->resolveGroupId($legacy));
  }

  /**
   * @return array<string, array{string, string}>
   */
  public static function legacyGroupIdProvider(): array {
    return [
      'amenagements' => ['amenagements', 'equipements'],
      'exterieurs' => ['exterieurs', 'equipements'],
      'hauteurs' => ['hauteurs', 'type_etat_du_batiment'],
      'structure' => ['structure_du_batiment', 'type_etat_du_batiment'],
      'vehicle access' => ['acces_vehicules', 'equipements'],
      'forbidden activity' => ['activite_non_autorisee', 'informations_complementaires'],
      'labels' => ['normes_certifications_et_labels', 'informations_complementaires'],
    ];
  }

  /**
   * @covers ::resolveGroupId
   */
  public function testResolveCanonicalGroupIdIsStable(): void {
    foreach ($this->registry->getCanonicalGroupIds() as $groupId) {
      self::assertSame($groupId, $this->registry->resolveGroupId($groupId));
    }
  }

  /**
   * @covers ::resolveCrmGroupCode
   */
  public function testResolveCrmGroupCode(): void {
    self::assertSame('equipements', $this->registry->resolveCrmGroupCode('AM_NAGEMENTS'));
    self::assertSame('equipements', $this->registry->resolveCrmGroupCode('EQUIPEMENTS'));
    self::assertSame('prestations_de_service', $this->registry->resolveCrmGroupCode('SERVICES'));
    self::assertSame('informations_complementaires', $this->registry->resolveCrmGroupCode(''));
  }

  /**
   * @covers ::isLegacyGroupId
   */
  public function testLegacyDetection(): void {
    self::assertTrue($this->registry->isLegacyGroupId('amenagements'));
    self::assertFalse($this->registry->isLegacyGroupId('equipements'));
  }

}
