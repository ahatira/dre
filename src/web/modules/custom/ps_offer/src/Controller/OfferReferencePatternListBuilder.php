<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

final class OfferReferencePatternListBuilder extends ConfigEntityListBuilder {

  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['bundles'] = $this->t('Bundles');
    $header['segments'] = $this->t('Segments');
    $header['mode'] = $this->t('Manual override');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_offer\Entity\OfferReferencePatternInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['bundles'] = implode(', ', $entity->getTargetBundles());
    $row['segments'] = (string) count($entity->getSegments());
    $row['mode'] = $entity->allowsManualOverride() ? $this->t('Allowed') : $this->t('Disabled');
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

}