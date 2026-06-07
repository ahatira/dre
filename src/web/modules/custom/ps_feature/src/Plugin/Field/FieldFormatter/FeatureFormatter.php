<?php

namespace Drupal\ps_feature\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_feature\Service\FeatureGroupIconResolver;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'feature_default' formatter.
 *
 * @FieldFormatter(
 *   id = "feature_default",
 *   label = @Translation("Default feature display"),
 *   field_types = {
 *     "feature"
 *   }
 * )
 */
class FeatureFormatter extends FormatterBase {

  /**
   * The feature type plugin manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * Resolves feature group heading icons.
   *
   * @var \Drupal\ps_feature\Service\FeatureGroupIconResolver
   */
  protected FeatureGroupIconResolver $featureGroupIconResolver;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->featureTypeManager = $container->get('plugin.manager.feature_type');
    $instance->featureGroupIconResolver = $container->get('ps_feature.feature_group_icon_resolver');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_label' => TRUE,
      'show_group' => TRUE,
      'format_style' => 'default',
      'hide_disabled_flags' => TRUE,
      'group_order' => '',
      'group_filter' => '',
      'column_threshold' => 5,
      'column_rows' => 5,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature label'),
      '#default_value' => $this->getSetting('show_label'),
    ];

    $elements['show_group'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature group'),
      '#default_value' => $this->getSetting('show_group'),
    ];

    $elements['format_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Format style'),
      '#options' => [
        'default' => $this->t('Default'),
        'compact' => $this->t('Compact'),
        'detailed' => $this->t('Detailed'),
        'grouped' => $this->t('Grouped'),
      ],
      '#default_value' => $this->getSetting('format_style'),
    ];

    $group_storage = \Drupal::entityTypeManager()->getStorage('fb_feature_group');
    $group_entities = $group_storage->loadMultiple();
    usort($group_entities, static function ($a, $b): int {
      $a_weight = method_exists($a, 'getWeight') ? $a->getWeight() : 0;
      $b_weight = method_exists($b, 'getWeight') ? $b->getWeight() : 0;
      if ($a_weight === $b_weight) {
        return strcasecmp((string) $a->label(), (string) $b->label());
      }
      return $a_weight <=> $b_weight;
    });
    $group_help_lines = [];
    foreach ($group_entities as $group_entity) {
      $group_help_lines[] = $group_entity->id() . ' (' . $group_entity->label() . ')';
    }

    $group_filter_options = ['' => $this->t('- All groups -')];
    foreach ($group_entities as $group_entity) {
      $group_filter_options[$group_entity->id()] = $group_entity->label();
    }

    $elements['group_filter'] = [
      '#type' => 'select',
      '#title' => $this->t('Filter by group'),
      '#description' => $this->t('When set, only features from this group are rendered.'),
      '#options' => $group_filter_options,
      '#default_value' => (string) $this->getSetting('group_filter'),
    ];

    $elements['hide_disabled_flags'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide disabled flag features'),
      '#description' => $this->t('When enabled, flag features with a disabled payload are not rendered.'),
      '#default_value' => $this->getSetting('hide_disabled_flags'),
    ];

    $elements['column_threshold'] = [
      '#type' => 'number',
      '#title' => $this->t('Multi-column threshold'),
      '#description' => $this->t('When a group has more features than this number, desktop layout uses multiple columns.'),
      '#default_value' => (int) $this->getSetting('column_threshold'),
      '#min' => 1,
      '#step' => 1,
      '#states' => [
        'visible' => [
          ':input[name$="[settings_edit_form][settings][format_style]"]' => ['value' => 'grouped'],
        ],
      ],
    ];

    $elements['column_rows'] = [
      '#type' => 'number',
      '#title' => $this->t('Rows per column (desktop)'),
      '#description' => $this->t('Maximum number of feature lines per column before starting a new column on desktop.'),
      '#default_value' => (int) $this->getSetting('column_rows'),
      '#min' => 1,
      '#step' => 1,
      '#states' => [
        'visible' => [
          ':input[name$="[settings_edit_form][settings][format_style]"]' => ['value' => 'grouped'],
        ],
      ],
    ];

    // Build draggable table for group order.
    $group_order = $this->parseGroupOrderSetting((string) $this->getSetting('group_order'));
    $rows = [];
    $used = [];
    // Add groups in saved order first.
    foreach ($group_order as $id) {
      foreach ($group_entities as $group_entity) {
        if ($group_entity->id() === $id) {
          $rows[] = [
            'id' => $group_entity->id(),
            'label' => $group_entity->label(),
          ];
          $used[] = $id;
        }
      }
    }
    // Add remaining groups.
    foreach ($group_entities as $group_entity) {
      if (!in_array($group_entity->id(), $used, TRUE)) {
        $rows[] = [
          'id' => $group_entity->id(),
          'label' => $group_entity->label(),
        ];
      }
    }

    $elements['group_order'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Group label'),
        $this->t('Group ID'),
        $this->t('Order'),
      ],
      '#empty' => $this->t('No groups available.'),
      '#attributes' => ['id' => 'ps-feature-group-order-table'],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'group-order-weight',
        ],
      ],
      '#states' => [
        'visible' => [
          ':input[name$="[settings_edit_form][settings][format_style]"]' => ['value' => 'grouped'],
        ],
      ],
    ];
    foreach ($rows as $delta => $row) {
      $elements['group_order'][$row['id']]['#attributes']['class'][] = 'draggable';
      $elements['group_order'][$row['id']]['label'] = [
        '#markup' => $row['label'],
      ];
      $elements['group_order'][$row['id']]['id'] = [
        '#markup' => $row['id'],
      ];
      $elements['group_order'][$row['id']]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Order'),
        '#title_display' => 'invisible',
        '#default_value' => $delta,
        '#attributes' => ['class' => ['group-order-weight']],
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormValidate(array &$form, FormStateInterface $form_state) {
    parent::settingsFormValidate($form, $form_state);
    // Nothing needed, handled in submit.
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormSubmit(array $form, FormStateInterface $form_state) {
    parent::settingsFormSubmit($form, $form_state);
    // Save group order as a comma-separated list of IDs.
    $values = $form_state->getValue(['settings_edit_form', 'settings', 'group_order']);
    if (is_array($values)) {
      // Sort by weight.
      uasort($values, function ($a, $b) {
        return ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0);
      });
      $ids = array_keys($values);
      $form_state->setValue(['settings_edit_form', 'settings', 'group_order'], implode("\n", $ids));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    
    $show_label = $this->getSetting('show_label');
    $show_group = $this->getSetting('show_group');
    $format_style = $this->getSetting('format_style');
    $hide_disabled_flags = $this->getSetting('hide_disabled_flags');
    $group_order = $this->parseGroupOrderSetting((string) $this->getSetting('group_order'));

    $summary[] = $this->t('Show label: @value', ['@value' => $show_label ? $this->t('Yes') : $this->t('No')]);
    $summary[] = $this->t('Show group: @value', ['@value' => $show_group ? $this->t('Yes') : $this->t('No')]);
    $summary[] = $this->t('Style: @style', ['@style' => $format_style]);
    $summary[] = $this->t('Hide disabled flags: @value', ['@value' => $hide_disabled_flags ? $this->t('Yes') : $this->t('No')]);
    if ($group_order) {
      $summary[] = $this->t('Grouped order: @order', ['@order' => implode(', ', $group_order)]);
    }
    else {
      $summary[] = $this->t('Grouped order: automatic');
    }

    $group_filter = (string) $this->getSetting('group_filter');
    if ($group_filter !== '') {
      $summary[] = $this->t('Group filter: @group', ['@group' => $group_filter]);
    }

    if ($format_style === 'grouped') {
      $summary[] = $this->t('Multi-column threshold: @count', ['@count' => (int) $this->getSetting('column_threshold')]);
      $summary[] = $this->t('Rows per column: @count', ['@count' => (int) $this->getSetting('column_rows')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    if ($this->getSetting('format_style') === 'grouped') {
      if (empty($langcode)) {
        $langcode = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
      }
      // Avoid cache-only render arrays: Layout Builder FieldBlock treats them
      // as non-empty and still outputs a block wrapper.
      if ($this->viewElements($items, $langcode) === []) {
        return [];
      }
    }

    return parent::view($items, $langcode);
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $show_label = $this->getSetting('show_label');
    $show_group = $this->getSetting('show_group');
    $format_style = $this->getSetting('format_style');
    $hide_disabled_flags = $this->getSetting('hide_disabled_flags');
    $group_order = $this->parseGroupOrderSetting((string) $this->getSetting('group_order'));
    $group_filter = (string) $this->getSetting('group_filter');

    if ($format_style === 'grouped') {
      $grouped = $this->buildGroupedElements(
        $items,
        $show_label,
        $hide_disabled_flags,
        $group_order,
        $group_filter,
        (int) $this->getSetting('column_threshold'),
        (int) $this->getSetting('column_rows'),
      );
      if ($grouped === []) {
        return [];
      }

      return [
        0 => $grouped,
      ];
    }

    $elements = [];

    foreach ($items as $delta => $item) {
      $feature_definition = $item->getFeatureDefinition();
      
      if (!$feature_definition) {
        continue;
      }

      if ($group_filter !== '' && $feature_definition->getGroup() !== $group_filter) {
        continue;
      }

      $payload = $item->getPayloadArray();
      if ($this->shouldSkipFeature($feature_definition->getTypeDriver(), $payload, $hide_disabled_flags)) {
        continue;
      }
      $type = $feature_definition->getTypeDriver();
      
      try {
        $plugin = $this->featureTypeManager->createInstance($type);
        $formatted_value = $plugin->format($payload);
        if ($type === 'flag') {
          // Flags are implicit presence indicators: label only on display.
          $formatted_value = '';
        }
      }
      catch (\Exception $e) {
        $formatted_value = $this->t('Error formatting feature: @error', ['@error' => $e->getMessage()]);
      }

      // Build the render array based on format style.
      $build = $this->buildItemElement($feature_definition, $formatted_value, $show_label, $show_group, $format_style);

      $elements[$delta] = $build;
    }

    return $elements;
  }

  /**
   * Builds the render array for a single feature item.
   */
  protected function buildItemElement($feature_definition, string $formatted_value, bool $show_label, bool $show_group, string $format_style): array {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['feature-default']],
    ];

    if ($format_style === 'compact') {
      return [
        '#type' => 'container',
        '#attributes' => ['class' => ['feature-compact']],
        'value' => ['#markup' => $formatted_value],
      ];
    }

    if ($format_style === 'detailed') {
      $build['#attributes']['class'] = ['feature-detailed'];
    }

    if ($show_label) {
      $build['label'] = [
        '#type' => 'html_tag',
        '#tag' => 'strong',
        '#value' => $feature_definition->label(),
      ];
      if ($formatted_value !== '') {
        $build['separator'] = ['#markup' => ': '];
      }
    }

    if ($formatted_value !== '') {
      $build['value'] = ['#markup' => $formatted_value];
    }

    if ($show_group) {
      $group = \Drupal::entityTypeManager()
        ->getStorage('fb_feature_group')
        ->load($feature_definition->getGroup());

      if ($group) {
        $build['group'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => ' (' . $group->label() . ')',
          '#attributes' => ['class' => ['feature-group-badge']],
        ];
      }
    }

    if ($format_style === 'detailed') {
      $build['description'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $feature_definition->getDescription(),
        '#attributes' => ['class' => ['feature-description']],
      ];
    }

    return $build;
  }

  /**
   * Determines whether a feature should be skipped for display.
   */
  protected function shouldSkipFeature(string $type_driver, array $payload, bool $hide_disabled_flags): bool {
    if (!$hide_disabled_flags || $type_driver !== 'flag') {
      return FALSE;
    }

    if (array_key_exists('present', $payload)) {
      return (bool) $payload['present'] === FALSE;
    }
    if (array_key_exists('presence', $payload)) {
      return (bool) $payload['presence'] === FALSE;
    }

    return FALSE;
  }

  /**
   * Parses the formatter setting that lists ordered group IDs.
   */
  protected function parseGroupOrderSetting(string $group_order): array {
    if ($group_order === '') {
      return [];
    }

    $parts = preg_split('/[\r\n,]+/', $group_order) ?: [];
    $ordered = [];
    foreach ($parts as $part) {
      $id = trim($part);
      if ($id === '' || in_array($id, $ordered, TRUE)) {
        continue;
      }
      $ordered[] = $id;
    }
    return $ordered;
  }

  /**
   * Builds grouped render arrays by feature group.
   */
  protected function buildGroupedElements(FieldItemListInterface $items, bool $show_label, bool $hide_disabled_flags, array $group_order, string $group_filter = '', int $column_threshold = 5, int $column_rows = 5): array {
    $grouped_elements = [];
    $group_storage = \Drupal::entityTypeManager()->getStorage('fb_feature_group');
    $group_order_positions = array_flip($group_order);
    $column_threshold = max(1, $column_threshold);
    $column_rows = max(1, $column_rows);

    foreach ($items as $delta => $item) {
      $feature_definition = $item->getFeatureDefinition();
      if (!$feature_definition) {
        continue;
      }

      if ($group_filter !== '' && $feature_definition->getGroup() !== $group_filter) {
        continue;
      }

      $payload = $item->getPayloadArray();
      if ($this->shouldSkipFeature($feature_definition->getTypeDriver(), $payload, $hide_disabled_flags)) {
        continue;
      }

      try {
        $type = $feature_definition->getTypeDriver();
        $plugin = $this->featureTypeManager->createInstance($type);
        $formatted_value = $plugin->format($payload);
        if ($type === 'flag') {
          // Flags are implicit presence indicators: label only on display.
          $formatted_value = '';
        }
      }
      catch (\Exception $e) {
        $formatted_value = $this->t('Error formatting feature: @error', ['@error' => $e->getMessage()]);
      }

      $group_id = $feature_definition->getGroup() ?: '_other';
      $group = $group_storage->load($feature_definition->getGroup());
      $group_label = $group ? $group->label() : $this->t('Other');
      $group_weight = $group && method_exists($group, 'getWeight') ? $group->getWeight() : PHP_INT_MAX;
      $group_key = $group_id === '_other' ? '_other' : Html::getClass((string) $group_id);

      if (!isset($grouped_elements[$group_key])) {
        $grouped_elements[$group_key] = [
          'group_id' => $group_id,
          'group_label' => (string) $group_label,
          'group_weight' => (int) $group_weight,
          'order_position' => $group_order_positions[$group_id] ?? PHP_INT_MAX,
          'items' => [],
        ];
      }

      $grouped_elements[$group_key]['items']['item_' . $delta] = $this->buildItemElement($feature_definition, $formatted_value, $show_label, FALSE, 'default');
    }

    uasort($grouped_elements, static function (array $a, array $b): int {
      if ($a['order_position'] !== $b['order_position']) {
        return $a['order_position'] <=> $b['order_position'];
      }
      if ($a['group_weight'] !== $b['group_weight']) {
        return $a['group_weight'] <=> $b['group_weight'];
      }
      return strcasecmp($a['group_label'], $b['group_label']);
    });

    if (!$grouped_elements) {
      return [];
    }

    $elements = [
      '#type' => 'container',
      '#attributes' => ['class' => ['feature-grouped-list']],
    ];

    foreach ($grouped_elements as $group_key => $group_element) {
      $elements[$group_key] = $this->buildGroupedGroupRenderArray(
        $group_element,
        $column_threshold,
        $column_rows,
      );
    }

    return $elements;
  }

  /**
   * Builds the render array for one grouped feature section.
   */
  protected function buildGroupedGroupRenderArray(array $group_element, int $column_threshold, int $column_rows): array {
    $item_count = count($group_element['items']);
    $items_attributes = [
      'class' => ['feature-grouped-items'],
    ];

    if ($item_count > $column_threshold) {
      $items_attributes['class'][] = 'feature-grouped-items--columns';
      $items_attributes['data-column-rows'] = (string) $column_rows;
      $items_attributes['style'] = 'grid-template-rows: repeat(' . $column_rows . ', auto);';
    }

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['feature-grouped-group'],
        'data-feature-group' => $group_element['group_id'],
      ],
      '#cache' => [
        'tags' => $this->featureGroupIconResolver->getCacheTagsForGroup((string) $group_element['group_id']),
      ],
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['feature-grouped-title']],
        'icon' => $this->featureGroupIconResolver->buildRenderable((string) $group_element['group_id']),
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#value' => $group_element['group_label'],
          '#attributes' => ['class' => ['feature-grouped-title__label']],
        ],
      ],
      'items' => [
        '#type' => 'container',
        '#attributes' => $items_attributes,
      ] + $group_element['items'],
    ];
  }

}
