<?php

declare(strict_types=1);

namespace Drupal\Tests\ps\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps\Service\NotificationManager;
use Drupal\Tests\UnitTestCase;
use Egulias\EmailValidator\EmailValidator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps\Service\NotificationManager
 * @group ps
 */
final class NotificationManagerTest extends UnitTestCase {

  /**
   * Mock email validator.
   */
  private EmailValidator&MockObject $emailValidator;

  /**
   * Mock logger factory.
   */
  private LoggerChannelFactoryInterface&MockObject $loggerFactory;

  /**
   * Mock logger.
   */
  private LoggerChannelInterface&MockObject $logger;

  /**
   * Mock config factory.
   */
  private ConfigFactoryInterface&MockObject $configFactory;

  /**
   * Notification manager.
   */
  private NotificationManager $manager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->emailValidator = $this->createMock(EmailValidator::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $this->logger = $this->createMock(LoggerChannelInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);

    $this->loggerFactory->method('get')->willReturn($this->logger);
    $this->manager = new NotificationManager(
      $this->emailValidator,
      $this->loggerFactory,
      $this->configFactory,
    );
  }

  /**
   * @covers ::getChannels
   */
  public function testGetChannelsReturnsArray(): void {
    $channels = $this->manager->getChannels();
    $this->assertIsArray($channels);
    $this->assertContains('email', $channels);
  }

  /**
   * @covers ::validateEmail
   */
  public function testValidateEmailReturnsBoolean(): void {
    $this->emailValidator->method('isValid')->willReturn(TRUE);
    $result = $this->manager->validateEmail('test@example.com');
    $this->assertTrue($result);
  }

}
