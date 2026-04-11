<?php

declare(strict_types=1);

namespace Drupal\ps_division;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * List builder for Division entities.
 */
final class DivisionListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['type'] = $this->t('Type');
    $header['building_name'] = $this->t('Building Name');
    $header['lot'] = $this->t('Lot');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_division\Entity\DivisionInterface $entity */
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['building_name'] = $entity->getBuildingName();
    $row['lot'] = $entity->getLot() ?? $this->t('N/A');
    return $row + parent::buildRow($entity);
  }

}
