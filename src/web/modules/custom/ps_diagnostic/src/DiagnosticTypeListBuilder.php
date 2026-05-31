<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

final class DiagnosticTypeListBuilder extends ConfigEntityListBuilder {

  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('ID');
    $header['unit'] = $this->t('Unit');
    $header['classes'] = $this->t('Classes');
    $header['enabled'] = $this->t('Enabled');
    $header['weight'] = $this->t('Weight');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity): array {
    $classes = (array) $entity->get('classes');
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['unit'] = (string) ($entity->get('unit') ?? '');
    $row['classes'] = (string) count(array_filter($classes, static fn (array $class): bool => trim((string) ($class['label'] ?? '')) !== ''));
    $row['enabled'] = $entity->get('enabled') ? $this->t('Yes') : $this->t('No');
    $row['weight'] = (int) $entity->get('weight');
    return $row + parent::buildRow($entity);
  }

}
