<?php

declare(strict_types=1);

namespace Drupal\ps_context\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * List builder for ps_context_label_profile config entities.
 */
final class PsContextLabelProfileListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['asset_type'] = $this->t('Asset type');
    $header['operation_type'] = $this->t('Operation');
    $header['labels_count'] = $this->t('Labels');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_context\Entity\PsContextLabelProfileInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['asset_type'] = $entity->getAssetType();
    $row['operation_type'] = $entity->getOperationType();
    $row['labels_count'] = count(array_filter($entity->getLabels(), static fn(string $v): bool => $v !== ''));
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $this->messenger()->addMessage(
      $this->t('Labels define search bar, homepage hero and offer wording by asset type and operation. Profiles are merged from least to most specific (wildcards first).'),
      'info',
    );
    return parent::render();
  }

}
