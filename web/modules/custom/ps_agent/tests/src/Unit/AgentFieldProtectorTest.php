<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\ps_agent\Service\AgentFieldProtector;

/**
 * @coversDefaultClass \Drupal\ps_agent\Service\AgentFieldProtector
 * @group ps_agent
 */
class AgentFieldProtectorTest extends UnitTestCase {

  /**
   * @covers ::isBoEditableField
   */
  public function testIsBoEditableFieldProtected(): void {
    $entityTypeManager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
    $settingsManager = $this->createMock('Drupal\ps\Service\SettingsManagerInterface');

    $settingsManager->expects($this->any())
      ->method('get')
      ->willReturn([]);

    $protector = new AgentFieldProtector($entityTypeManager, $settingsManager);

    $this->assertTrue($protector->isBoEditableField('email'));
    $this->assertTrue($protector->isBoEditableField('phone'));
    $this->assertFalse($protector->isBoEditableField('first_name'));
  }

  /**
   * @covers ::getBoEditableFields
   */
  public function testGetBoEditableFields(): void {
    $entityTypeManager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
    $settingsManager = $this->createMock('Drupal\ps\Service\SettingsManagerInterface');

    $settingsManager->expects($this->any())
      ->method('get')
      ->willReturn([]);

    $protector = new AgentFieldProtector($entityTypeManager, $settingsManager);
    $fields = $protector->getBoEditableFields();

    $this->assertContains('email', $fields);
    $this->assertContains('phone', $fields);
    $this->assertContains('mobile', $fields);
    $this->assertContains('internal_notes', $fields);
  }

}
