<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_diagnostic\Service\DiagnosticTypeIconResolver;
use Drupal\ps_diagnostic\Service\DiagnosticTypeOptionsProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DiagnosticItemFormatterBase extends FormatterBase implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    mixed $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    protected readonly DiagnosticTypeOptionsProvider $typeOptionsProvider,
    protected readonly DateFormatterInterface $dateFormatter,
    protected readonly DiagnosticTypeIconResolver $iconResolver,
    protected readonly ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_diagnostic.type_options'),
      $container->get('date.formatter'),
      $container->get('ps_diagnostic.type_icon_resolver'),
      $container->get('config.factory'),
    );
  }

  public static function defaultSettings(): array {
    return [
      'show_dates' => FALSE,
      'show_ranges' => TRUE,
      'show_unknown_banner' => TRUE,
      'show_reference_table' => TRUE,
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_ranges'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show ranges'),
      '#default_value' => (bool) $this->getSetting('show_ranges'),
    ];

    $elements['show_dates'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show dates'),
      '#default_value' => (bool) $this->getSetting('show_dates'),
    ];

    $elements['show_unknown_banner'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show unknown-state banner'),
      '#default_value' => (bool) $this->getSetting('show_unknown_banner'),
    ];

    $elements['show_reference_table'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show reference threshold table'),
      '#default_value' => (bool) $this->getSetting('show_reference_table'),
      '#description' => $this->t('Used by the Full formatter.'),
    ];

    return $elements;
  }

  public function settingsSummary(): array {
    return [
      $this->getSetting('show_ranges') ? $this->t('Ranges: shown') : $this->t('Ranges: hidden'),
      $this->getSetting('show_dates') ? $this->t('Dates: shown') : $this->t('Dates: hidden'),
      $this->getSetting('show_unknown_banner') ? $this->t('Unknown banner: shown') : $this->t('Unknown banner: hidden'),
      $this->getSetting('show_reference_table') ? $this->t('Reference table: shown') : $this->t('Reference table: hidden'),
    ];
  }

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      $value = $item->getValue();
      if (($value['diagnostic_type'] ?? '') === '') {
        continue;
      }

      $type_id = (string) $value['diagnostic_type'];
      $type = $this->typeOptionsProvider->getType($type_id);
      $classes = $this->normalizeClasses($type?->getClasses() ?? []);
      $active_class = $this->resolveActiveClass(
        trim((string) ($value['class'] ?? '')),
        trim((string) ($value['value'] ?? '')),
        $classes,
      );

      $state = $this->resolveDisabledState($value, $active_class);
      $unit = $type?->getUnit() ?? '';
      $has_class = trim((string) ($value['class'] ?? '')) !== '';
      $has_value = trim((string) ($value['value'] ?? '')) !== '';
      $show_dates = (bool) $this->getSetting('show_dates') && ($has_class || $has_value);
      $cursor_colors = $this->resolveCursorColors($active_class, $state['disabled']);

      $elements[$delta] = [
        '#theme' => $this->getThemeHook(),
        '#item' => $value,
        '#type_id' => $type_id,
        '#type_label' => $type?->label() ?? strtoupper($type_id),
        '#icon' => $this->iconResolver->buildRenderable($type_id),
        '#unit' => $unit,
        '#value_display' => $this->buildValueDisplay(trim((string) ($value['value'] ?? '')), $unit),
        '#diagnostic_date_display' => $show_dates ? $this->formatDate(trim((string) ($value['diagnostic_date'] ?? '')), $langcode) : '',
        '#validity_end_date_display' => $show_dates ? $this->formatDate(trim((string) ($value['validity_end_date'] ?? '')), $langcode) : '',
        '#show_dates' => $show_dates,
        '#show_ranges' => (bool) $this->getSetting('show_ranges'),
        '#show_unknown_banner' => (bool) $this->getSetting('show_unknown_banner'),
        '#show_reference_table' => (bool) $this->getSetting('show_reference_table'),
        '#is_disabled' => $state['disabled'],
        '#disabled_reason' => $state['reason'],
        '#disabled_message' => $state['message'],
        '#classes' => $classes,
        '#scale_rows' => $this->buildScaleRows($classes),
        '#active_class' => $active_class,
        '#cursor_fill' => $cursor_colors['fill'],
        '#cursor_text' => $cursor_colors['text'],
        '#legend_low' => $this->getScaleLegends($type_id)['low'],
        '#legend_high' => $this->getScaleLegends($type_id)['high'],
        '#attached' => [
          'library' => ['ps_diagnostic/diagnostic'],
        ],
        '#cache' => [
          'tags' => Cache::mergeTags(
            $type ? $type->getCacheTags() : [],
            $this->iconResolver->getCacheTagsForType($type_id),
            $this->configFactory->get('ps_diagnostic.settings')->getCacheTags(),
          ),
        ],
      ];
    }

    return $elements;
  }

  abstract protected function getThemeHook(): string;

  private function resolveDisabledState(array $value, ?array $active_class): array {
    if (!empty($value['non_applicable'])) {
      return [
        'disabled' => TRUE,
        'reason' => 'non_applicable',
        'message' => $this->resolveDisabledMessage('non_applicable'),
      ];
    }

    if (!empty($value['no_classification'])) {
      return [
        'disabled' => TRUE,
        'reason' => 'no_classification',
        'message' => $this->resolveDisabledMessage('no_classification'),
      ];
    }

    $end_date = trim((string) ($value['validity_end_date'] ?? ''));
    if ($end_date !== '' && $this->isExpired($end_date)) {
      return [
        'disabled' => TRUE,
        'reason' => 'expired',
        'message' => $this->resolveDisabledMessage('expired'),
      ];
    }

    $has_class = trim((string) ($value['class'] ?? '')) !== '';
    $has_value = trim((string) ($value['value'] ?? '')) !== '';
    if (!$has_value && !$has_class) {
      return [
        'disabled' => TRUE,
        'reason' => 'missing_data',
        'message' => $this->resolveDisabledMessage('missing_data'),
      ];
    }

    if (!$active_class) {
      return [
        'disabled' => TRUE,
        'reason' => 'missing_data',
        'message' => $this->resolveDisabledMessage('missing_data'),
      ];
    }

    return ['disabled' => FALSE, 'reason' => 'ok', 'message' => ''];
  }

  /**
   * Resolves the front message for a disabled diagnostic state.
   */
  private function resolveDisabledMessage(string $reason): string {
    $config = $this->configFactory->get('ps_diagnostic.settings');
    $mode = (string) ($config->get('fallback_message_mode') ?? 'single');
    if ($mode === 'single') {
      $message = trim((string) ($config->get('fallback_message_single') ?? ''));
      if ($message !== '') {
        return $message;
      }

      return (string) $this->t('Energy label not provided by the owner.');
    }

    return match ($reason) {
      'non_applicable' => (string) $this->t('Diagnostic not applicable for this offer.'),
      'no_classification' => (string) $this->t('Diagnostic has no classification.'),
      'expired' => (string) $this->t('Diagnostic has expired.'),
      default => (string) $this->t('Diagnostic label is not provided by the owner.'),
    };
  }

  /**
   * Resolves cursor badge colors from the active class or disabled state.
   *
   * @return array{fill: string, text: string}
   *   Hex colors for the scale cursor badge.
   */
  private function resolveCursorColors(?array $active_class, bool $is_disabled): array {
    if ($is_disabled || $active_class === NULL) {
      return ['fill' => '#989a9f', 'text' => '#ffffff'];
    }

    $fill = trim((string) ($active_class['color'] ?? ''));
    if ($fill === '' || !preg_match('/^#[0-9A-Fa-f]{6}$/', $fill)) {
      $fill = '#111316';
    }

    $label = strtoupper(trim((string) ($active_class['label'] ?? '')));
    $text = in_array($label, ['D', 'E'], TRUE) ? '#1f2226' : '#ffffff';

    return ['fill' => $fill, 'text' => $text];
  }

  private function isExpired(string $value): bool {
    $timestamp = strtotime($value);
    if (!$timestamp) {
      return FALSE;
    }

    $today = strtotime('today');
    return $timestamp < $today;
  }

  private function buildValueDisplay(string $value, string $unit): string {
    if ($value === '') {
      return '';
    }
    return $unit !== '' ? $value . ' ' . $unit : $value;
  }

  private function getScaleLegends(string $type_id): array {
    return match (strtolower($type_id)) {
      'dpe' => [
        'low' => (string) $this->t('Energy-efficient property'),
        'high' => (string) $this->t('Energy-intensive property'),
      ],
      'ges' => [
        'low' => (string) $this->t('Low greenhouse gas emissions'),
        'high' => (string) $this->t('High greenhouse gas emissions'),
      ],
      default => [
        'low' => (string) $this->t('Best class'),
        'high' => (string) $this->t('Worst class'),
      ],
    };
  }

  private function normalizeClasses(array $classes): array {
    $normalized = [];
    foreach ($classes as $class) {
      $label = trim((string) ($class['label'] ?? ''));
      if ($label === '') {
        continue;
      }

      $range_max_raw = $class['range_max'] ?? '';
      $range_max = ($range_max_raw === '' || (int) $range_max_raw <= 0) ? NULL : (int) $range_max_raw;
      $normalized[] = [
        'label' => $label,
        'color' => trim((string) ($class['color'] ?? '')),
        'range_max' => $range_max,
      ];
    }

    $min = 0;
    foreach ($normalized as $index => $class) {
      $range_min = $index === 0 ? 0 : $min + 1;
      $range_max = isset($class['range_max']) ? (int) $class['range_max'] : 0;
      $normalized[$index]['range_min'] = $range_min;
      $normalized[$index]['range_text'] = $range_max > 0
        ? ((string) $range_min) . ' - ' . ((string) $range_max)
        : (string) $range_min . '+';
      if ($range_max > 0) {
        $min = $range_max;
      }
    }

    return $normalized;
  }

  private function resolveActiveClass(string $item_class, string $value, array $classes): ?array {
    if ($classes === []) {
      return NULL;
    }

    if ($item_class !== '') {
      foreach ($classes as $class) {
        if (strcasecmp($class['label'], $item_class) === 0) {
          return $class;
        }
      }
    }

    $numeric_value = $this->extractNumericValue($value);
    if ($numeric_value === NULL) {
      return NULL;
    }

    foreach ($classes as $class) {
      if ($class['range_max'] !== NULL && $numeric_value <= (float) $class['range_max']) {
        return $class;
      }
    }

    return end($classes) ?: NULL;
  }

  private function extractNumericValue(string $value): ?float {
    if ($value === '') {
      return NULL;
    }
    if (!preg_match('/-?[0-9]+([\.,][0-9]+)?/', $value, $matches)) {
      return NULL;
    }
    return (float) str_replace(',', '.', $matches[0]);
  }

  private function formatDate(string $value, string $langcode): string {
    if ($value === '') {
      return '';
    }

    $timestamp = strtotime($value);
    if (!$timestamp) {
      return $value;
    }

    return $this->dateFormatter->format($timestamp, 'short', '', NULL, $langcode);
  }

  private function buildScaleRows(array $classes): array {
    $total = count($classes);
    if ($total === 0) {
      return [];
    }

    $rows = [];
    foreach (array_values($classes) as $index => $class) {
      $rows[] = $class + [
        'width' => (int) round(48 + (($index + 1) / $total) * 52),
      ];
    }

    return $rows;
  }

}
