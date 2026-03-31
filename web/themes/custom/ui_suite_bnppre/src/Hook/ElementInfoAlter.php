<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnppre\Element\ElementPreRenderContextualLinksPlaceholder;
use Drupal\ui_suite_bnppre\Element\ElementPreRenderDropbutton;
use Drupal\ui_suite_bnppre\Element\ElementPreRenderLayoutBuilder;
use Drupal\ui_suite_bnppre\Element\ElementPreRenderLink;
use Drupal\ui_suite_bnppre\Element\ElementProcessActions;
use Drupal\ui_suite_bnppre\Element\ElementProcessAjax;
use Drupal\ui_suite_bnppre\Element\ElementProcessCheckboxes;
use Drupal\ui_suite_bnppre\Element\ElementProcessIconAutocomplete;
use Drupal\ui_suite_bnppre\Element\ElementProcessInputGroup;
use Drupal\ui_suite_bnppre\Element\ElementProcessRadios;
use Drupal\ui_suite_bnppre\Element\ElementProcessTextFormat;

/**
 * Element Info Alter.
 */
class ElementInfoAlter
{
  /**
   * Constructs an ElementInfoAlter instance.
   *
   * @param \Drupal\ui_suite_bnppre\Element\ElementProcessIconAutocomplete $elementProcessIconAutocomplete
   *   The icon autocomplete process handler.
   */
  /**
   * List of additional properties on checkbox.
   *
   * @var array<string, bool>
   */
    public const array CHECKBOX_PROPERTIES = [
    'is_inline' => false,
    'is_reverse' => false,
    'is_switch' => false,
    ];

  /**
   * List of additional properties on radios.
   *
   * @var array<string, bool>
   */
    public const array RADIOS_PROPERTIES = [
    'is_inline' => false,
    'is_reverse' => false,
    ];

  /**
   * List of additional properties for input group feature.
   *
   * @var array<string, bool|array>
   */
    public const array INPUT_GROUP_PROPERTIES = [
    'input_group_attributes' => [],
    'input_group_after' => [],
    'input_group_before' => [],
    'input_group_button' => false,
    ];

  /**
   * List of form elements supporting input group.
   *
   * @var string[]
   */
    public const array INPUT_GROUP_ELEMENTS = [
    'color',
    'date',
    'email',
    'entity_autocomplete',
    'file',
    'language_select',
    'machine_name',
    'managed_file',
    'number',
    'password',
    'password_confirm',
    'search',
    'select',
    'tel',
    'text_format',
    'textarea',
    'textfield',
    'url',
    'weight',
    ];

  /**
   * List of form elements supporting ajax throbber put as input group.
   *
   * @var string[]
   */
    public const array AJAX_INPUT_GROUP_ELEMENTS = [
    'date',
    'email',
    'entity_autocomplete',
    'file',
    'language_select',
    'machine_name',
    'managed_file',
    'number',
    'password',
    'password_confirm',
    'search',
    'select',
    'tel',
    'textarea',
    'textfield',
    'url',
    'weight',
    ];

  /**
   * List of additional properties for floating label feature.
   *
   * @var array<string, bool>
   */
    public const array FLOATING_LABEL_PROPERTIES = [
    'floating_label' => false,
    ];

  /**
   * List of form elements supporting floating label.
   *
   * @var string[]
   */
    public const array FLOATING_LABEL_ELEMENTS = [
    'date',
    'email',
    'entity_autocomplete',
    'language_select',
    'machine_name',
    'number',
    'password',
    'password_confirm',
    'search',
    'select',
    'tel',
    'text_format',
    'textarea',
    'textfield',
    'url',
    'weight',
    ];

  /**
   * List of additional properties for icon feature.
   *
   * @var array<string, mixed>
   */
    public const array ICON_PROPERTIES = [
    'icon' => [],
    'icon_position' => 'before',
    ];

  /**
   * List of form elements supporting icon.
   *
   * @var string[]
   */
    public const array ICON_ELEMENTS = [
    'button',
    'link',
    'submit',
    ];

