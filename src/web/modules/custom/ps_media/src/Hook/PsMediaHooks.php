<?php

declare(strict_types=1);

namespace Drupal\ps_media\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme registration hooks for PS Media.
 */
final class PsMediaHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    return [
      'gallery_summary' => [
        'variables' => [
          'offer' => NULL,
          'medias' => [],
          'order' => 'type_then_manual',
          'show_titles' => TRUE,
          'use_thumbnail' => TRUE,
          'lazy_load' => TRUE,
        ],
        'template' => 'gallery-summary',
      ],
      'documents_list' => [
        'variables' => [
          'offer' => NULL,
          'documents' => [],
          'show_titles' => TRUE,
          'link_text' => 'Download',
        ],
        'template' => 'documents-list',
      ],
    ];
  }

}
