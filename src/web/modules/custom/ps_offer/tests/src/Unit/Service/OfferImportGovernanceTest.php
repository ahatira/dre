<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_offer\Service\OfferImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_offer\Service\OfferImportGovernance
 */
#[CoversClass(OfferImportGovernance::class)]
#[Group('ps_offer')]
final class OfferImportGovernanceTest extends UnitTestCase {

  /**
   * @covers ::resolveEffectiveLockStrategy
   */
  public function testResolveEffectiveLockStrategyInheritsGlobalDefault(): void {
    $service = $this->buildService(
      ['crm_row_strategy_override' => OfferImportGovernance::STRATEGY_INHERIT],
      'skip_field',
    );

    self::assertSame('skip_field', $service->resolveEffectiveLockStrategy('node'));
  }

  /**
   * @covers ::resolveEffectiveLockStrategy
   */
  public function testResolveEffectiveLockStrategyUsesDomainOverride(): void {
    $service = $this->buildService(
      ['crm_row_strategy_override' => OfferImportGovernance::STRATEGY_SKIP_ROW],
      'skip_field',
    );

    self::assertSame('skip_row', $service->resolveEffectiveLockStrategy('node'));
  }

  /**
   * @covers ::shouldSkipProtectedRow
   */
  public function testShouldSkipProtectedRow(): void {
    $entity = $this->createMock(ContentEntityInterface::class);
    $entity->method('getEntityTypeId')->willReturn('node');

    $service = $this->buildService(
      ['crm_row_strategy_override' => OfferImportGovernance::STRATEGY_SKIP_ROW],
      'log_only',
    );

    self::assertTrue($service->shouldSkipProtectedRow($entity));
  }

  /**
   * @covers ::shouldUnpublishMissingOffer
   */
  public function testShouldUnpublishMissingOfferForNonProtectedOffer(): void {
    $lockField = new \stdClass();
    $lockField->value = FALSE;

    $offer = $this->createMock(NodeInterface::class);
    $offer->method('isPublished')->willReturn(TRUE);
    $offer->method('hasField')->with('field_internal_lock')->willReturn(TRUE);
    $offer->method('get')->with('field_internal_lock')->willReturn($lockField);

    $service = $this->buildService([
      'missing_from_xml' => [
        'offer_action' => OfferImportGovernance::ACTION_UNPUBLISH,
        'protected_offer_action' => OfferImportGovernance::ACTION_KEEP_PUBLISHED,
      ],
    ]);

    self::assertTrue($service->shouldUnpublishMissingOffer($offer, FALSE));
  }

  /**
   * Builds an offer import governance service with mocked config.
   *
   * @param array<string, mixed> $values
   *   Config values keyed by config name suffix.
   */
  private function buildService(array $values, string $globalStrategy = 'log_only'): OfferImportGovernance {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(static function (string $key) use ($values) {
      $parts = explode('.', $key);
      $cursor = $values;
      foreach ($parts as $part) {
        if (!is_array($cursor) || !array_key_exists($part, $cursor)) {
          return NULL;
        }
        $cursor = $cursor[$part];
      }
      return $cursor;
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with(OfferImportGovernance::CONFIG_NAME)->willReturn($config);

    $globalResolver = $this->createMock(ImportGovernanceGlobalResolver::class);
    $globalResolver->method('getGlobalLockStrategy')->willReturn($globalStrategy);

    return new OfferImportGovernance($configFactory, $globalResolver);
  }

}
