<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\Core\Url;

/**
 * Builds the homepage search hero delegate bar SDC.
 */
final class HeroDelegateBarBuilder {

  /**
   * Builds the delegate bar SDC render array.
   *
   * @param array<string, string> $labels
   *   Localized delegate labels from block configuration.
   *
   * @return array<string, mixed>
   *   Render array for search-hero-delegate.
   */
  public function build(array $labels): array {
    $buttonUrl = trim((string) ($labels['delegate_url'] ?? ''));
    if ($buttonUrl === '') {
      $buttonUrl = '/contact';
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero-delegate',
      '#props' => [
        'prompt' => (string) ($labels['delegate_prompt'] ?? ''),
        'tooltip' => (string) ($labels['delegate_tooltip'] ?? ''),
        'button_label' => (string) ($labels['delegate_button_label'] ?? ''),
        'button_url' => Url::fromUserInput($buttonUrl)->toString(),
      ],
    ];
  }

}
