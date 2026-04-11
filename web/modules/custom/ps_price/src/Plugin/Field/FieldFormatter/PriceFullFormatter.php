<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_price\Service\PriceFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Full price field formatter.
 *
 * Displays price with all details (amount, currency, unit, period, flags).
 */
#[FieldFormatter(
  id: 'ps_price_full',
  label: new TranslatableMarkup('Full'),
  field_types: ['ps_price'],
  weight: 0,
)]
class PriceFullFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a PriceFullFormatter object.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array<string, mixed> $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label.
   * @param string $view_mode
   *   The view mode.
   * @param array<string, mixed> $third_party_settings
   *   Third party settings.
   * @param \Drupal\ps_price\Service\PriceFormatterInterface $priceFormatter
   *   The price formatter service.
   */
  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    mixed $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    protected readonly PriceFormatterInterface $priceFormatter,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_price.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    unset($langcode);
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => $this->priceFormatter->format($item, [
          'show_currency' => TRUE,
          'show_unit' => TRUE,
          'show_period' => TRUE,
          'show_flags' => TRUE,
        ]),
      ];
    }

    return $elements;
  }

}
