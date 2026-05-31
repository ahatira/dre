<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

final class DictionaryTypeListBuilder extends ConfigEntityListBuilder {

  public function render(): array {
    $build = parent::render();

    $build['ps_dictionary_actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-dictionary-actions'],
      ],
      'add_type' => [
        '#type' => 'link',
        '#title' => $this->t('Add dictionary type'),
        '#url' => Url::fromRoute('ps_dictionary.type_add'),
        '#attributes' => ['class' => ['button', 'button--primary']],
      ],
      'import_csv' => [
        '#type' => 'link',
        '#title' => $this->t('Import CSV'),
        '#url' => Url::fromRoute('ps_dictionary.import_form'),
        '#attributes' => ['class' => ['button']],
      ],
    ];

    if (isset($build['table'])) {
      $build['ps_dictionary_actions']['#weight'] = ($build['table']['#weight'] ?? 0) - 10;
    }

    return $build;
  }

  public function buildHeader(): array {
    $header['id'] = $this->t('Machine name');
    $header['label'] = $this->t('Label');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity): array {
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }

  public function getDefaultOperations(EntityInterface $entity): array {
    $operations = parent::getDefaultOperations($entity);

    $operations = [
      'entries' => [
        'title' => $this->t('Entries'),
        'weight' => -20,
        'url' => Url::fromRoute('ps_dictionary.entry_collection', [
          'ps_dictionary_type' => $entity->id(),
        ]),
      ],
    ] + $operations;

    return $operations;
  }

}
