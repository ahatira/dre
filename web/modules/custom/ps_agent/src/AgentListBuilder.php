<?php

declare(strict_types=1);

namespace Drupal\ps_agent;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a list builder for Agent entities.
 */
class AgentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header = [];
    $header['id'] = $this->t('ID');
    $header['last_name'] = $this->t('Last Name');
    $header['first_name'] = $this->t('First Name');
    $header['email'] = $this->t('Email');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row = [];
    $row['id'] = $entity->id();
    $row['last_name'] = $entity->get('last_name')->value ?? '';
    $row['first_name'] = $entity->get('first_name')->value ?? '';
    $row['email'] = $entity->get('email')->value ?? '';
    $row['status'] = $entity->get('status')->value ? $this->t('Active') : $this->t('Inactive');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    $operations = parent::getDefaultOperations($entity);
    return $operations;
  }

}
