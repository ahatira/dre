<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\Element as UsbElement;
use Drupal\views\ViewExecutable;

/**
 * Hooks for Media Library support.
 */
class MediaLibrary
{
  /**
   * Ensure the remove button is the first element.
   */
    public const REMOVE_BUTTON_WEIGHT = -10;

  /**
   * Ensure the edit link is after the remove button.
   */
    public const EDIT_LINK_WEIGHT = -5;

  /**
   * Implements hook_form_BASE_FORM_ID_alter().
   */
    #[Hook('form_media_library_add_form_alter')]
    public function mediaLibraryAddForm(array &$form, FormStateInterface $formState, string $formId): void
    {
        $this->styleWrapper($form);
        $this->styleCreatedMediaList($form);
    }

  /**
   * Implements hook_preprocess_HOOK().
   */
    #[Hook('preprocess_media_library_item')]
    public function preprocessMediaLibraryItem(array &$variables): void
    {
        $this->preprocessRemoveButton($variables);
        $this->preprocessEditLink($variables);
    }

  /**
   * Implements hook_preprocess_HOOK().
   *
   * Add icons in the media library view & contextual title for offer search.
   */
    #[Hook('preprocess_views_view')]
    public function preprocessViewsView(array &$variables): void
    {
      /** @var \Drupal\views\ViewExecutable $view */
        $view = $variables['view'];
        
        // Handle media library view (existing logic).
        if ($view->id() == 'media_library') {
            if (empty($variables['header']) || !\is_array($variables['header'])) {
                return;
            }

            $icons = [
            'widget' => 'grid-fill',
            'widget_table' => 'list-ul',
            ];

            foreach ($variables['header'] as $headerId => $header) {
                if (
                    isset($header['#type'])
                    && $header['#type'] == 'link'
                    // @phpstan-ignore-next-line
                    && isset($header['#options']['view'], $header['#options']['target_display_id'], $icons[$header['#options']['target_display_id']])
                ) {
                  // @phpstan-ignore-next-line
                    $element = UsbElement::create($variables['header'][$headerId]);
                    $element->setIcon(Bootstrap::icon($icons[$header['#options']['target_display_id']]));
                }
            }
            return;
        }
        
        // Handle offer search view (new logic).
        if ($view->id() == 'ps_offer_search' && $view->current_display == 'page_1') {
            $exposed_input = $view->getExposedInput();
            $title_parts = [];

            // Check if location filter is active.
            if (!empty($exposed_input['location_multi'])) {
                $locations = is_array($exposed_input['location_multi'])
                  ? $exposed_input['location_multi']
                  : explode('||', $exposed_input['location_multi']);
                $locations = array_filter(array_map('trim', $locations));
                if (!empty($locations)) {
                    $location_str = implode(', ', $locations);
                    $title_parts[] = t('Offices in @location', ['@location' => $location_str])->render();
                }
            }

            // Fallback title if no location specified.
            if (empty($title_parts)) {
                $title_parts[] = t('Offices')->render();
            }

            $variables['contextual_title'] = !empty($title_parts) ? implode(' – ', $title_parts) : NULL;
        }
    }

  /**
   * Implements hook_views_pre_render().
   *
   * Handle CSS classes for the media library.
   *
   * @see \claro_views_pre_render()
   */
    #[Hook('views_pre_render')]
    public function preRender(ViewExecutable $view): void
    {
        if ($view->id() != 'media_library') {
            return;
        }
        if ($view->current_display != 'widget') {
            return;
        }
        if (!\array_key_exists('media_library_select_form', $view->field)) {
            return;
        }

      // @phpstan-ignore-next-line
        $this->addClasses($view->field['media_library_select_form']->options['element_wrapper_class'], [
        'position-absolute',
        'ms-1',
        'z-1',
        ]);
    }

  /**
   * Add classes.
   *
   * @param string $option
   *   The existing option.
   * @param string[] $classesToAdd
   *   The classes to add.
   */
    protected function addClasses(string &$option, array $classesToAdd): void
    {
        $classes = \preg_split('/\s+/', $option);
        if (!\is_array($classes)) {
            return;
        }

        $classes = \array_filter($classes);
        $classes = \array_merge($classes, $classesToAdd);
        $option = \implode(' ', \array_unique($classes));
    }

  /**
   * Style the form wrapper, all steps.
   *
   * @param array $form
   *   The form structure.
   */
    protected function styleWrapper(array &$form): void
    {
        $form['#attributes']['class'][] = 'row';
        $form['#attributes']['class'][] = 'm-1';
        $form['#attributes']['class'][] = 'mb-3';
        $form['#attributes']['class'][] = 'p-2';
        $form['#attributes']['class'][] = 'border';
    }

  /**
   * Style the created media, second step.
   *
   * @param array $form
   *   The form structure.
   */
    protected function styleCreatedMediaList(array &$form): void
    {
        if (!isset($form['media']) || !\is_array($form['media'])) {
            return;
        }

        $form['media']['#attributes']['class'][] = 'list-unstyled';

      /** @var string|int $key */
        foreach (Element::children($form['media']) as $key) {
            $media = &$form['media'][$key];

            $media['#wrapper_attributes']['class'][] = 'row';

            $media['preview']['#attributes']['class'][] = 'col-2';
            $media['preview']['#attributes']['class'][] = 'bg-light';
            $media['preview']['#attributes']['class'][] = 'd-flex';
            $media['preview']['#attributes']['class'][] = 'align-items-center';
            $media['preview']['#attributes']['class'][] = 'justify-content-center';

            $media['fields']['#attributes']['class'][] = 'col-10';
            $media['fields']['#attributes']['class'][] = 'mt-3';

          // Need CSS for the special 'right', so handle all properties with CSS.
            $media['remove_button']['#attributes']['class'][] = 'media-added-remove-button';

          // @phpstan-ignore-next-line
            $element = UsbElement::create($media['remove_button']);
            $element->setIcon(Bootstrap::icon('x-lg'));
        }
    }

  /**
   * Add icon on remove button.
   *
   * @param array $variables
   *   The preprocessed variables.
   */
    protected function preprocessRemoveButton(array &$variables): void
    {
        if (!isset($variables['content']['remove_button'])) {
            return;
        }

      // @phpstan-ignore-next-line
        $element = UsbElement::create($variables['content']['remove_button']);
        $element->setIcon(Bootstrap::icon('trash', 'bootstrap', [
        'size' => '16px',
        'alt' => $element->getProperty('value'),
        ]));
        $element->setProperty('icon_position', 'icon_only');
        $element->addClass([
        'btn-sm',
        'media-library-remove-button',
        ]);
        $element->setProperty('weight', static::REMOVE_BUTTON_WEIGHT);
    }

  /**
   * Add icon and button style on edit link.
   *
   * @param array $variables
   *   The preprocessed variables.
   */
    protected function preprocessEditLink(array &$variables): void
    {
        if (!isset($variables['content']['media_edit'])) {
            return;
        }

      // @phpstan-ignore-next-line
        $element = UsbElement::create($variables['content']['media_edit']);
        $element->setIcon(Bootstrap::icon('pencil-fill', 'bootstrap', [
        'size' => '16px',
        ]));
        $element->setProperty('icon_position', 'icon_only');
        $element->addClass([
        'btn',
        'btn-sm',
        'btn-success',
        ]);
        $element->setProperty('weight', static::EDIT_LINK_WEIGHT);
    }
}
