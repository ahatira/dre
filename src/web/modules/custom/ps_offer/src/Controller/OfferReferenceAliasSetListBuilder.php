<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

final class OfferReferenceAliasSetListBuilder extends ConfigEntityListBuilder {

  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['patterns'] = $this->t('Patterns');
    $header['entries'] = $this->t('Entries');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_offer\Entity\OfferReferenceAliasSetInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['patterns'] = $entity->getAppliesToPatternIds() !== [] ? implode(', ', $entity->getAppliesToPatternIds()) : $this->t('All patterns');
    $row['entries'] = (string) count($entity->getEntries());
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

}