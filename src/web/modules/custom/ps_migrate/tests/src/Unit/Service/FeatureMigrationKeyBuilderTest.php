<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the feature migration key builder.
 */
#[CoversClass(FeatureMigrationKeyBuilder::class)]
#[Group('ps_migrate')]
final class FeatureMigrationKeyBuilderTest extends UnitTestCase {

  /**
   * Ensures keys are normalized deterministically.
   */
  public function testBuildIdsAreNormalized(): void {
    $builder = new FeatureMigrationKeyBuilder();

    self::assertSame('am_nagements', $builder->buildGroupId('AM_NAGEMENTS'));
    self::assertSame('tec_hall_daccueil', $builder->buildDefinitionId('AM_NAGEMENTS', 'TEC_HALL_DACCUEIL'));
    self::assertSame('tec_hall_daccueil', $builder->buildDefinitionId('', 'TEC_HALL_DACCUEIL'));
    self::assertSame('tec_hall_daccueil', $builder->normalize('TEC_HALL_DACCUEIL'));
    self::assertSame('', $builder->normalize('   '));
  }

}