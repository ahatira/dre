<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_patterns\Plugin\UiPatterns\PropType\LinksPropType;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;

/**
 * Ensure links structure fits into dropdown structure.
 */
class PreprocessLinksDropbutton {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_links__dropbutton')]
  public function preprocess(array &$variables): void {
    if (empty($variables['links']) || !\is_array($variables['links'])) {
      return;
    }

    /** @var array{array{title: string, url?: string, attributes?: array}} $links */
    $links = LinksPropType::normalize(\array_filter(
      $variables['links'],
    ));
    $first_link = \array_shift($links);

    // Not exactly a variant detection method but it is ok for now.
    $button_variant = \str_replace('-', '_', Bootstrap::cssClassFromString($first_link['title'], 'outline_dark'));

    // Detect size from type.
    if (isset($variables['attributes']['dropbutton_type']) && \is_string($variables['attributes']['dropbutton_type'])) {
      if (\str_contains($variables['attributes']['dropbutton_type'], 'small')) {
        $button_variant .= '__sm';
      }
      if (\str_contains($variables['attributes']['dropbutton_type'], 'large')) {
        $button_variant .= '__lg';
      }
      unset($variables['attributes']['dropbutton_type']);
    }

    $variables['dropdown'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:dropdown',
      '#props' => [
        'button_url' => $first_link['url'] ?? '',
        'button_attributes' => $first_link['attributes'] ?? NULL,
        'button_variant' => $button_variant,
        // @phpstan-ignore-next-line
        'button_split' => !empty($links),
        // @phpstan-ignore-next-line
        'content' => empty($links) ? [] : $links,
      ],
      '#slots' => [
        'title' => $first_link['title'],
      ],
      '#attributes' => $variables['attributes'],
    ];
  }

}
