<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\geofield\Plugin\Field\FieldType\GeofieldItem;
use Drupal\ps_offer\Service\GoogleMapsSettings;
use Drupal\ps_offer\Service\OfferMapLocationBuilder;
use Drupal\ps_offer\Service\OfferMapSettings;
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
    private readonly OfferMapLocationBuilder $mapLocationBuilder,
    private readonly OfferMapSettings $mapSettings,
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
      $container->get('ps_offer.map_location_builder'),
      $container->get('ps_offer.map_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    if (!$this->mapSettings->isEnabled()) {
      return [];
    }

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

    $exact_location = $this->mapLocationBuilder->showsExactAddress($entity);
    $locality_label = $this->mapLocationBuilder->buildLocalityLabel($entity);
    $address = $this->mapLocationBuilder->buildPublicAddress($entity);
    $show_travel = $exact_location && $this->mapSettings->isTravelEnabled();
    $show_poi = $this->mapSettings->isPoiEnabled();

    $client_settings = $this->googleMapsSettings->getClientSettings();
    $client_settings['map'] = $this->mapSettings->getClientMapSettings($entity);

    return [
      0 => [
        '#type' => 'component',
        '#component' => 'ps_theme:offer-map-interactive',
        '#props' => [
          'lat' => (float) $lat,
          'lng' => (float) $lng,
          'exact_location' => $exact_location,
          'locality_label' => $locality_label,
          'address' => $address,
          'show_travel' => $show_travel,
          'show_poi' => $show_poi,
          'travel_mode_icons' => $this->mapSettings->getTravelModeIcons(),
          'poi_filter_icons' => $this->mapSettings->getPoiFilterIcons(),
          'offer_id' => (int) $entity->id(),
        ],
        '#attached' => [
          'library' => ['ps_theme/offer-map'],
          'drupalSettings' => [
            'psOfferMap' => $client_settings,
          ],
        ],
        '#cache' => [
          'tags' => array_merge(
            $entity->getCacheTags(),
            $this->mapSettings->getCacheTags(),
          ),
          'contexts' => ['languages:language_interface'],
        ],
      ],
    ];
  }

}
