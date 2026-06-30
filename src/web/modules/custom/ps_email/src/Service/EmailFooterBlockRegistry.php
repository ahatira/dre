<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\ps_email\Plugin\EmailFooterBlock\EmailFooterBlockInterface;

/**
 * Registry of email footer block plugins keyed by plugin id.
 */
final class EmailFooterBlockRegistry {

  /**
   * @param array<string, \Drupal\ps_email\Plugin\EmailFooterBlock\EmailFooterBlockInterface> $blocks
   *   Footer block plugins keyed by id.
   */
  public function __construct(
    private readonly array $blocks,
  ) {}

  /**
   * Returns plugin definitions for the layout form.
   *
   * @return array<string, string>
   *   Plugin id => label.
   */
  public function getLabels(): array {
    $labels = [];
    foreach ($this->blocks as $id => $block) {
      $labels[$id] = $block->label();
    }
    asort($labels);
    return $labels;
  }

  /**
   * Builds HTML for a configured footer component.
   *
   * @param array<string, mixed> $component
   *   Component config (plugin, settings, weight).
   */
  public function buildComponent(array $component, ?string $langcode = NULL): string {
    $pluginId = (string) ($component['plugin'] ?? '');
    if ($pluginId === '' || !isset($this->blocks[$pluginId])) {
      return '';
    }

    $settings = is_array($component['settings'] ?? NULL) ? $component['settings'] : [];
    return $this->blocks[$pluginId]->build($settings, $langcode);
  }

  /**
   * Returns a plugin instance.
   */
  public function get(string $pluginId): ?EmailFooterBlockInterface {
    return $this->blocks[$pluginId] ?? NULL;
  }

}
