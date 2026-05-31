<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_agent\Entity\Agent;
use Drupal\ps_agent\Entity\AgentType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Runner\Extension\RunTestsInSeparateProcesses;

/**
 * Tests the Agent entity.
 */
#[Group('ps_agent')]
#[RunTestsInSeparateProcesses]
final class AgentEntityTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'image',
    'file',
    'ps_core',
    'ps_dictionary',
    'ps_agent',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installEntitySchema('ps_agent');
    $this->installEntitySchema('ps_agent_type');
    $this->installConfig(['ps_agent']);
  }

  /**
   * Tests basic CRUD operations on Agent entity.
   */
  public function testAgentCrud(): void {
    // Create an agent.
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john.doe@example.com',
      'phone' => '+33123456789',
      'job_title' => 'Sales Manager',
      'internal_external' => 'internal',
      'status' => TRUE,
    ]);

    $agent->save();

    $this->assertNotNull($agent->id(), 'Agent entity was saved and has an ID.');
    $this->assertEquals('John', $agent->get('first_name')->value);
    $this->assertEquals('Doe', $agent->get('last_name')->value);
    $this->assertEquals('john.doe@example.com', $agent->get('email')->value);

    // Test computed display_name field.
    $this->assertEquals('John Doe', $agent->get('display_name')->value, 'Computed display_name is correct.');

    // Test has_avatar calculated field (should be FALSE when no avatar).
    $this->assertFalse($agent->get('has_avatar')->value, 'has_avatar is FALSE when no avatar is set.');

    // Load the agent.
    $loaded_agent = Agent::load($agent->id());
    $this->assertInstanceOf(Agent::class, $loaded_agent);
    $this->assertEquals('John', $loaded_agent->get('first_name')->value);

    // Update the agent.
    $loaded_agent->set('first_name', 'Jane');
    $loaded_agent->save();

    $reloaded_agent = Agent::load($agent->id());
    $this->assertEquals('Jane', $reloaded_agent->get('first_name')->value);
    $this->assertEquals('Jane Doe', $reloaded_agent->get('display_name')->value, 'Computed display_name updated after first_name change.');

    // Delete the agent.
    $loaded_agent->delete();
    $deleted_agent = Agent::load($agent->id());
    $this->assertNull($deleted_agent, 'Agent entity was deleted.');
  }

  /**
   * Tests the has_avatar calculated field.
   */
  public function testHasAvatarCalculation(): void {
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Test',
      'last_name' => 'User',
      'email' => 'test@example.com',
      'status' => TRUE,
    ]);

    // Before save, has_avatar should not be set yet.
    $agent->save();

    // After save, has_avatar should be FALSE (no avatar).
    $this->assertFalse($agent->get('has_avatar')->value, 'has_avatar is FALSE when no avatar.');

    // Note: Testing with actual file upload requires more setup.
    // This tests the basic calculation logic.
  }

  /**
   * Tests the bundle entity type.
   */
  public function testAgentTypeBundle(): void {
    // The default bundle should exist from config.
    $default_type = AgentType::load('default');
    $this->assertInstanceOf(AgentType::class, $default_type);
    $this->assertEquals('Agent', $default_type->label());

    // Create a new bundle.
    $partner_type = AgentType::create([
      'id' => 'partner',
      'label' => 'Partner',
    ]);
    $partner_type->save();

    $loaded_partner_type = AgentType::load('partner');
    $this->assertInstanceOf(AgentType::class, $loaded_partner_type);
    $this->assertEquals('Partner', $loaded_partner_type->label());

    // Create an agent with the new bundle.
    $partner_agent = Agent::create([
      'type' => 'partner',
      'first_name' => 'Partner',
      'last_name' => 'Agent',
      'email' => 'partner@example.com',
      'status' => TRUE,
    ]);
    $partner_agent->save();

    $this->assertEquals('partner', $partner_agent->bundle());
  }

  /**
   * Tests field constraints and validation.
   */
  public function testFieldValidation(): void {
    // Test with minimal required fields.
    $agent = Agent::create([
      'type' => 'default',
      'first_name' => 'Minimal',
      'last_name' => 'Agent',
    ]);

    $violations = $agent->validate();
    $this->assertCount(0, $violations, 'Agent with minimal fields is valid.');

    $agent->save();
    $this->assertNotNull($agent->id());
  }

}
