<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Hooks for Views support.
 */
class Views {

  /**
   * Gets dictionary manager service when available.
   */
  protected function getDictionaryManager(): ?object {
    if (!\Drupal::hasService('ps_dictionary.manager')) {
      return NULL;
    }

    return \Drupal::service('ps_dictionary.manager');
  }

  /**
   * Gets feature manager service when available.
   */
  protected function getFeatureManager(): ?object {
    if (!\Drupal::hasService('ps_features.manager')) {
      return NULL;
    }

    return \Drupal::service('ps_features.manager');
  }

  /**
   * Builds UI options payload from dictionary entries metadata.
   *
   * Output shape:
   * - CODE => ['label' => string, 'ui_label' => string, 'icon' => string]
   *
   * @return array<string, array<string, string>>
   *   UI options keyed by dictionary code.
   */
  protected function buildDictionaryUiOptions(string $dictionaryType): array {
    $manager = $this->getDictionaryManager();
    if ($manager === NULL || !method_exists($manager, 'getEntries')) {
      return [];
    }

    $entries = $manager->getEntries($dictionaryType, TRUE);
    $options = [];

    foreach ($entries as $entry) {
      if (!is_object($entry) || !method_exists($entry, 'getCode') || !method_exists($entry, 'getLabel')) {
        continue;
      }

      $code = (string) $entry->getCode();
      if ($code === '') {
        continue;
      }

      $label = (string) $entry->getLabel();
      $icon = '';
      $ui_label = $label;
      $visible = TRUE;

      if (method_exists($entry, 'getMetadataValue')) {
        $icon = (string) $entry->getMetadataValue('icon', '');
        $ui_label = (string) $entry->getMetadataValue('ui_label', $label);
        $visible = (bool) $entry->getMetadataValue('search_ui_visible', TRUE);
      }

      $options[$code] = [
        'label' => $label,
        'ui_label' => $ui_label,
        'icon' => $icon,
        'visible' => $visible ? '1' : '0',
      ];
    }

    return $options;
  }

  /**
   * Resolve the first existing key among candidate form element keys.
   */
  protected function resolveElementKey(array $form, array $candidates): ?string {
    foreach ($candidates as $candidate) {
      if (isset($form[$candidate])) {
        return $candidate;
      }
    }

    return NULL;
  }

  /**
   * Find a direct form child that contains an input with the given #name.
   */
  protected function findChildByInputName(array $form, string $inputName): ?string {
    foreach (Element::children($form) as $child_key) {
      if (!isset($form[$child_key]) || !\is_array($form[$child_key])) {
        continue;
      }

      if ($this->elementContainsInputName($form[$child_key], $inputName)) {
        return $child_key;
      }
    }

    return NULL;
  }

