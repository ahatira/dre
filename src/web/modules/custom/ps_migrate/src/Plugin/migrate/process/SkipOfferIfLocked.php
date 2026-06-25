<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\ImportPipelineLockStrategy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Skips offer migration row when the existing offer is manually locked.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_offer_if_locked"
 * )
 */
final class SkipOfferIfLocked extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ImportPipelineLockStrategy $lockStrategy,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('ps_migrate.import_pipeline_lock_strategy'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
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

    if (empty($ids)) {
      return $value;
    }

    $nid = (int) reset($ids);
    $offer = $this->entityTypeManager->getStorage('node')->load($nid);
    if ($offer && $offer->hasField('field_internal_lock') && (bool) $offer->get('field_internal_lock')->value) {
      if ($this->lockStrategy->shouldSkipRow('field_internal_lock')) {
        throw new MigrateSkipRowException(sprintf('Offer %s is protected by field_internal_lock.', $businessId));
      }
    }

    return $value;
  }

}
