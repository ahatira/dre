<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Query\QueryInterface;
use Psr\Log\LoggerInterface;

/**
 * Resolves offer node IDs for homepage featured carousel dynamic fallback.
 *
 * Prefers Search API (Solr index "offers"); falls back to entity query when
 * the index is unavailable or returns no results.
 */
final class OfferFeaturedOffersResolver {

  private const INDEX_ID = 'offers';

  private const MAX_LIMIT = 12;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Returns published offer node IDs for dynamic carousel content.
   *
   * @return list<int>
   *   Offer node IDs sorted by most recently changed.
   */
  public function resolveDynamicNids(?int $limit = NULL): array {
    $limit = max(1, min(self::MAX_LIMIT, $limit ?? 6));
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

    $nids = $this->resolveFromSearchIndex($limit, $langcode);
    if ($nids !== []) {
      return $nids;
    }

    return $this->resolveFromEntityStorage($limit);
  }

  /**
   * Loads offer node IDs from the Search API offers index.
   *
   * @return list<int>
   *   Matching offer node IDs, or empty when the query fails.
   */
  private function resolveFromSearchIndex(int $limit, string $langcode): array {
    $index = Index::load(self::INDEX_ID);
    if ($index === NULL) {
      return [];
    }

    try {
      $query = $index->query();
      $query->addCondition('status', TRUE);
      $query->addCondition('langcode', $langcode);
      $query->sort('changed', QueryInterface::SORT_DESC);
      $query->range(0, $limit);

      $nids = [];
      foreach ($query->execute()->getResultItems() as $item) {
        $nid = (int) $item->getOriginalObject()?->getValue()?->id();
        if ($nid > 0) {
          $nids[] = $nid;
        }
      }

      return $nids;
    }
    catch (\Throwable $exception) {
      $this->logger->warning('Homepage featured offers Solr query failed: @message', [
        '@message' => $exception->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Loads offer node IDs from entity storage when Solr is unavailable.
   *
   * @return list<int>
   *   Published offer node IDs sorted by changed date.
   */
  private function resolveFromEntityStorage(int $limit): array {
    $ids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'offer')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('changed', 'DESC')
      ->range(0, $limit)
      ->execute();

    return array_values(array_map('intval', $ids));
  }

}
