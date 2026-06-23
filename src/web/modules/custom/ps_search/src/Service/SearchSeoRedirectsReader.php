<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Resolves configured SEO migration redirects (M6 clean-break 301 map).
 */
final class SearchSeoRedirectsReader {

  private const CONFIG_NAME = 'ps_search.seo_redirects';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns the redirect target path for an incoming request path.
   *
   * @return string|null
   *   Root-relative target path, or NULL when no redirect applies.
   */
  public function resolveTarget(string $pathInfo): ?string {
    $config = $this->configFactory->get(self::CONFIG_NAME);
    if (!(bool) ($config->get('enabled') ?? TRUE)) {
      return NULL;
    }

    $redirects = $config->get('redirects') ?? [];
    if (!is_array($redirects) || $redirects === []) {
      return NULL;
    }

    foreach ($this->pathVariants($pathInfo) as $candidate) {
      if (isset($redirects[$candidate]) && is_string($redirects[$candidate])) {
        $target = trim($redirects[$candidate]);
        if ($target !== '' && $target !== $candidate) {
          return $this->normalizeConfiguredPath($target);
        }
      }
    }

    return NULL;
  }

  /**
   * Builds path lookup candidates with and without trailing slash.
   *
   * @return list<string>
   *   Normalized path variants to match against configured keys.
   */
  private function pathVariants(string $pathInfo): array {
    $normalized = $this->normalizeConfiguredPath($pathInfo);
    if ($normalized === '/') {
      return ['/'];
    }

    $withoutTrailing = rtrim($normalized, '/');
    $withTrailing = $withoutTrailing . '/';

    return array_values(array_unique([$withTrailing, $withoutTrailing]));
  }

  /**
   * Ensures a root-relative path with a leading slash.
   */
  private function normalizeConfiguredPath(string $path): string {
    $path = trim($path);
    if ($path === '' || $path === '/') {
      return '/';
    }

    if (!str_starts_with($path, '/')) {
      $path = '/' . $path;
    }

    return $path;
  }

}
