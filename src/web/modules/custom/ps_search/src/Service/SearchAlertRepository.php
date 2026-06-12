<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ps_search\Entity\SearchAlert;
use Drupal\ps_search\Entity\SearchAlertInterface;

/**
 * Creates and deduplicates Search alert entities from webform submissions.
 */
final class SearchAlertRepository {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly SearchAlertCriteriaSerializer $criteriaSerializer,
    private readonly AccountProxyInterface $currentUser,
  ) {}

  /**
   * Creates a search alert entity from submission values.
   *
   * @param array<string, mixed> $values
   *   Webform submission values.
   *
   * @return \Drupal\ps_search\Entity\SearchAlert|null
   *   Saved entity, or NULL when duplicate active alert exists.
   */
  public function createFromSubmission(array $values): ?SearchAlert {
    $criteriaJson = (string) ($values['criteria_json'] ?? '');
    $criteria = json_decode($criteriaJson, TRUE);
    if (!is_array($criteria)) {
      $criteria = [];
    }
    $criteria = $this->criteriaSerializer->normalizeCriteria($criteria);
    $email = strtolower(trim((string) ($values['prof_email_address'] ?? '')));
    $hash = $this->criteriaSerializer->hash($criteria);

    if ($email === '') {
      return NULL;
    }

    if ($this->findDuplicate($email, $hash) !== NULL) {
      return NULL;
    }

    /** @var \Drupal\ps_search\Entity\SearchAlert $entity */
    $entity = SearchAlert::create([
      'alert_name' => trim((string) ($values['alert_name'] ?? '')),
      'prof_email' => $email,
      'frequence' => (string) ($values['frequence'] ?? SearchAlertInterface::FREQUENCE_WEEKLY),
      'optout_email' => !empty($values['optout_email_transaction']),
      'optout_sms' => !empty($values['optout_sms_transaction']),
      'optout_tel' => !empty($values['optout_tel_transaction']),
      'criteria' => $this->criteriaSerializer->toJson($criteria),
      'criteria_hash' => $hash,
      'search_url' => (string) ($values['search_url'] ?? ($criteria['search_url'] ?? '')),
      'search_path' => (string) ($values['search_path'] ?? ($criteria['search_path'] ?? '')),
      'alert_status' => SearchAlertInterface::STATUS_ACTIVE,
      'uid' => (int) $this->currentUser->id(),
      'langcode' => (string) ($criteria['langcode'] ?? 'en'),
    ]);
    $entity->save();
    return $entity;
  }

  /**
   * Finds an existing active alert for email + criteria hash.
   */
  public function findDuplicate(string $email, string $hash): ?SearchAlert {
    $storage = $this->entityTypeManager->getStorage('search_alert');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('prof_email', strtolower($email))
      ->condition('criteria_hash', $hash)
      ->condition('alert_status', SearchAlertInterface::STATUS_ACTIVE)
      ->range(0, 1)
      ->execute();
    if ($ids === []) {
      return NULL;
    }
    $entity = $storage->load(reset($ids));
    return $entity instanceof SearchAlert ? $entity : NULL;
  }

}
