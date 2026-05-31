<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_core\Service\ConfigResolver;
use Drupal\Tests\UnitTestCase;

final class ConfigResolverTest extends UnitTestCase {

  public function testGetReturnsValueOrDefault(): void {
    $config = $this->createMock(Config::class);
    $config
      ->method('get')
      ->willReturnMap([
        ['present_key', 'value'],
        ['missing_key', NULL],
      ]);

    $factory = $this->createMock(ConfigFactoryInterface::class);
    $factory
      ->method('get')
      ->with('ps_core.settings')
      ->willReturn($config);

    $resolver = new ConfigResolver($factory);

    self::assertSame('value', $resolver->get('ps_core.settings', 'present_key', 'fallback'));
    self::assertSame('fallback', $resolver->get('ps_core.settings', 'missing_key', 'fallback'));
  }

}
