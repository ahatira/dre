<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_diagnostic\Entity\PsDiagnosticInterface;

/**
 * Provides a listing of diagnostics.
 */
class PsDiagnosticListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header = [];
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['unit'] = $this->t('Unit');
    $header['classes'] = $this->t('Classes');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    assert($entity instanceof PsDiagnosticInterface);

    $row = [];
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['unit'] = $entity->getUnit();
    $row['classes'] = implode(', ', array_keys($entity->getClasses()));

    return $row + parent::buildRow($entity);
  }

}
