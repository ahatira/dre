<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\geofield\Plugin\Field\FieldType\GeofieldItem;
use Drupal\ps_offer\Service\GoogleMapsSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders an interactive offer map with POI and travel time tools.
 *
 * @FieldFormatter(
 *   id = "ps_offer_geo_interactive",
 *   label = @Translation("Offer interactive map"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
final class OfferGeoInteractiveFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly GoogleMapsSettings $googleMapsSettings,
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
      $container->get('ps_offer.google_maps_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    $item = $items->first();
    if (!$item instanceof GeofieldItem) {
      return [];
    }

    $lat = $item->lat;
    $lng = $item->lon;
    if ($lat === NULL || $lng === NULL) {
      return [];
    }

    $address = '';
    if ($entity->hasField('field_address') && !$entity->get('field_address')->isEmpty()) {
      $address_item = $entity->get('field_address')->first();
      $parts = array_filter([
        trim((string) ($address_item->address_line1 ?? '')),
        trim((string) ($address_item->postal_code ?? '')),
        trim((string) ($address_item->locality ?? '')),
      ]);
      $address = implode(', ', $parts);
    }

    return [
      0 => [
        '#type' => 'component',
        '#component' => 'ps_theme:offer-map-interactive',
        '#props' => [
          'lat' => (float) $lat,
          'lng' => (float) $lng,
          'address' => $address,
          'offer_id' => (int) $entity->id(),
        ],
        '#attached' => [
          'library' => ['ps_theme/offer-map'],
          'drupalSettings' => [
            'psOfferMap' => $this->googleMapsSettings->getClientSettings(),
          ],
        ],
      ],
    ];
  }

}
