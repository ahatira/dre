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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Compact locality formatter for comparison tables.
 *
 * @FieldFormatter(
 *   id = "ps_offer_location_compare",
 *   label = @Translation("Offer location (compare table)"),
 *   field_types = {
 *     "address"
 *   }
 * )
 */
final class OfferLocationCompareFormatter extends AddressPlainFormatter implements ContainerFactoryPluginInterface {

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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    foreach ($items as $item) {
      $postal = trim((string) ($item->postal_code ?? ''));
      $locality = trim((string) ($item->locality ?? ''));
      $location = trim($postal . ' ' . $locality);
      if ($location === '') {
        continue;
      }

      return [
        0 => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $location,
          '#attributes' => ['class' => ['ps-offer-location-compare']],
        ],
      ];
    }

    return [];
  }

}
