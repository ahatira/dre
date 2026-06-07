<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Drupal\address\Plugin\Field\FieldFormatter\AddressPlainFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_offer\Service\OfferSurfaceKpiBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formats surface/capacity KPI and locality on one line.
 *
 * @FieldFormatter(
 *   id = "ps_offer_location_summary",
 *   label = @Translation("Offer location summary"),
 *   field_types = {
 *     "address"
 *   }
 * )
 */
final class OfferLocationSummaryFormatter extends AddressPlainFormatter implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    AddressFormatRepositoryInterface $address_format_repository,
    CountryRepositoryInterface $country_repository,
    SubdivisionRepositoryInterface $subdivision_repository,
    private readonly OfferSurfaceKpiBuilder $surfaceKpiBuilder,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
      $address_format_repository,
      $country_repository,
      $subdivision_repository,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('address.address_format_repository'),
      $container->get('address.country_repository'),
      $container->get('address.subdivision_repository'),
      $container->get('ps_offer.surface_kpi_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    $parts = [];

    $kpi = $this->surfaceKpiBuilder->buildKpiSummary($entity);
    if ($kpi !== '') {
      $parts[] = $kpi;
    }

    foreach ($items as $item) {
      $postal = trim((string) ($item->postal_code ?? ''));
      $locality = trim((string) ($item->locality ?? ''));
      $location = trim($postal . ' ' . $locality);
      if ($location !== '') {
        $parts[] = $location;
      }
    }

    if ($parts === []) {
      return [];
    }

    return [
      0 => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => implode(' • ', $parts),
        '#attributes' => ['class' => ['ps-offer-location-summary']],
      ],
    ];
  }

}
