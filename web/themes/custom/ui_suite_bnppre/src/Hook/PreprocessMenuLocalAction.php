<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;

/**
 * Add button style to local actions.
 */
class PreprocessMenuLocalAction {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_menu_local_action')]
  public function preprocess(array &$variables): void {
    /** @var array{title: string, url: \Drupal\Core\Url, localized_options?: array} $link */
    $link = $variables['element']['#link'];
    $link += [
      'title' => '',
      'localized_options' => [],
    ];
    $options = $link['localized_options'];

    // Turn link into a mini-button and colorize based on title.
    $class = Bootstrap::cssClassFromString($link['title'], 'outline-dark');
    if (!isset($options['attributes']['class'])) {
      $options['attributes']['class'] = [];
    }
    $string = \is_string($options['attributes']['class']);
    if ($string) {
      // @phpstan-ignore-next-line
      $options['attributes']['class'] = \explode(' ', $options['attributes']['class']);
    }
    $options['attributes']['class'][] = 'btn';
    $options['attributes']['class'][] = 'btn-sm';
    $options['attributes']['class'][] = 'btn-' . $class;
    if ($string) {
      // @phpstan-ignore-next-line
      $options['attributes']['class'] = \implode(' ', $options['attributes']['class']);
    }

    $variables['link'] = [
      '#type' => 'link',
      '#title' => $link['title'],
      '#options' => $options,
      '#url' => $link['url'],
      // @phpstan-ignore-next-line
      '#icon' => Bootstrap::iconFromString($link['title']),
    ];
  }

}
