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
      'amenagements' => ['amenagements', 'equipment'],
      'exterieurs' => ['exterieurs', 'equipment'],
      'hauteurs' => ['hauteurs', 'building'],
      'structure' => ['structure_du_batiment', 'building'],
      'vehicle access' => ['acces_vehicules', 'transport'],
      'forbidden activity' => ['activite_non_autorisee', 'additional'],
      'labels' => ['normes_certifications_et_labels', 'additional'],
      'old canonical equipment' => ['equipements', 'equipment'],
      'old canonical services' => ['prestations_de_service', 'services'],
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
    self::assertSame('equipment', $this->registry->resolveCrmGroupCode('AM_NAGEMENTS'));
    self::assertSame('equipment', $this->registry->resolveCrmGroupCode('EQUIPEMENTS'));
    self::assertSame('services', $this->registry->resolveCrmGroupCode('SERVICES'));
    self::assertSame('additional', $this->registry->resolveCrmGroupCode(''));
  }

  /**
   * @covers ::isLegacyGroupId
   */
  public function testLegacyDetection(): void {
    self::assertTrue($this->registry->isLegacyGroupId('amenagements'));
    self::assertTrue($this->registry->isLegacyGroupId('equipements'));
    self::assertFalse($this->registry->isLegacyGroupId('equipment'));
  }

}
