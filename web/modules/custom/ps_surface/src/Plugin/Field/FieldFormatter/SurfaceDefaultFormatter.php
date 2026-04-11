<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default formatter for ps_surface field.
 */
#[FieldFormatter(
  id: 'ps_surface_default',
  label: new TranslatableMarkup('Surface formatter'),
  field_types: ['ps_surface'],
)]
final class SurfaceDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the formatter.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array<string, mixed> $plugin_definition
   *   The plugin definition array.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array<string, mixed> $settings
   *   The settings.
   * @param string $label
   *   The label.
   * @param string $view_mode
   *   The view mode.
   * @param array<string, mixed> $third_party_settings
   *   Third-party settings.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   */
  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly DictionaryManagerInterface $dictionaryManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_unit' => TRUE,
      'show_qualification' => FALSE,
      'decimals' => 0,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['show_unit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show unit label'),
      '#default_value' => $this->getSetting('show_unit'),
    ];

    $form['show_qualification'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show qualification label'),
      '#default_value' => $this->getSetting('show_qualification'),
    ];

    $form['decimals'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of decimals'),
      '#default_value' => $this->getSetting('decimals'),
      '#min' => 0,
      '#max' => 4,
      '#step' => 1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $summary[] = $this->getSetting('show_unit') ? $this->t('Unit: displayed') : $this->t('Unit: hidden');
    $summary[] = $this->getSetting('show_qualification') ? $this->t('Qualification: displayed') : $this->t('Qualification: hidden');
    $summary[] = $this->t('Decimals: @count', ['@count' => $this->getSetting('decimals')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    unset($langcode);
    $elements = [];
    $decimals = (int) $this->getSetting('decimals');

    foreach ($items as $delta => $item) {
      // Get the numeric surface value from the field item.
      $surfaceValue = $item->get('value')->getValue();
      if ($surfaceValue === NULL) {
        continue;
      }

      $parts = [number_format((float) $surfaceValue, $decimals, '.', ' ')];

      if ($this->getSetting('show_unit')) {
        $unitCode = $item->get('unit')->getValue();
        if ($unitCode !== NULL) {
          // Try to get symbol metadata; fall back to label, then code.
          $unitSymbol = $this->dictionaryManager->getMetadataValue('surface_unit', $unitCode, 'symbol');
          if ($unitSymbol === NULL) {
            $unitSymbol = $this->dictionaryManager->getLabel('surface_unit', $unitCode) ?? $unitCode;
          }
          // Ensure scalar value for implode (in case metadata returns array).
          $parts[] = is_scalar($unitSymbol) ? (string) $unitSymbol : $unitCode;
        }
      }

      if ($this->getSetting('show_qualification')) {
        $qualificationCode = $item->get('qualification')->getValue();
        if ($qualificationCode !== NULL) {
          $qualificationLabel = $this->dictionaryManager->getLabel('surface_qualification', $qualificationCode) ?? $qualificationCode;
          $parts[] = '(' . $qualificationLabel . ')';
        }
      }

      $elements[$delta] = [
        '#markup' => implode(' ', $parts),
      ];
    }

    return $elements;
  }

}
