<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_migrate\Service\ImportPipelineRunContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Skips offer rows when source checksum matches stored field_source_checksum.
 *
 * Active only in delta mode when skip_unchanged_offers is enabled in BO.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_unchanged_offer"
 * )
 */
final class SkipUnchangedOffer extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly ImportPipelineRunContext $runContext,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('ps_core.entity_protection_manager'),
      $container->get('ps_migrate.import_pipeline_run_context'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
    if (!$this->runContext->shouldSkipUnchangedOffers()) {
      return $value;
    }

    $businessId = trim((string) $value);
    if ($businessId === '') {
      return $value;
    }

    $ids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'offer')
      ->condition('field_business_id', $businessId)
      ->range(0, 1)
      ->execute();

    if ($ids === []) {
      return $value;
    }

    $offer = $this->entityTypeManager->getStorage('node')->load((int) reset($ids));
    if ($offer === NULL || !$offer->hasField('field_source_checksum')) {
      return $value;
    }

    $storedChecksum = trim((string) $offer->get('field_source_checksum')->value);
    if ($storedChecksum === '') {
      return $value;
    }

    $incomingChecksum = $this->protectionManager->computeChecksum($row->getSource());
    if (hash_equals($storedChecksum, $incomingChecksum)) {
      throw new MigrateSkipRowException(sprintf(
        'Offer %s unchanged — source checksum matches stored value.',
        $businessId,
      ));
    }

    return $value;
  }

}
