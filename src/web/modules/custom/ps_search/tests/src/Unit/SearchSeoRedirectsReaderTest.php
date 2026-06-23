<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_search\Service\SearchSeoRedirectsReader;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchSeoRedirectsReader
 */
final class SearchSeoRedirectsReaderTest extends UnitTestCase {

  /**
   * @covers ::resolveTarget
   */
  public function testResolveTargetMatchesWithOrWithoutTrailingSlash(): void {
    $reader = $this->buildReader([
      '/a-louer/bureaux/paris/' => '/a-louer/bureaux/paris-75/',
    ]);

    self::assertSame(
      '/a-louer/bureaux/paris-75/',
      $reader->resolveTarget('/a-louer/bureaux/paris'),
    );
    self::assertSame(
      '/a-louer/bureaux/paris-75/',
      $reader->resolveTarget('/a-louer/bureaux/paris/'),
    );
  }

  /**
   * @covers ::resolveTarget
   */
  public function testResolveTargetReturnsNullWhenDisabled(): void {
    $reader = $this->buildReader([], FALSE);
    self::assertNull($reader->resolveTarget('/a-louer/bureaux/paris/'));
  }

  /**
   * Builds a reader backed by mocked config values.
   *
   * @param array<string, string> $redirects
   *   Configured redirect map.
   * @param bool $enabled
   *   Whether redirects are enabled.
   */
  private function buildReader(array $redirects, bool $enabled = TRUE): SearchSeoRedirectsReader {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(function (string $key) use ($redirects, $enabled) {
      return match ($key) {
        'enabled' => $enabled,
        'redirects' => $redirects,
        default => NULL,
      };
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_search.seo_redirects')->willReturn($config);

    return new SearchSeoRedirectsReader($configFactory);
  }

}
