<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_suite_bnp\Kernel;

use Drupal\Tests\sdc_devel\Kernel\SdcDevelComponentKernelTestBase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Validate components.
 */
#[CoversNothing]
#[RunTestsInSeparateProcesses]
#[Group('ui_suite_bnp')]
class ComponentValidatorTest extends SdcDevelComponentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'ui_patterns',
    'ui_styles',
    'ui_styles_entity_status',
    'ui_styles_page',
  ];

  /**
   * {@inheritdoc}
   */
  protected static $themes = [
    'ui_suite_bnp',
  ];

}
