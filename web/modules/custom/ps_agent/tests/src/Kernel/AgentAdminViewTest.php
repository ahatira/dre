<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Tests the Agents admin view configuration.
 *
 * @group ps_agent
 */
class AgentAdminViewTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'ps',
    'ps_dictionary',
    'phone_international',
    'ps_agent',
  ];

  /**
   * Tests exposed admin filters.
   */
  public function testAdminViewExposesFullNameAndReferenceFilters(): void {
    $config = $this->config('views.view.agents_admin');
    $filters = $config->get('display.default.display_options.filters') ?? [];
    $fields = $config->get('display.default.display_options.fields') ?? [];

    $this->assertArrayHasKey('combine', $filters);
    $this->assertSame('combine', $filters['combine']['plugin_id']);
    $this->assertSame('Full Name', $filters['combine']['expose']['label']);
    $this->assertSame([
      'first_name' => 'first_name',
      'last_name' => 'last_name',
    ], $filters['combine']['fields']);

    $this->assertArrayHasKey('external_id', $filters);
    $this->assertTrue($filters['external_id']['exposed']);
    $this->assertSame('external_id', $filters['external_id']['field']);
    $this->assertSame('Reference', $filters['external_id']['expose']['label']);
    $this->assertSame('reference', $filters['external_id']['expose']['identifier']);

    $this->assertArrayHasKey('external_id', $fields);
    $this->assertSame('external_id', $fields['external_id']['field']);
    $this->assertSame('Reference', $fields['external_id']['label']);

    $this->assertArrayNotHasKey('first_name', $filters);
    $this->assertArrayNotHasKey('last_name', $filters);
  }

}
