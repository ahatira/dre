<?php

declare(strict_types=1);

namespace Drupal\ps_search;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\ps_search\Entity\SearchAlertInterface;

/**
 * List builder for Search alert entities.
 */
final class SearchAlertListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'alert_name' => $this->t('Title'),
      'prof_email' => $this->t('Email'),
      'frequence' => $this->t('Frequency'),
      'alert_status' => $this->t('Status'),
      'last_sent' => $this->t('Last sent'),
      'last_match_count' => $this->t('Last matches'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    assert($entity instanceof SearchAlertInterface);
    /** @var \Drupal\ps_search\Entity\SearchAlert $entity */
    $row['alert_name'] = $entity->getAlertName();
    $row['prof_email'] = $entity->getProfEmail();
    $row['frequence'] = $entity->getFrequence();
    $row['alert_status'] = $entity->get('alert_status')->value;
    $lastSent = (int) $entity->get('last_sent')->value;
    $row['last_sent'] = $lastSent > 0
      ? \Drupal::service('date.formatter')->format($lastSent, 'short')
      : $this->t('Never');
    $row['last_match_count'] = (int) $entity->get('last_match_count')->value;
    return $row + parent::buildRow($entity);
  }

}
