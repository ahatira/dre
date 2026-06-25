<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Base class for ps_migrate kernel tests.
 */
abstract class PsMigrateKernelTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'options',
    'file',
    'migrate',
    'migrate_plus',
    'migrate_tools',
    'ps_core',
    'ps_migrate',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('import_run');
    $this->installConfig(['ps_migrate']);
  }

}
