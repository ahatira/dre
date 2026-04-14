<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\ps_agent\Entity\Agent;

/**
 * Tests for Agent entity.
 *
 * @group ps_agent
 */
class AgentEntityTest extends EntityKernelTestBase {

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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create default agent type.
    $this->container->get('entity_type.manager')
      ->getStorage('agent_type')
      ->create(['id' => 'default', 'label' => 'Default'])
      ->save();
  }

  /**
   * Tests agent entity creation.
   */
  public function testCreateAgent(): void {
    /** @var \Drupal\ps_agent\Entity\Agent $agent */
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
      'external_id' => 'CRM-123',
    ]);

    $this->assertEquals('John', $agent->getFirstName());
    $this->assertEquals('Doe', $agent->getLastName());
    $this->assertEquals('john@example.com', $agent->getEmail());
    $this->assertEquals('CRM-123', $agent->getExternalId());
  }

  /**
   * Tests agent save and load.
   */
  public function testSaveAndLoadAgent(): void {
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Jane',
      'last_name' => 'Smith',
      'email' => 'jane@example.com',
      'status' => TRUE,
    ]);

    $agent->save();
    $id = $agent->id();

    $this->assertIsInt($id);

    $loaded = Agent::load($id);
    $this->assertNotNull($loaded);
    $this->assertEquals('Jane', $loaded->getFirstName());
    $this->assertEquals('Smith', $loaded->getLastName());
  }

  /**
   * Tests agent label.
   */
  public function testAgentLabel(): void {
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Test',
      'last_name' => 'Agent',
    ]);

    $this->assertEquals('Agent', $agent->label());
  }

  /**
   * Tests agent status methods.
   */
  public function testAgentStatus(): void {
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Test',
      'last_name' => 'Active',
      'status' => TRUE,
    ]);

    $this->assertTrue($agent->isActive());

    $agent->setActive(FALSE);
    $this->assertFalse($agent->isActive());

    $agent->setActive(TRUE);
    $this->assertTrue($agent->isActive());
  }

  /**
   * Tests international phone normalization.
   */
  public function testInternationalPhoneNormalization(): void {
    /** @var \Drupal\phone_international\Helpers\PhoneNumberInterface $validator */
    $validator = $this->container->get('phone_international.validate');

    $this->assertTrue($validator->isValidNumber('+33 7 62 89 61 28'));
    $this->assertSame('+33762896128', $validator->formatNumber('+33 7 62 89 61 28'));

    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Sophie',
      'last_name' => 'Dacosta',
      'phone' => '+33 7 62 89 61 28',
    ]);
    $agent->save();

    $loaded = Agent::load($agent->id());
    $this->assertSame('+33762896128', $loaded?->getPhone());
  }

  /**
   * Tests the admin collection route path.
   */
  public function testAdminCollectionRoutePath(): void {
    $route = $this->container->get('router.route_provider')->getRouteByName('entity.agent.collection');

    $this->assertSame('/admin/ps/content/agents', $route->getPath());
  }

}
