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
 * Maps CRM DISPLAY flag to field_show_address.
 *
 * @MigrateProcessPlugin(
 *   id = "offer_show_address_from_xml"
 * )
 */
final class OfferShowAddressFromXml extends ProcessPluginBase implements ContainerFactoryPluginInterface {

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
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): int {
    if (!$value instanceof \SimpleXMLElement) {
      return 1;
    }

    return $this->locationReader->showsExactAddress($value) ? 1 : 0;
  }

}
