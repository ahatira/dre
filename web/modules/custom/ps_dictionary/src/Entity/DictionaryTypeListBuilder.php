<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * List builder for Dictionary Type entities.
 */
class DictionaryTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity): array {
    $operations = [];

    // Add 'List entries' operation.
    if ($entity->hasLinkTemplate('entries')) {
      $operations['entries'] = [
        'title' => $this->t('List entries'),
        'url' => $entity->toUrl('entries'),
        'weight' => -10,
      ];
    }

    // Add Edit operation.
    if ($entity->hasLinkTemplate('edit-form')) {
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'url' => $entity->toUrl('edit-form'),
        'weight' => 0,
      ];
    }

    // Add Delete operation.
    assert($entity instanceof DictionaryTypeInterface);
    if ($entity->hasLinkTemplate('delete-form') && !$entity->isLocked()) {
      $operations['delete'] = [
        'title' => $this->t('Delete'),
        'url' => $entity->toUrl('delete-form'),
        'weight' => 10,
      ];
    }

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'label' => $this->t('Label'),
      'id' => $this->t('Machine Name'),
      'entries' => $this->t('Entries'),
      'locked' => $this->t('Locked'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryTypeInterface $entity */
    $entryStorage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
    $count = $entryStorage->getQuery()
      ->condition('dictionary_type', $entity->id())
      ->count()
      ->execute();

    return [
      'label' => $entity->label(),
      'id' => $entity->id(),
      'entries' => $count,
      'locked' => $entity->isLocked() ? $this->t('Yes') : $this->t('No'),
    ] + parent::buildRow($entity);
  }

}
