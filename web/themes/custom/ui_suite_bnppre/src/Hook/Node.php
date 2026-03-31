<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\Element;

/**
 * Hooks for Node support.
 */
class Node {

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_node_preview_form_select_alter')]
  public function alter(array &$form, FormStateInterface $formState, string $formId): void {
    // @phpstan-ignore-next-line
    $element = Element::create($form);

    $element->addClass([
      'row',
      'row-cols-auto',
      'justify-content-center',
      'align-items-center',
      'bg-info-subtle',
      'text-center',
      'pt-3',
      'gap-sm-5',
    ]);

    // Backlink.
    if (isset($element->backlink) && $element->backlink instanceof Element) {
      /** @var array{attributes?: array{class?: string|array}} $options */
      $options = $element->backlink->getProperty('options', []);
      $element->backlink->addClass($options['attributes']['class'] ?? []);
      $element->backlink->addClass(['btn', 'btn-info', 'mb-3']);
      $element->backlink->setIcon((Bootstrap::icon('chevron-left')));

      // Ensure the UUID is set.
      if (isset($element->uuid) && $element->uuid instanceof Element) {
        $uuid = $element->uuid->getProperty('value');
        if ($uuid) {
          $options['query'] = ['uuid' => $uuid];
        }
      }
      // Override the options attributes.
      $options['attributes'] = $element->backlink->getAttributes()->getArrayCopy();

      $element->backlink->setProperty('options', $options);
    }

    // View mode.
    if (isset($element->view_mode) && $element->view_mode instanceof Element) {
      $element->view_mode->setProperty('title_display', 'inline');
      $element->view_mode->addClass(['col-auto', 'pe-0'], Element::LABEL);
      $element->view_mode->addClass('col-auto', Element::INNER_WRAPPER);
    }
  }

}
