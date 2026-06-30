<?php

declare(strict_types=1);

namespace Drupal\views_promo_card;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * List builder for promo card placements.
 */
class PromoCardPlacementListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'label' => $this->t('Label'),
      'view' => $this->t('View'),
      'display' => $this->t('Display'),
      'status' => $this->t('Status'),
      'operations' => $this->t('Operations'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface $entity */
    $row['label'] = $entity->label();
    $row['view'] = $entity->getViewId();
    $row['display'] = $entity->getDisplayId();
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    if (isset($build['table']['#empty'])) {
      $build['table']['#empty'] = $this->t('No placements yet. <a href=":url">Add a placement</a>.', [
        ':url' => Url::fromRoute('entity.promo_card_placement.add_form')->toString(),
      ]);
    }

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-list__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Placements bind promo cards to a View display and define where cards appear in the search results grid.'),
        '#weight' => -20,
      ],
    ];

    return $build;
  }

}
