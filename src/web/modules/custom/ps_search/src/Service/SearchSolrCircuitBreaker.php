<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Psr\Log\LoggerInterface;

/**
 * Skips Solr queries after a recent failure to keep pages responsive.
 *
 * Uses a per-request flag plus a short-lived cache entry so repeated Solr
 * calls on the same page (counts, view, markers) do not each wait for timeouts.
 */
final class SearchSolrCircuitBreaker {

  private const CACHE_KEY = 'ps_search:solr_unavailable';

  private const CACHE_TTL = 60;

  /**
   * Whether Solr already failed during this PHP request.
   */
  private static bool $failedThisRequest = FALSE;

  public function __construct(
    private readonly CacheBackendInterface $cache,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Whether Solr queries should be skipped for this request.
   */
  public function isUnavailable(): bool {
    if (self::$failedThisRequest) {
      return TRUE;
    }

    return $this->cache->get(self::CACHE_KEY) !== FALSE;
  }

  /**
   * Records a Solr failure and opens the circuit for subsequent calls.
   */
  public function recordFailure(\Throwable $exception): void {
    self::$failedThisRequest = TRUE;
    $this->cache->set(self::CACHE_KEY, TRUE, time() + self::CACHE_TTL);
    $this->logger->warning('Solr unavailable; skipping further search queries for @seconds s: @message', [
      '@seconds' => self::CACHE_TTL,
      '@message' => $exception->getMessage(),
    ]);
  }

  /**
   * Clears the circuit after a successful Solr query.
   */
  public function recordSuccess(): void {
    self::$failedThisRequest = FALSE;
    $this->cache->delete(self::CACHE_KEY);
  }

}
