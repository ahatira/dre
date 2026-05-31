<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Unit;

use Drupal\ps_agent\Service\AgentValidationManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('ps_agent')]
final class AgentValidationManagerTest extends UnitTestCase {

  public function testMissingValues(): void {
    $manager = new AgentValidationManager();

    $errors = $manager->validateContactValues('', '');

    $this->assertArrayHasKey('email', $errors);
    $this->assertArrayHasKey('phone', $errors);
  }

  public function testInvalidPhoneFormat(): void {
    $manager = new AgentValidationManager();

    $errors = $manager->validateContactValues('agent@example.com', 'abc-phone');

    $this->assertArrayHasKey('phone', $errors);
  }

  public function testValidContactValues(): void {
    $manager = new AgentValidationManager();

    $errors = $manager->validateContactValues('agent@example.com', '+33123456789');

    $this->assertSame([], $errors);
  }

}
