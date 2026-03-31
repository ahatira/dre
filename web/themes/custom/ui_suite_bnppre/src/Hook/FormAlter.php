<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;

/**
 * Alter forms.
 */
class FormAlter {

  /**
   * List of forms IDs considered as Layout Builder related.
   *
   * @var string[]
   */
  public const array LAYOUT_BUILDER_FORM_IDS = [
    'layout_builder_add_block',
    'layout_builder_block_move',
    'layout_builder_configure_section',
    'layout_builder_remove_block',
    'layout_builder_remove_section',
    'layout_builder_update_block',
    'layout_builder_block_clone.clone_block_form',
    'section_library_add_section_to_library',
    'section_library_add_template_to_library',
  ];

  /**
   * List of partial forms IDs considered as Layout Builder related.
   *
   * @var string[]
   */
  public const array LAYOUT_BUILDER_FORM_IDS_CONTAINS = [
    'layout_builder_translate_form',
  ];

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function alter(array &$form, FormStateInterface $formState, string $form_id): void {
    // See \Drupal\Core\Form\FormBuilder::processForm().
    if ($formState->isMethodType('get') && $formState->getAlwaysProcess()) {
      $form['#after_build'][] = [
        static::class,
        'afterBuildAddGetFormMethod',
      ];
    }

    if ($this->isOffcanvasForm($form, $form_id)) {
      // Even with the after build, some elements like actions are not marked,
      // so marked it directly.
      static::markElements($form);
      // Use #after_build otherwise we do not have access to all subform
      // elements.
      $form['#after_build'][] = [
        static::class,
        'afterBuildMarkLayoutBuilder',
      ];
    }

    // Specific Display builder override.
    if (\str_starts_with($form_id, 'display_builder') || \str_starts_with($form_id, 'display-builder')) {
      // Even with the after build, some elements like actions are not marked,
      // so marked it directly.
      static::markElements($form, '#isDisplayBuilder');
      // Use #after_build otherwise we do not have access to all subform
      // elements.
      $form['#after_build'][] = [
        static::class,
        'afterBuildMarkDisplayBuilder',
      ];
    }

    // Default styling for Layout Builder "main" form.
    // There is no specific form ID to target as the base form ID is dynamic in
    // LayoutBuilderEntityFormTrait.
    if (\str_ends_with($form_id, '_layout_builder_form')) {
      $this->alterLayoutBuilderForm($form, $formState);
    }
  }

  /**
   * Form element #after_build callback.
   */
  public static function afterBuildAddGetFormMethod(array $element, FormStateInterface $form_state): array {
    static::addGetFormMethod($element);
    return $element;
  }

  /**
   * Form element #after_build callback.
   */
  public static function afterBuildMarkLayoutBuilder(array $element, FormStateInterface $form_state): array {
    static::markElements($element);
    return $element;
  }

  /**
   * Form element #after_build callback.
   */
  public static function afterBuildMarkDisplayBuilder(array $element, FormStateInterface $form_state): array {
    static::markElements($element, '#isDisplayBuilder');
    return $element;
  }

  /**
   * Set form method to all form elements.
   *
   * To allow other processing to act based on this information.
   *
   * @param array $form
   *   The form or form element which children should have form method attached.
   */
  protected static function addGetFormMethod(array &$form): void {
    /** @var string|int $child */
    foreach (Element::children($form) as $child) {
      if (!isset($form[$child]['#form_method'])) {
        $form[$child]['#form_method'] = 'get';
      }
      // @phpstan-ignore-next-line
      static::addGetFormMethod($form[$child]);
    }
  }

  /**
   * Detect if Layout Builder offcanvas form.
   *
   * @param array $form
   *   The form structure.
   * @param string $form_id
   *   The form ID.
   *
   * @return bool
   *   True for offcanvas form.
   *
   * @see gin_lb_is_layout_builder_form_id()
   */
  protected function isOffcanvasForm(array &$form, string $form_id): bool {
    if (\in_array($form_id, $this::LAYOUT_BUILDER_FORM_IDS, TRUE)) {
      return TRUE;
    }

    foreach ($this::LAYOUT_BUILDER_FORM_IDS_CONTAINS as $form_id_contain) {
      if (\str_contains($form_id, $form_id_contain)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Set mark to all form elements.
   *
   * @param array $form
   *   The form or form element which children should have form id attached.
   * @param string $property
   *   The property to store the mark.
   */
  protected static function markElements(array &$form, string $property = '#isOffcanvas'): void {
    /** @var string|int $child */
    foreach (Element::children($form) as $child) {
      if (!isset($form[$child][$property])) {
        $form[$child][$property] = TRUE;
      }
      // @phpstan-ignore-next-line
      static::markElements($form[$child], $property);
    }
  }

  /**
   * Default styling for Layout Builder "main" form.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   */
  protected function alterLayoutBuilderForm(array &$form, FormStateInterface $formState): void {
    $form['actions']['#attributes']['class'][] = 'mb-3';

    // Move preview checkbox out of actions so buttons are aligned.
    $form['preview_toggle'] = $form['actions']['preview_toggle'];
    unset($form['actions']['preview_toggle']);
  }

}
