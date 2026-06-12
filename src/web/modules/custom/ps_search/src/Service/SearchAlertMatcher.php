<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_search\Entity\SearchAlert;
use Drupal\ps_search\Entity\SearchAlertInterface;
use Drupal\ps_search\Search\Query\SearchQueryFactory;
use Drupal\search_api\Query\QueryInterface;

/**
 * Finds new offers matching stored search alert criteria.
 */
final class SearchAlertMatcher {

  private const INDEX_ID = 'offers';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly SearchAlertCriteriaSerializer $criteriaSerializer,
    private readonly SearchQueryFactory $queryFactory,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LoggerChannelInterface $logger,
  ) {}

  /**
   * Processes a batch of active alerts and returns digest payloads.
   *
   * @return array<int, array{alert: \Drupal\ps_search\Entity\SearchAlert, nids: array<int, int>}>
   *   Alerts with matching node IDs.
   */
  public function processBatch(?int $limit = NULL): array {
    $settings = $this->configFactory->get('ps_search.alert_settings');
    if (!(bool) $settings->get('enabled')) {
      return [];
    }

    $batchSize = $limit ?? max(1, (int) ($settings->get('batch_size') ?? 50));
    $storage = $this->entityTypeManager->getStorage('search_alert');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('alert_status', SearchAlertInterface::STATUS_ACTIVE)
      ->sort('last_sent', 'ASC')
      ->range(0, $batchSize)
      ->execute();

    if ($ids === []) {
      return [];
    }

    $results = [];
    /** @var \Drupal\ps_search\Entity\SearchAlert[] $alerts */
    $alerts = $storage->loadMultiple($ids);
    foreach ($alerts as $alert) {
      if (!$this->isDue($alert)) {
        continue;
      }

      $nids = $this->findMatchingOfferIds($alert);
      if ($nids === []) {
        continue;
      }

      $results[] = [
        'alert' => $alert,
        'nids' => $nids,
      ];
    }

    return $results;
  }

  /**
   * Purges anonymous alerts older than configured retention.
   */
  public function purgeExpiredAnonymous(): int {
    $retentionDays = max(1, (int) ($this->configFactory->get('ps_search.alert_settings')->get('retention_days') ?? 365));
    $threshold = \Drupal::time()->getRequestTime() - ($retentionDays * 86400);
    $storage = $this->entityTypeManager->getStorage('search_alert');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', 0)
      ->condition('created', $threshold, '<')
      ->execute();

    if ($ids === []) {
      return 0;
    }

    $entities = $storage->loadMultiple($ids);
    $storage->delete($entities);
    $this->logger->notice('Purged @count expired anonymous search alert(s).', ['@count' => count($ids)]);

    return count($ids);
  }

  /**
   * Finds offer node IDs changed since the alert last notification.
   *
   * @return array<int, int>
   *   Node IDs keyed by node ID.
   */
  public function findMatchingOfferIds(SearchAlert $alert): array {
    $criteria = $alert->getCriteria();
    if ($criteria === []) {
      return [];
    }

    try {
      $request = $this->criteriaSerializer->buildRequest($criteria);
      $query = $this->queryFactory->createQuery($request);
      $this->applyChangedSince($query, $alert);
      $query->range(0, 50);

      $results = $query->execute();
      $nids = [];
      foreach ($results->getResultItems() as $item) {
        $nid = (int) $item->getOriginalObject()?->getValue()?->id();
        if ($nid > 0) {
          $nids[$nid] = $nid;
        }
      }
      return $nids;
    }
    catch (\Throwable $exception) {
      $this->logger->error('Search alert @id matching failed: @message', [
        '@id' => $alert->id(),
        '@message' => $exception->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Whether the alert frequency allows sending on this run.
   */
  private function isDue(SearchAlert $alert): bool {
    $lastSent = (int) ($alert->get('last_sent')->value ?? 0);
    if ($lastSent === 0) {
      return TRUE;
    }

    $interval = $alert->getFrequence() === SearchAlertInterface::FREQUENCE_DAILY ? 86400 : 604800;
    return (\Drupal::time()->getRequestTime() - $lastSent) >= $interval;
  }

  /**
   * Restricts matches to offers updated after last_sent (or last 24h).
   */
  private function applyChangedSince(QueryInterface $query, SearchAlert $alert): void {
    $lastSent = (int) ($alert->get('last_sent')->value ?? 0);
    $threshold = $lastSent > 0 ? $lastSent : (\Drupal::time()->getRequestTime() - 86400);
    $query->addCondition('changed', $threshold, '>');
  }

}
