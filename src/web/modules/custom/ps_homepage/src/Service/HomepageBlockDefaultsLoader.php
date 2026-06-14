<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Extension\ExtensionPathResolver;

/**
 * Loads per-language default configuration for homepage LB blocks.
 */
final class HomepageBlockDefaultsLoader {

  /**
   * @var array<string, mixed>|null
   */
  private ?array $defaults = NULL;

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  /**
   * Returns default block configuration for a plugin and language.
   *
   * @return array<string, mixed>
   */
  public function forPlugin(string $pluginId, string $langcode): array {
    $all = $this->loadDefaults();
    $pluginDefaults = $all[$pluginId][$langcode] ?? $all[$pluginId]['en'] ?? [];
    return is_array($pluginDefaults) ? $pluginDefaults : [];
  }

  /**
   * @return array<string, mixed>
   */
  private function loadDefaults(): array {
    if ($this->defaults !== NULL) {
      return $this->defaults;
    }

    $modulePath = $this->extensionPathResolver->getPath('module', 'ps_homepage');
    $file = $modulePath . '/data/homepage_block_defaults.yml';
    if (!is_readable($file)) {
      $this->defaults = [];
      return $this->defaults;
    }

    $parsed = Yaml::decode((string) file_get_contents($file));
    $this->defaults = is_array($parsed) ? $parsed : [];
    return $this->defaults;
  }

}
