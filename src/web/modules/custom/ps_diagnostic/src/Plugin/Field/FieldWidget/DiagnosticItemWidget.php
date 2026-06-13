<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_diagnostic\Service\DiagnosticTypeOptionsProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Widget for diagnostic_item.
 */
#[FieldWidget(
  id: 'diagnostic_item_default',
  label: new TranslatableMarkup('Diagnostic item editor'),
  field_types: ['diagnostic_item'],
)]
final class DiagnosticItemWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly DiagnosticTypeOptionsProvider $typeOptionsProvider,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('ps_diagnostic.type_options'),
    );
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta] ?? NULL;
    $current = $item ? $item->getValue() : [];

    $type_options = $this->typeOptionsProvider->getOptions();
    $configured_types = $this->getConfiguredTypes();
    if ($configured_types !== []) {
      $type_options = array_intersect_key($type_options, array_flip($configured_types));
    }

    $single_configured_type = count($configured_types) === 1 ? $configured_types[0] : '';
    if ($single_configured_type !== '' && ($current['diagnostic_type'] ?? '') === '') {
      $current['diagnostic_type'] = $single_configured_type;
    }

    $selected_type = (string) ($current['diagnostic_type'] ?? '');
    $type_id_for_widget = $selected_type !== '' ? $selected_type : $single_configured_type;
    $type = $type_id_for_widget !== '' ? $this->typeOptionsProvider->getType($type_id_for_widget) : NULL;
    $classes = $this->normalizeClasses($type?->getClasses() ?? []);
    $suggested_class = $this->resolveSuggestedClass((string) ($current['value'] ?? ''), $classes);
    $current_class = trim((string) ($current['class'] ?? ''));
    $class_for_preview = $current_class !== '' ? $current_class : ($suggested_class ?? '');
    $class_options = $selected_type !== ''
      ? $this->typeOptionsProvider->getClassOptions($selected_type)
      : $this->typeOptionsProvider->getClassOptionsForTypes($configured_types !== [] ? $configured_types : array_keys($type_options));

    $field_name = $items->getName();
    $no_classification_input_name = sprintf('%s[%d][no_classification]', $field_name, $delta);
    $non_applicable_input_name = sprintf('%s[%d][non_applicable]', $field_name, $delta);
    $visibility_states = [
      ':input[name="' . $no_classification_input_name . '"]' => ['checked' => FALSE],
      ':input[name="' . $non_applicable_input_name . '"]' => ['checked' => FALSE],
    ];

    if ($single_configured_type !== '') {
      $element['diagnostic_type'] = [
        '#type' => 'value',
        '#value' => $single_configured_type,
      ];
    }
    else {
      $element['diagnostic_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Diagnostic type'),
        '#options' => ['' => $this->t('- Select -')] + $type_options,
        '#default_value' => $current['diagnostic_type'] ?? '',
        '#required' => FALSE,
        '#description' => $this->t('Choose the diagnostic family before entering the class or value.'),
      ];
    }

    $element['field_title'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => Html::escape((string) $this->fieldDefinition->getLabel()),
      '#attributes' => ['class' => ['ps-diagnostic-widget__field-title']],
      '#weight' => 1,
    ];

    $element['intro'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-diagnostic-widget__intro-wrap'],
      ],
      '#weight' => 5,
      '#states' => [
        'visible' => $visibility_states,
      ],
    ];
    $element['intro']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => (string) $this->t('How this field works'),
      '#attributes' => [
        'class' => ['ps-diagnostic-widget__intro-title'],
      ],
    ];
    $element['intro']['text'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => (string) $this->t('Enter the measured value first. The widget suggests the class automatically from the scale.'),
      '#attributes' => [
        'class' => ['ps-diagnostic-widget__intro-text'],
      ],
    ];

    $element['no_classification'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('No classification'),
      '#default_value' => !empty($current['no_classification']),
      '#weight' => 10,
      '#wrapper_attributes' => [
        'class' => ['ps-diagnostic-widget__flag-item'],
      ],
    ];

    $element['non_applicable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Non applicable'),
      '#default_value' => !empty($current['non_applicable']),
      '#weight' => 11,
      '#wrapper_attributes' => [
        'class' => ['ps-diagnostic-widget__flag-item'],
      ],
    ];

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#default_value' => $current['value'] ?? '',
      '#size' => 20,
      '#description' => $this->t('Measured value used to calculate the class automatically.'),
      '#weight' => 20,
      '#wrapper_attributes' => [
        'class' => ['ps-diagnostic-widget__value-item'],
      ],
      '#states' => [
        'visible' => $visibility_states,
      ],
    ];

    $element['class_preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-diagnostic-widget__preview-wrap'],
        'data-ps-diagnostic-class-preview' => '1',
      ],
      '#weight' => 30,
      '#states' => [
        'visible' => $visibility_states,
      ],
    ];
    $element['class_preview']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => (string) $this->t('Class (auto preview)'),
      '#attributes' => [
        'class' => ['form-item__label'],
      ],
    ];
    $element['class_preview']['scale'] = [
      '#markup' => $this->buildClassPreviewMarkup($classes, $class_for_preview),
    ];
    $element['class_preview']['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => (string) $this->t('Class is computed automatically from the value scale.'),
      '#attributes' => [
        'class' => ['form-item__description'],
      ],
    ];

    if ($single_configured_type !== '' && $class_options !== []) {
      $element['class'] = [
        '#type' => 'hidden',
        '#default_value' => $class_for_preview,
        '#attributes' => [
          'data-ps-diagnostic-class-input' => '1',
        ],
        '#weight' => 31,
      ];
    }
    elseif ($class_options !== []) {
      $element['class'] = [
        '#type' => 'select',
        '#title' => $this->t('Class'),
        '#options' => ['' => $this->t('- Select -')] + $class_options,
        '#default_value' => $class_for_preview,
        '#weight' => 31,
        '#states' => [
          'visible' => $visibility_states,
        ],
      ];
    }
    else {
      $element['class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Class'),
        '#default_value' => $class_for_preview,
        '#size' => 10,
        '#weight' => 31,
        '#states' => [
          'visible' => $visibility_states,
        ],
      ];
    }

    $element['diagnostic_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Start date'),
      '#default_value' => $current['diagnostic_date'] ?? '',
      '#weight' => 40,
      '#wrapper_attributes' => [
        'class' => ['ps-diagnostic-widget__date-item'],
      ],
      '#states' => [
        'visible' => $visibility_states,
      ],
    ];

    $element['validity_end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('End date'),
      '#default_value' => $current['validity_end_date'] ?? '',
      '#weight' => 41,
      '#wrapper_attributes' => [
        'class' => ['ps-diagnostic-widget__date-item'],
      ],
      '#states' => [
        'visible' => $visibility_states,
      ],
    ];

    $element['#attributes']['class'][] = 'ps-diagnostic-widget';
    $element['#attributes']['data-ps-diagnostic-widget'] = '1';
    $element['#attributes']['data-ps-diagnostic-type-id'] = $type_id_for_widget;
    $element['#attributes']['data-ps-diagnostic-classes'] = Json::encode($classes);
    $element['#attached']['library'][] = 'ps_diagnostic/diagnostic_admin';

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $configured_types = $this->getConfiguredTypes();
    $single_configured_type = count($configured_types) === 1 ? $configured_types[0] : '';

    $values = array_values(array_filter($values, fn (array $value): bool => !$this->isEffectivelyEmpty($value, $single_configured_type)));

    foreach ($values as &$value) {
      if ($single_configured_type !== '' && (($value['diagnostic_type'] ?? '') === '')) {
        $value['diagnostic_type'] = $single_configured_type;
      }

      $value['no_classification'] = !empty($value['no_classification']) ? 1 : 0;
      $value['non_applicable'] = !empty($value['non_applicable']) ? 1 : 0;

      if ($value['no_classification'] === 1 || $value['non_applicable'] === 1) {
        $value['value'] = '';
        $value['class'] = '';
        $value['diagnostic_date'] = '';
        $value['validity_end_date'] = '';
        continue;
      }

      $type_id = trim((string) ($value['diagnostic_type'] ?? ''));
      $classes = $this->normalizeClasses($this->typeOptionsProvider->getType($type_id)?->getClasses() ?? []);
      $suggested = $this->resolveSuggestedClass(trim((string) ($value['value'] ?? '')), $classes);
      if ($suggested !== NULL && ($single_configured_type !== '' || trim((string) ($value['class'] ?? '')) === '')) {
        $value['class'] = $suggested;
      }
    }
    return $values;
  }

  /**
   * @param array<int, array{label:string,color:string,range_max:int|null}> $classes
   */
  private function buildClassPreviewMarkup(array $classes, string $activeClass): string {
    if ($classes === []) {
      return '<span class="ps-diagnostic-widget__scale-empty">' . Html::escape((string) $this->t('No class scale configured.')) . '</span>';
    }

    $slider_value = 1;
    $class_index = 1;
    $items = '';
    $range_min = 0;
    foreach ($classes as $class) {
      $label = (string) ($class['label'] ?? '');
      if ($label === '') {
        continue;
      }

      $range_max = $class['range_max'] ?? NULL;
      if ($range_max !== NULL) {
        $range_text = $range_min . '–' . (string) $range_max;
        $range_min = ((int) $range_max) + 1;
      }
      else {
        $range_text = '≥' . (string) $range_min;
      }

      $chip_classes = ['ps-diagnostic-widget__scale-chip'];
      if ($activeClass !== '' && strcasecmp($activeClass, $label) === 0) {
        $chip_classes[] = 'is-active';
        $slider_value = $class_index;
      }

      $items .= '<span class="' . implode(' ', $chip_classes) . '" data-ps-class-label="' . Html::escape($label) . '" data-ps-class-color="' . Html::escape((string) ($class['color'] ?? '')) . '">'
        . '<strong class="ps-diagnostic-widget__chip-label">' . Html::escape($label) . '</strong>'
        . '<small class="ps-diagnostic-widget__chip-range">' . Html::escape($range_text) . '</small>'
        . '</span>';
      $class_index++;
    }

    // Slider is injected by JS: Drupal's #markup XSS filter strips <input> elements.
    return '<div class="ps-diagnostic-widget__slider-wrap" data-ps-diagnostic-slider-wrap="1" data-ps-diagnostic-slider-initial="' . (string) $slider_value . '" data-ps-diagnostic-slider-max="' . (string) max(1, count($classes)) . '"></div>'
      . '<div class="ps-diagnostic-widget__scale" data-ps-diagnostic-scale="1">'
      . $items
      . '</div>';
  }

  private function getConfiguredTypes(): array {
    $setting = $this->getFieldSetting('allowed_types');
    if (is_array($setting)) {
      return array_values(array_filter(array_map(static fn ($value): string => trim((string) $value), $setting)));
    }
    return array_values(array_filter(array_map('trim', explode("\n", (string) $setting))));
  }

  /**
   * @param array<int, array<string, mixed>> $classes
   * @return array<int, array{label:string,color:string,range_max:int|null}>
   */
  private function normalizeClasses(array $classes): array {
    $normalized = [];
    foreach ($classes as $class) {
      $label = trim((string) ($class['label'] ?? ''));
      if ($label === '') {
        continue;
      }

      $range_max_raw = $class['range_max'] ?? NULL;
      $range_max = ($range_max_raw === NULL || $range_max_raw === '' || (int) $range_max_raw <= 0)
        ? NULL
        : (int) $range_max_raw;

      $normalized[] = [
        'label' => $label,
        'color' => trim((string) ($class['color'] ?? '')),
        'range_max' => $range_max,
      ];
    }

    return $normalized;
  }

  /**
   * @param array<int, array{label:string,color:string,range_max:int|null}> $classes
   */
  private function resolveSuggestedClass(string $value, array $classes): ?string {
    $numeric_value = $this->extractNumericValue($value);
    if ($numeric_value === NULL || $classes === []) {
      return NULL;
    }

    foreach ($classes as $class) {
      if ($class['range_max'] !== NULL && $numeric_value <= (float) $class['range_max']) {
        return $class['label'];
      }
    }

    $last_class = end($classes);
    return is_array($last_class) ? (string) ($last_class['label'] ?? '') : NULL;
  }

  private function isEffectivelyEmpty(array $value, string $single_configured_type): bool {
    if (!empty($value['no_classification']) || !empty($value['non_applicable'])) {
      return FALSE;
    }

    foreach (['value', 'class', 'diagnostic_date', 'validity_end_date'] as $key) {
      if (trim((string) ($value[$key] ?? '')) !== '') {
        return FALSE;
      }
    }

    $type = trim((string) ($value['diagnostic_type'] ?? ''));
    if ($type === '') {
      return TRUE;
    }

    return $single_configured_type !== '' && $type === $single_configured_type;
  }

  private function extractNumericValue(string $value): ?float {
    $value = trim($value);
    if ($value === '' || !preg_match('/-?[0-9]+([\.,][0-9]+)?/', $value, $matches)) {
      return NULL;
    }

    return (float) str_replace(',', '.', $matches[0]);
  }

}
