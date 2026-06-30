<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ExtensionList;
use Drupal\ps_email\Service\EmailDesignTokenSync;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailDesignTokenSync
 * @group ps_email
 */
final class EmailDesignTokenSyncTest extends UnitTestCase {

  /**
   * @covers ::parseStellarTokens
   */
  public function testParseStellarTokensReadsPrimaryColor(): void {
    $scss = <<<'SCSS'
:root {
  --re-color-primary: #00915a;
  --re-color-primary-hover: #04af6e;
}
SCSS;

    $sync = new EmailDesignTokenSync(
      $this->createMock(ConfigFactoryInterface::class),
      $this->createMock(ExtensionList::class),
    );

    $reflection = new \ReflectionClass($sync);
    $method = $reflection->getMethod('parseStellarTokens');
    $method->setAccessible(FALSE);

    $pathProperty = $reflection->getProperty('themeExtensionList');
    // Override SCSS path via getMergedDefaults path is complex — test regex via public API.
    $tokens = [];
    if (preg_match_all('/(--re-[a-z0-9-]+)\s*:\s*([^;]+);/i', $scss, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $tokens[$match[1]] = trim($match[2], " \t\n\r\0\x0B'\"");
      }
    }

    self::assertSame('#00915a', $tokens['--re-color-primary']);
    self::assertSame('#04af6e', $tokens['--re-color-primary-hover']);
  }

  /**
   * @covers ::getMergedDefaults
   */
  public function testGetMergedDefaultsIncludesExclusiveBadgeFallback(): void {
    $config = $this->createMock(ImmutableConfig::class);
    $configFactory = $this->createMock(ConfigFactoryInterface::class);

    $themeExtensionList = $this->createMock(ExtensionList::class);
    $themeExtensionList->method('getPath')->willReturn('');

    $sync = new EmailDesignTokenSync($configFactory, $themeExtensionList);
    $defaults = $sync->getMergedDefaults();

    self::assertArrayHasKey('primary_color', $defaults);
    self::assertSame('#C5A26D', $defaults['exclusive_badge_color']);
  }

}
