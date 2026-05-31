<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests structured validation of feature technical elements.
 */
#[CoversClass(FeatureTechnicalElementValidator::class)]
#[Group('ps_migrate')]
final class FeatureTechnicalElementValidatorTest extends UnitTestCase {

  /**
   * Ensures valid records return no errors.
   */
  public function testValidateValidRecord(): void {
    $validator = new FeatureTechnicalElementValidator();

    $result = $validator->validate([
      'group_code' => 'AM_NAGEMENTS',
      'feature_code' => 'TEC_HALL_DACCUEIL',
      'definition_id' => 'am_nagements__tec_hall_daccueil',
      'type_driver' => 'numeric',
      'payload' => [
        'value' => '532.00',
        'unit' => 'M2',
      ],
    ]);

    self::assertSame([], $result['errors']);
  }

  /**
   * Ensures missing mandatory fields are reported as errors.
   */
  public function testValidateMissingMandatoryFields(): void {
    $validator = new FeatureTechnicalElementValidator();

    $result = $validator->validate([
      'group_code' => '',
      'feature_code' => '',
      'definition_id' => str_repeat('a', 129),
      'type_driver' => '',
      'payload' => [],
    ]);

    self::assertContains('missing_group_code', array_column($result['errors'], 'code'));
    self::assertContains('missing_feature_code', array_column($result['errors'], 'code'));
    self::assertContains('definition_id_too_long', array_column($result['errors'], 'code'));
    self::assertContains('missing_type_driver', array_column($result['errors'], 'code'));
  }

  /**
   * Ensures soft anomalies are reported as warnings.
   */
  public function testValidateWarnings(): void {
    $validator = new FeatureTechnicalElementValidator();

    $result = $validator->validate([
      'group_code' => 'Group Space',
      'feature_code' => 'feature-lower',
      'definition_id' => 'group_space__feature_lower',
      'type_driver' => 'flag',
      'payload' => [
        'value' => 'yes',
        'unit' => str_repeat('x', 33),
      ],
    ]);

    self::assertContains('group_code_non_canonical', array_column($result['warnings'], 'code'));
    self::assertContains('feature_code_non_canonical', array_column($result['warnings'], 'code'));
    self::assertContains('flag_has_scalar_value', array_column($result['warnings'], 'code'));
    self::assertContains('unit_too_long', array_column($result['warnings'], 'code'));
  }

}