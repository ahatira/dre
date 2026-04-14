<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\ps_agent\Entity\Agent;

/**
 * Tests for Agent Manager service.
 *
 * @group ps_agent
 */
class AgentManagerTest extends EntityKernelTestBase {

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
   * Tests get active agents.
   */
  public function testGetActiveAgents(): void {
    $manager = \Drupal::service('ps_agent.manager');

    // Create test agents.
    Agent::create([
      'type' => 'default',
      'first_name' => 'Active1',
      'last_name' => 'Agent',
      'status' => TRUE,
    ])->save();

    Agent::create([
      'type' => 'default',
      'first_name' => 'Inactive',
      'last_name' => 'Agent',
      'status' => FALSE,
    ])->save();

    Agent::create([
      'type' => 'default',
      'first_name' => 'Active2',
      'last_name' => 'Agent',
      'status' => TRUE,
    ])->save();

    $agents = $manager->getActiveAgents();
    $this->assertCount(2, $agents);
  }

  /**
   * Tests get agent by external ID.
   */
  public function testGetAgentByExternalId(): void {
    $manager = \Drupal::service('ps_agent.manager');

    Agent::create([
      'type' => 'default',
      'first_name' => 'John',
      'last_name' => 'Doe',
      'external_id' => 'CRM-12345',
    ])->save();

    $agent = $manager->getAgentByExternalId('CRM-12345');
    $this->assertNotNull($agent);
    $this->assertEquals('John', $agent->getFirstName());

    $notFound = $manager->getAgentByExternalId('NONEXISTENT');
    $this->assertNull($notFound);
  }

  /**
   * Tests agent exists check.
   */
  public function testAgentExists(): void {
    $manager = \Drupal::service('ps_agent.manager');

    Agent::create([
      'type' => 'default',
      'first_name' => 'Test',
      'last_name' => 'Agent',
      'external_id' => 'EXT-123',
    ])->save();

    $this->assertTrue($manager->agentExists('external_id', 'EXT-123'));
    $this->assertFalse($manager->agentExists('external_id', 'NONEXISTENT'));
  }

  /**
   * Tests create agent.
   */
  public function testCreateAgent(): void {
    $manager = \Drupal::service('ps_agent.manager');

    $agent = $manager->createAgent('Jane', 'Smith', [
      'email' => 'jane@example.com',
      'external_id' => 'CRM-999',
      'type' => 'default',
    ]);

    $this->assertEquals('Jane', $agent->getFirstName());
    $this->assertEquals('Smith', $agent->getLastName());
    $this->assertEquals('jane@example.com', $agent->getEmail());
    $this->assertTrue($agent->isActive());
  }

  /**
   * Tests get agents by field.
   */
  public function testGetAgentsByField(): void {
    $manager = \Drupal::service('ps_agent.manager');

    Agent::create([
      'type' => 'default',
      'first_name' => 'John',
      'last_name' => 'Test',
      'status' => TRUE,
    ])->save();

    Agent::create([
      'type' => 'default',
      'first_name' => 'Jane',
      'last_name' => 'Test',
      'status' => TRUE,
    ])->save();

    $agents = $manager->getAgentsByField('last_name', 'Test');
    $this->assertCount(2, $agents);
  }

}