  /**
   * Recursively check whether an element tree contains a matching input #name.
   */
  protected function elementContainsInputName(array $element, string $inputName): bool {
    if (isset($element['#name']) && $element['#name'] === $inputName) {
      return TRUE;
    }

    foreach ($element as $key => $value) {
      if (\is_string($key) && \str_starts_with($key, '#')) {
        continue;
      }

      if (\is_array($value) && $this->elementContainsInputName($value, $inputName)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Build a dropdown panel opening prefix for a filter element.
   *
   * @param string $panelId
   *   CSS/aria identifier for this panel (slug).
   * @param string $label
   *   Visible meta label for the trigger button.
   * @param string|null $summary
   *   Main trigger value/placeholder text.
   */
  protected function buildPanelPrefix(string $panelId, string $label, ?string $summary = NULL): string {
    $id = Html::cleanCssIdentifier($panelId);
    $label = Html::escape($label);
    $summary_text = Html::escape($summary ?? $label);
    return '<div class="ps-filter-panel ps-filter-panel--' . $id . '" data-ps-panel="' . $id . '">'
      . '<div class="ps-filter-panel__trigger" role="button" tabindex="0" '
      . 'aria-expanded="false" aria-haspopup="true" aria-controls="ps-fp-' . $id . '">'
      . '<span class="ps-filter-panel__meta-label">' . $label . '</span>'
      . '<span class="ps-filter-panel__value">' . $summary_text . '</span>'
      . '<span class="ps-filter-panel__arrow" aria-hidden="true"></span>'
      . '</div>'
      . '<div class="ps-filter-panel__content" id="ps-fp-' . $id . '" hidden>';
  }

  /**
   * Build the closing suffix for a dropdown panel.
   */
  protected function buildPanelSuffix(): string {
    $apply = Html::escape((string) new TranslatableMarkup('Show results'));
    return '<div class="ps-filter-panel__footer">'
      . '<span class="btn btn-primary ps-filter-panel__apply">' . $apply . '</span>'
      . '</div>'
      . '</div>'
      . '</div>';
  }

  /**
   * Build the closing suffix for range dropdown panels (surface/price).
   */
  protected function buildRangePanelSuffix(): string {
    $apply = Html::escape((string) new TranslatableMarkup('Show results'));
    $reset = Html::escape((string) new TranslatableMarkup('Delete all'));
    return '<div class="ps-filter-panel__footer ps-filter-panel__footer--range">'
      . '<span class="btn btn-link ps-filter-panel__reset">' . $reset . '</span>'
      . '<span class="btn btn-primary ps-filter-panel__apply">' . $apply . '</span>'
      . '</div>'
      . '</div>'
      . '</div>';
  }

  /**
   * Build a static panel (visual placeholder, no dropdown content).
   */
  protected function buildStaticPanel(string $panelId, string $label, string $summary): string {
    $id = Html::cleanCssIdentifier($panelId);
    $label = Html::escape($label);
    $summary_text = Html::escape($summary);

    return '<div class="ps-filter-panel ps-filter-panel--' . $id . ' ps-filter-panel--static" data-ps-panel="' . $id . '">'
      . '<div class="ps-filter-panel__trigger" role="presentation">'
      . '<span class="ps-filter-panel__meta-label">' . $label . '</span>'
      . '<span class="ps-filter-panel__value">' . $summary_text . '</span>'
      . '<span class="ps-filter-panel__arrow" aria-hidden="true"></span>'
      . '</div>'
      . '</div>';
  }

  /**
   * Builds feature options (code => label) for a filter key.
   *
   * @return array<string, string>
   *   Feature options keyed by feature code.
   */
  protected function buildFeatureOptions(string $filterKey): array {
    $manager = $this->getFeatureManager();
    if ($manager === NULL || !method_exists($manager, 'getFeaturesByGroup')) {
      return [];
    }

    $all_groups = $manager->getFeaturesByGroup();
    if (!is_array($all_groups)) {
      return [];
    }

    $options = [];

    // Accessibility is a pseudo-group spanning multiple real feature groups.
    if ($filterKey === 'accessibility') {
      $accessibility_ids = ['has_elevator', 'highly_flexible'];
      foreach ($all_groups as $group_data) {
        if (!is_array($group_data) || !isset($group_data['features']) || !is_array($group_data['features'])) {
          continue;
        }

        foreach ($group_data['features'] as $code => $feature) {
          if (!in_array($code, $accessibility_ids, TRUE)) {
            continue;
          }
          if (is_object($feature) && method_exists($feature, 'label')) {
            $label = (string) $feature->label();
            if ($code !== '' && $label !== '') {
              $options[$code] = $label;
            }
          }
        }
      }

      return $options;
    }

    if (!isset($all_groups[$filterKey]) || !is_array($all_groups[$filterKey])) {
      return [];
    }

    $group_data = $all_groups[$filterKey];
    if (!isset($group_data['features']) || !is_array($group_data['features'])) {
      return [];
    }

    foreach ($group_data['features'] as $code => $feature) {
      if (is_object($feature) && method_exists($feature, 'label')) {
        $label = (string) $feature->label();
        if ($code !== '' && $label !== '') {
          $options[$code] = $label;
        }
      }
    }

    return $options;
  }

  /**
   * Builds a feature checkboxes element using feature codes as submitted values.
   *
   * @return array<string, mixed>
   *   Form API element.
   */
  protected function buildFeatureCheckboxesElement(string $filterKey, string $title): array {
    $options = $this->buildFeatureOptions($filterKey);
    $selected_values = array_values(array_filter(array_map(
      static fn(mixed $value): string => trim((string) $value),
      (array) \Drupal::request()->query->all($filterKey)
    )));

    $element = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup($title),
      '#attributes' => [
        'class' => [
          'ps-offer-feature-checkboxes',
          'ps-offer-feature-checkboxes--' . Html::cleanCssIdentifier($filterKey),
        ],
      ],
    ];

    $index = 0;
    foreach ($options as $code => $label) {
      $index++;
      $element['item_' . $index] = [
        '#type' => 'checkbox',
        '#title' => $label,
        '#return_value' => $code,
        '#default_value' => in_array($code, $selected_values, TRUE),
        '#name' => $filterKey . '[]',
      ];
    }

    return $element;
  }

  /**
   * Builds a boolean toggle element (single checkbox).
   *
   * @return array<string, mixed>
   *   Form API checkbox element.
   */
  protected function buildBooleanToggleElement(string $name, string $title, array $queryNames = []): array {
    if ($queryNames === []) {
      $queryNames = [$name];
    }

    $raw_query = (string) \Drupal::request()->server->get('QUERY_STRING', '');
    $query = [];
    if ($raw_query !== '') {
      parse_str($raw_query, $query);
    }

    $is_checked = FALSE;
    foreach ($queryNames as $queryName) {
      if (!array_key_exists($queryName, $query)) {
        continue;
      }

      $raw = $query[$queryName];
      $values = is_array($raw) ? $raw : [$raw];
      foreach ($values as $value) {
        if (in_array(strtolower(trim((string) $value)), ['1', 'true', 'on'], TRUE)) {
          $is_checked = TRUE;
          break 2;
        }
      }
    }

    return [
      '#type' => 'checkbox',
      '#title' => new TranslatableMarkup($title),
      '#name' => $name,
      '#return_value' => '1',
      '#default_value' => $is_checked,
      '#attributes' => [
        'class' => ['ps-offer-boolean-toggle'],
      ],
    ];
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_views_exposed_form_alter')]
  public function viewsExposedFormAlter(array &$form, FormStateInterface $formState, string $form_id): void {
    $form_dom_id = (string) ($form['#id'] ?? '');
    $is_offer_search_form = str_contains($form_dom_id, 'views-exposed-form-ps-offer-search-page-1');

    if ($is_offer_search_form) {
      $form['#attributes']['class'][] = 'ps-offer-search-bar';

      // -- Resolve filter keys. -----------------------------------------------
      $keys_key = $this->resolveElementKey($form, ['keys', 'search_api_fulltext']);
      $property_key = $this->resolveElementKey($form, ['property_type', 'field_property_type']);
      $location_key = $this->resolveElementKey($form, ['location']);
      $surface_key = $this->resolveElementKey($form, ['surface', 'ps_offer_surface_main_value'])
        ?? $this->findChildByInputName($form, 'surface[min]');
      $price_key = $this->resolveElementKey($form, ['price', 'ps_offer_price_normalized_main'])
        ?? $this->findChildByInputName($form, 'price[min]');
      $transaction_key = $this->resolveElementKey($form, ['transaction_type', 'field_transaction_types']);
      $reference_key = $this->resolveElementKey($form, ['reference', 'field_reference']);
      $accessibility_key = $this->resolveElementKey($form, ['accessibility', 'ps_offer_feature_accessibility']);
      $equipments_key = $this->resolveElementKey($form, ['equipments', 'ps_offer_feature_equipments']);
      $services_key = $this->resolveElementKey($form, ['services', 'ps_offer_feature_services']);
      $building_condition_key = $this->resolveElementKey($form, ['building_condition', 'ps_offer_building_condition']);
      $nearby_transport_key = $this->resolveElementKey($form, ['nearby_transport']);
      $immersive_tour_key = $this->resolveElementKey($form, ['immersive_tour', 'ps_offer_has_virtual_tour']);
      $video_key = $this->resolveElementKey($form, ['video', 'ps_offer_has_video']);

      $surface_values = (array) \Drupal::request()->query->all('surface');
      $price_values = (array) \Drupal::request()->query->all('price');

      // -- Hide the keys (fulltext) filter — not in primary bar. ---------------
      if ($keys_key !== NULL) {
        $form[$keys_key]['#access'] = FALSE;
      }

      // -- Property type: expose options array for JS icon-grid, wrap panel. --
      if ($property_key !== NULL) {
        // Use a dedicated query key to avoid collisions with Views exposed
        // filter internals on AJAX requests.
        $form[$property_key]['#name'] = 'ps_property_type';

        $options = $this->buildDictionaryUiOptions('property_type');
        if (empty($options) && isset($form[$property_key]['#options']) && is_array($form[$property_key]['#options'])) {
          $options = [];
          foreach ($form[$property_key]['#options'] as $code => $label) {
            if ((string) $code === 'All') {
              continue;
            }
            $options[(string) $code] = [
              'label' => (string) $label,
              'ui_label' => (string) $label,
              'icon' => '',
              'visible' => '1',
            ];
          }
        }

        if (!empty($options)) {
          $form[$property_key]['#attributes']['data-ps-options'] = json_encode($options, JSON_UNESCAPED_UNICODE);
        }
        $form[$property_key]['#attributes']['data-ps-filter'] = 'property-type';
        $form[$property_key]['#prefix'] = $this->buildPanelPrefix(
          'property-type',
          (string) new TranslatableMarkup('Property type'),
          (string) new TranslatableMarkup('Offices for rent')
        );
        $form[$property_key]['#suffix'] = $this->buildPanelSuffix();
        $form[$property_key]['#weight'] = 1;
      }

      if ($transaction_key !== NULL) {
        // Use a dedicated query key to avoid collisions with Views exposed
        // filter internals on AJAX requests.
        $form[$transaction_key]['#name'] = 'ps_transaction_type';

        $transaction_options = $this->buildDictionaryUiOptions('transaction_type');
        if (!empty($transaction_options)) {
          $form[$transaction_key]['#attributes']['data-ps-options'] = json_encode($transaction_options, JSON_UNESCAPED_UNICODE);
        }
        $form[$transaction_key]['#attributes']['data-ps-filter'] = 'transaction-type';
      }

      // -- Location filter: plain text input with panel wrapper. ---------------
      if ($location_key !== NULL) {
        $form[$location_key]['#attributes']['data-ps-filter'] = 'location';
        $form[$location_key]['#attributes']['autocomplete'] = 'off';
        $form[$location_key]['#attributes']['placeholder'] = (string) new TranslatableMarkup('City, district or zip code');
        $form[$location_key]['#prefix'] = $this->buildPanelPrefix(
          'location',
          (string) new TranslatableMarkup('Location(s)'),
          (string) new TranslatableMarkup('City, district or zip code')
        );
        $form[$location_key]['#suffix'] = $this->buildPanelSuffix();
        $form[$location_key]['#weight'] = 2;
      }

      // -- Surface: range fieldset with panel wrapper. -------------------------
      if ($surface_key !== NULL) {
        $form[$surface_key]['#attributes']['data-ps-filter'] = 'surface';
        $form[$surface_key]['#prefix'] = $this->buildPanelPrefix(
          'surface',
          (string) new TranslatableMarkup('Surface (m²)'),
          (string) new TranslatableMarkup('Surface')
        );
        $form[$surface_key]['#suffix'] = $this->buildRangePanelSuffix();
        $form[$surface_key]['#weight'] = 3;
      }
      else {
        $form['ps_surface_fallback'] = [
          '#type' => 'fieldset',
          '#title' => new TranslatableMarkup('Surface (m²)'),
          '#attributes' => [
            'data-ps-filter' => 'surface',
          ],
          '#prefix' => $this->buildPanelPrefix(
            'surface',
            (string) new TranslatableMarkup('Surface (m²)'),
            (string) new TranslatableMarkup('Surface')
          ),
          '#suffix' => $this->buildRangePanelSuffix(),
          '#weight' => 3,
        ];

        $form['ps_surface_fallback']['min'] = [
          '#type' => 'number',
          '#title' => new TranslatableMarkup('Surface min (m²)'),
          '#name' => 'surface[min]',
          '#default_value' => isset($surface_values['min']) && is_scalar($surface_values['min']) ? (string) $surface_values['min'] : '',
          '#min' => 0,
          '#step' => 1,
          '#size' => 8,
          '#attributes' => [
            'inputmode' => 'numeric',
          ],
        ];

        $form['ps_surface_fallback']['max'] = [
          '#type' => 'number',
          '#title' => new TranslatableMarkup('Surface max (m²)'),
          '#name' => 'surface[max]',
          '#default_value' => isset($surface_values['max']) && is_scalar($surface_values['max']) ? (string) $surface_values['max'] : '',
          '#min' => 0,
          '#step' => 1,
          '#size' => 8,
          '#attributes' => [
            'inputmode' => 'numeric',
          ],
        ];
      }

      // -- Price: range fieldset with panel wrapper. ---------------------------
      if ($price_key !== NULL) {
        $form[$price_key]['#attributes']['data-ps-filter'] = 'price';
        $form[$price_key]['#prefix'] = $this->buildPanelPrefix(
          'price',
          (string) new TranslatableMarkup('Price'),
          (string) new TranslatableMarkup('Price')
        );
        $form[$price_key]['#suffix'] = $this->buildRangePanelSuffix();
        $form[$price_key]['#weight'] = 4;
      }
      else {
        $form['ps_price_fallback'] = [
          '#type' => 'fieldset',
          '#title' => new TranslatableMarkup('Price'),
          '#attributes' => [
            'data-ps-filter' => 'price',
          ],
          '#prefix' => $this->buildPanelPrefix(
            'price',
            (string) new TranslatableMarkup('Price'),
            (string) new TranslatableMarkup('Price')
          ),
          '#suffix' => $this->buildRangePanelSuffix(),
          '#weight' => 4,
        ];

        $form['ps_price_fallback']['min'] = [
          '#type' => 'number',
          '#title' => new TranslatableMarkup('Price min (€)'),
          '#name' => 'price[min]',
          '#default_value' => isset($price_values['min']) && is_scalar($price_values['min']) ? (string) $price_values['min'] : '',
          '#min' => 0,
          '#step' => 1,
          '#size' => 8,
          '#attributes' => [
            'inputmode' => 'numeric',
          ],
        ];

        $form['ps_price_fallback']['max'] = [
          '#type' => 'number',
          '#title' => new TranslatableMarkup('Price max (€)'),
          '#name' => 'price[max]',
          '#default_value' => isset($price_values['max']) && is_scalar($price_values['max']) ? (string) $price_values['max'] : '',
          '#min' => 0,
          '#step' => 1,
          '#size' => 8,
          '#attributes' => [
            'inputmode' => 'numeric',
          ],
        ];
      }

      // -- Sort: Add a sort dropdown to the filter bar. ----------------------
      $sort_by_key = $this->resolveElementKey($form, ['sort_by']);
      if ($sort_by_key === NULL) {
        // Manually create sort selector.
        $form['sort_by'] = [
          '#type' => 'select',
          '#title' => new TranslatableMarkup('Sort by'),
          '#name' => 'sort_by',
          '#options' => [
            'ps_offer_surface_main_value' => new TranslatableMarkup('Increasing surface'),
          ],
          '#default_value' => (string) (\Drupal::request()->query->get('sort_by') ?? ''),
          '#weight' => 4,
          '#wrapper_attributes' => [
            'class' => ['ps-filter-panel'],
          ],
          '#attributes' => [
            'class' => ['form-select', 'ps-filter-panel__input'],
          ],
        ];
      }

      // -- More-filters trigger button (offcanvas opener). ---------------------
      $moreLabel = Html::escape((string) new TranslatableMarkup('More filters'));
      $form['ps_more_filters_trigger'] = [
        '#markup' => '<div class="ps-filter-panel__trigger ps-filter-panel__trigger--more" role="button" tabindex="0"'
          . ' data-bs-toggle="offcanvas" data-bs-target="#ps-more-filters"'
          . ' aria-controls="ps-more-filters">'
          . '<span class="ps-filter-panel__label">' . $moreLabel . '</span>'
          . '<span class="ps-filter-panel__arrow" aria-hidden="true"></span>'
          . '</div>',
        '#weight' => 5,
      ];

      // -- Offcanvas container (inside the form so secondary inputs submit). ---
      $offcanvasTitle = Html::escape((string) new TranslatableMarkup('More filters'));
      $closeLabel = Html::escape((string) new TranslatableMarkup('Close'));
      $deleteAll = Html::escape((string) new TranslatableMarkup('Delete all'));
      $showResults = Html::escape((string) new TranslatableMarkup('Show results'));

      $form['ps_more_filters'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['offcanvas', 'offcanvas-end', 'ps-offer-more-filters'],
          'id' => 'ps-more-filters',
          'tabindex' => '-1',
          'data-bs-scroll' => 'true',
          'data-bs-backdrop' => 'false',
          'aria-labelledby' => 'ps-more-filters-title',
        ],
        '#weight' => 90,
      ];

      $form['ps_more_filters']['header'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['offcanvas-header']],
      ];

      $form['ps_more_filters']['header']['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h5',
        '#value' => $offcanvasTitle,
        '#attributes' => [
          'class' => ['offcanvas-title'],
          'id' => 'ps-more-filters-title',
        ],
      ];

      $form['ps_more_filters']['header']['close'] = [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#value' => '',
        '#attributes' => [
          'type' => 'button',
          'class' => ['btn-close'],
          'data-bs-dismiss' => 'offcanvas',
          'aria-label' => $closeLabel,
        ],
      ];

      $form['ps_more_filters']['body'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['offcanvas-body', 'ps-offer-more-filters__body']],
      ];

      // Transaction type: goes into offcanvas.
      if ($transaction_key !== NULL) {
        $form['ps_more_filters']['body']['transaction'] = $form[$transaction_key];
        $form['ps_more_filters']['body']['transaction']['#name'] = 'ps_transaction_type';
        $form['ps_more_filters']['body']['transaction']['#weight'] = 1;
        unset($form[$transaction_key]);
      }

      // Ad reference: goes into offcanvas.
      if ($reference_key !== NULL) {
        $form['ps_more_filters']['body']['reference'] = $form[$reference_key];
        $form['ps_more_filters']['body']['reference']['#type'] = 'hidden';
        $form['ps_more_filters']['body']['reference']['#weight'] = 2;
        unset($form[$reference_key]);
      }

      // -- Feature filters: update options and move to offcanvas. ----
      if ($accessibility_key !== NULL) {
        $form['ps_more_filters']['body']['accessibility'] = $this->buildFeatureCheckboxesElement('accessibility', 'Accessibility');
        $form['ps_more_filters']['body']['accessibility']['#weight'] = 3;
        unset($form[$accessibility_key]);
      }

      if ($equipments_key !== NULL) {
        $form['ps_more_filters']['body']['equipments'] = $this->buildFeatureCheckboxesElement('equipments', 'Equipments');
        $form['ps_more_filters']['body']['equipments']['#weight'] = 4;
        unset($form[$equipments_key]);
      }

      if ($services_key !== NULL) {
        $form['ps_more_filters']['body']['services'] = $this->buildFeatureCheckboxesElement('services', 'Services');
        $form['ps_more_filters']['body']['services']['#weight'] = 5;
        unset($form[$services_key]);
      }

      if ($building_condition_key !== NULL) {
        $form['ps_more_filters']['body']['building_condition'] = $this->buildFeatureCheckboxesElement('building_condition', 'Building type/condition');
        $form['ps_more_filters']['body']['building_condition']['#weight'] = 6;
        unset($form[$building_condition_key]);
      }

      if ($nearby_transport_key !== NULL) {
        $form['ps_more_filters']['body']['nearby_transport'] = $form[$nearby_transport_key];
        $form['ps_more_filters']['body']['nearby_transport']['#type'] = 'hidden';
        $form['ps_more_filters']['body']['nearby_transport']['#weight'] = 7;
        unset($form[$nearby_transport_key]);
      }

      if ($immersive_tour_key !== NULL) {
        $form['ps_more_filters']['body']['immersive_tour'] = $this->buildBooleanToggleElement('immersive_tour_enabled', 'Immersive tour', ['immersive_tour_enabled']);
        $form['ps_more_filters']['body']['immersive_tour']['#weight'] = 8;
        unset($form[$immersive_tour_key]);
      }

      if ($video_key !== NULL) {
        $form['ps_more_filters']['body']['video'] = $this->buildBooleanToggleElement('video_enabled', 'Video', ['video_enabled']);
        $form['ps_more_filters']['body']['video']['#weight'] = 9;
        unset($form[$video_key]);
      }

      $form['ps_more_filters']['footer'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-offer-more-filters__footer']],
      ];

      $form['ps_more_filters']['footer']['reset'] = [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#value' => $deleteAll,
        '#attributes' => [
          'type' => 'reset',
          'class' => ['btn', 'btn-link', 'ps-offer-more-filters__reset'],
        ],
      ];

      $form['ps_more_filters']['footer']['apply'] = [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#value' => $showResults,
        '#attributes' => [
          'type' => 'button',
          'class' => ['btn', 'btn-primary', 'ps-offer-more-filters__apply'],
        ],
      ];

      // -- Actions: hide reset, style submit. ----------------------------------
      if (isset($form['actions'])) {
        $form['actions']['#weight'] = 80;
        $form['actions']['#attributes']['class'][] = 'ps-offer-search-bar__actions';
        if (isset($form['actions']['submit'])) {
          $form['actions']['submit']['#value'] = new TranslatableMarkup('Show results');
          $form['actions']['submit']['#attributes']['class'][] = 'btn';
          $form['actions']['submit']['#attributes']['class'][] = 'btn-primary';
          $form['actions']['submit']['#attributes']['class'][] = 'ps-offer-search-bar__submit';
        }
        if (isset($form['actions']['reset'])) {
          $form['actions']['reset']['#access'] = FALSE;
        }
      }

      return;
    }

    // -- Generic Views exposed form styling. -----------------------------------
    $form['#attributes']['class'][] = 'row';
    $form['#attributes']['class'][] = 'row-cols-auto';
    $form['#attributes']['class'][] = 'align-items-end';

    if (isset($form['actions'])) {
      $form['actions']['#attributes']['class'][] = 'mb-3';
    }
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
