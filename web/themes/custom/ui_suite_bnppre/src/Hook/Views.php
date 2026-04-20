<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;

/**
 * Hooks for Views support.
 */
class Views {

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_views_exposed_form_alter')]
  public function viewsExposedFormAlter(array &$form, FormStateInterface $formState, string $form_id): void {
    $form_dom_id = (string) ($form['#id'] ?? '');
    $is_offer_search_form = str_contains($form_dom_id, 'views-exposed-form-ps-offer-search-page-1');

    if ($is_offer_search_form) {
      $form['#attributes']['class'][] = 'ps-offer-search-exposed';
    }
    else {
      $form['#attributes']['class'][] = 'row';
      $form['#attributes']['class'][] = 'row-cols-auto';
      $form['#attributes']['class'][] = 'align-items-end';
    }

    if (isset($form['actions'])) {
      $form['actions']['#attributes']['class'][] = 'mb-3';
    }
    // Reset button.
    if (isset($form['actions']['reset'])) {
      $form['actions']['reset']['#attributes']['class'][] = 'ms-2';
    }

    // @phpstan-ignore-next-line
    if (!\str_starts_with($form_dom_id, 'views-exposed-form-media-library-widget')) {
      return;
    }
    $form['#attributes']['class'][] = 'm-1';
    $form['#attributes']['class'][] = 'mb-3';
    $form['#attributes']['class'][] = 'p-2';
    $form['#attributes']['class'][] = 'border';
  }

  /**
   * Implements hook_form_alter().
   *
   * Default styling for views bulk actions forms.
   */
  #[Hook('form_alter')]
  public function viewsBulkActionFormAlter(array &$form, FormStateInterface $formState, string $form_id): void {
    // There is no specific form ID to target.
    if (!\is_string($form['#id']) || \str_starts_with($form['#id'], 'views-form')) {
      return;
    }

    if (!isset($form['header']) || !\is_array($form['header'])) {
      return;
    }

    /** @var string[] $headerElements */
    $headerElements = Element::children($form['header']);
    foreach ($headerElements as $headerElement) {
      if (!\str_ends_with($headerElement, '_bulk_form')) {
        continue;
      }

      $form['header'][$headerElement]['#attributes']['class'][] = 'row';
      $form['header'][$headerElement]['#attributes']['class'][] = 'row-cols-auto';
      $form['header'][$headerElement]['#attributes']['class'][] = 'align-items-end';
      if (isset($form['header'][$headerElement]['actions'])) {
        $form['header'][$headerElement]['actions']['#attributes']['class'][] = 'mb-3';
      }
    }
  }

}
