<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\Utility;

use Drupal\Core\Theme\Icon\IconDefinitionInterface;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_core\Utility\IconIdUtility
 * @group ps_core
 */
final class IconIdUtilityTest extends UnitTestCase {

  /**
   * @covers ::normalizeStoredIcon
   */
  public function testNormalizeStoredIcon(): void {
    $this->assertSame('bnp_custom:infos', IconIdUtility::normalizeStoredIcon('bnp_custom:infos', 'bnp_custom:default'));
    $this->assertSame('bnp_custom:default', IconIdUtility::normalizeStoredIcon('', 'bnp_custom:default'));
    $this->assertSame('bnp_custom:default', IconIdUtility::normalizeStoredIcon(NULL, 'bnp_custom:default'));
  }

  /**
   * @covers ::extractFromSubmission
   */
  public function testExtractFromSubmission(): void {
    $this->assertSame('bnp_custom:equipement', IconIdUtility::extractFromSubmission('bnp_custom:equipement'));
    $this->assertSame('bnp_custom:equipement', IconIdUtility::extractFromSubmission([
      'target_id' => 'bnp_custom:equipement',
    ]));
    $this->assertSame('bnp_custom:fallback', IconIdUtility::extractFromSubmission([], 'bnp_custom:fallback'));
  }

  /**
   * @covers ::extractFromSubmission
   */
  public function testExtractFromSubmissionWithIconObject(): void {
    $icon = $this->createMock(IconDefinitionInterface::class);
    $icon->method('getId')->willReturn('bnp_custom:medal');

    $this->assertSame('bnp_custom:medal', IconIdUtility::extractFromSubmission(['icon' => $icon]));
  }

}
