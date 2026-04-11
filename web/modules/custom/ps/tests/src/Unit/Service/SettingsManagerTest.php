<?php

declare(strict_types=1);

namespace Drupal\Tests\ps\Unit\Service;

use Drupal\Core\Config\ConfigFactory;
use Drupal\ps\Service\SettingsManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps\Service\SettingsManager
 * @group ps
 */
final class SettingsManagerTest extends UnitTestCase {

  /**
   * Mock config factory.
   */
  private ConfigFactory&MockObject $configFactory;

  /**
   * Settings manager under test.
   */
  private SettingsManager $settingsManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->configFactory = $this->createMock(ConfigFactory::class);
    $this->settingsManager = new SettingsManager($this->configFactory);
  }

  /**
   * @covers ::get
   */
  public function testGetReturnsValue(): void {
    $config = $this->createMock('Drupal\Core\Config\Config');
    $config->method('getRawData')->willReturn(['key' => 'value']);
    $this->configFactory->method('get')->willReturn($config);

    $result = $this->settingsManager->get('key');
    $this->assertSame('value', $result);
  }

  /**
   * @covers ::get
   */
  public function testGetReturnsDefaultWhenNotFound(): void {
    $config = $this->createMock('Drupal\Core\Config\Config');
    $config->method('getRawData')->willReturn([]);
    $this->configFactory->method('get')->willReturn($config);

    $result = $this->settingsManager->get('missing', 'default');
    $this->assertSame('default', $result);
  }

  /**
   * @covers ::getAll
   */
  public function testGetAllReturnsAllSettings(): void {
    $expected = ['key1' => 'value1', 'key2' => 'value2'];
    $config = $this->createMock('Drupal\Core\Config\Config');
    $config->method('getRawData')->willReturn($expected);
    $this->configFactory->method('get')->willReturn($config);

    $result = $this->settingsManager->getAll();
    $this->assertSame($expected, $result);
  }

}
