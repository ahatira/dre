<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\OfferLocationXmlReader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds field_geo WKT from visible or hidden CRM GPS coordinates.
 *
 * @MigrateProcessPlugin(
 *   id = "offer_geo_from_xml"
 * )
 */
final class OfferGeoFromXml extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly OfferLocationXmlReader $locationReader,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.offer_location_xml_reader'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): ?string {
    if (!$value instanceof \SimpleXMLElement) {
      return NULL;
    }

    return $this->locationReader->getGeoWkt($value);
  }

}