  /**
   * Implements hook_element_info_alter().
   */
    #[Hook('element_info_alter')]
    public function alter(array &$info): void
    {
      // Sort the types for easier debugging.
        \ksort($info, \SORT_NATURAL);

      // Actions.
        if (isset($info['actions'])) {
            $info['actions']['#process'][] = [
            ElementProcessActions::class,
            'processActions',
            ];
        }

      // Ajax.
        foreach (static::AJAX_INPUT_GROUP_ELEMENTS as $form_element_id) {
            if (!isset($info[$form_element_id])) {
                continue;
            }

            $info[$form_element_id]['#process'][] = [
            ElementProcessAjax::class,
            'processAjax',
            ];
        }

      // Checkbox.
        if (isset($info['checkbox'])) {
            foreach (static::CHECKBOX_PROPERTIES as $property => $property_default_value) {
                $info['checkbox']["#{$property}"] = $property_default_value;
            }
        }

      // Checkboxes.
        if (isset($info['checkboxes'])) {
            foreach (static::CHECKBOX_PROPERTIES as $property => $property_default_value) {
                $info['checkboxes']["#{$property}"] = $property_default_value;
            }
            $info['checkboxes']['#process'][] = [
            ElementProcessCheckboxes::class,
            'processCheckboxes',
            ];
        }

      // Contextual link placeholder.
        if (isset($info['contextual_links_placeholder'])) {
            $info['contextual_links_placeholder']['#pre_render'][] = [
            ElementPreRenderContextualLinksPlaceholder::class,
            'preRenderContextualLinksPlaceholder',
            ];
        }

      // Dropbutton.
        if (isset($info['dropbutton'])) {
            if (!isset($info['dropbutton']['#pre_render']) || !\is_array($info['dropbutton']['#pre_render'])) {
                $info['dropbutton']['#pre_render'] = [];
            }

          // Remove Core pre_render to remove wrapper and classes.
          // @phpstan-ignore-next-line
            foreach ($info['dropbutton']['#pre_render'] as $key => $pre_render_infos) {
                if ($info['dropbutton']['#pre_render'][$key][0] == 'Drupal\Core\Render\Element\Dropbutton') {
                    unset($info['dropbutton']['#pre_render'][$key]);
                    break;
                }
            }

            $info['dropbutton']['#pre_render'][] = [
            ElementPreRenderDropbutton::class,
            'preRenderDropbutton',
            ];
        }

      // Floating label.
        foreach (static::FLOATING_LABEL_ELEMENTS as $form_element_id) {
            if (!isset($info[$form_element_id])) {
                continue;
            }

            foreach (static::FLOATING_LABEL_PROPERTIES as $property => $property_default_value) {
                $info[$form_element_id]["#{$property}"] = $property_default_value;
            }
        }

      // Layout Builder.
        if (isset($info['layout_builder'])) {
            $info['layout_builder']['#pre_render'][] = [
            ElementPreRenderLayoutBuilder::class,
            'preRenderLayoutBuilder',
            ];
        }

      // Link.
        if (isset($info['link'])) {
            if (!isset($info['link']['#pre_render']) || !\is_array($info['link']['#pre_render'])) {
                $info['link']['#pre_render'] = [];
            }

          // Need to be placed first because a pre_render from core is doing early
          // rendering.
          // @phpstan-ignore-next-line
            \array_unshift($info['link']['#pre_render'], [
            ElementPreRenderLink::class,
            'preRenderLink',
            ]);
        }

      // Icon.
        foreach (static::ICON_ELEMENTS as $form_element_id) {
            if (!isset($info[$form_element_id])) {
                continue;
            }

            foreach (static::ICON_PROPERTIES as $property => $property_default_value) {
                $info[$form_element_id]["#{$property}"] = $property_default_value;
            }
        }

      // Icon Autocomplete.
        if (isset($info['icon_autocomplete'])) {
            $info['icon_autocomplete']['#process'][] = [
            ElementProcessIconAutocomplete::class,
            'processIconAutocomplete',
            ];
        }

      // Input group.
        foreach (static::INPUT_GROUP_ELEMENTS as $form_element_id) {
            if (!isset($info[$form_element_id])) {
                continue;
            }

            foreach (static::INPUT_GROUP_PROPERTIES as $property => $property_default_value) {
                $info[$form_element_id]["#{$property}"] = $property_default_value;
            }
            $info[$form_element_id]['#process'][] = [
            ElementProcessInputGroup::class,
            'processInputGroup',
            ];
        }

      // Radios.
        if (isset($info['radios'])) {
            foreach (static::RADIOS_PROPERTIES as $property => $property_default_value) {
                $info['radios']["#{$property}"] = $property_default_value;
            }
            $info['radios']['#process'][] = [
            ElementProcessRadios::class,
            'processRadios',
            ];
        }

      // Text format.
        if (isset($info['text_format'])) {
            $info['text_format']['#process'][] = [
            ElementProcessTextFormat::class,
            'processTextFormat',
            ];
        }
    }
}
