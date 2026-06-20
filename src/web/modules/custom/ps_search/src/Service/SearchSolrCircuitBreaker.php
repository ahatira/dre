<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
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
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Whether Solr queries should be skipped for this request.
   */
  public function isUnavailable(): bool {
    if (!$this->isConfigured()) {
      return TRUE;
    }

    if (self::$failedThisRequest) {
      return TRUE;
    }

    return $this->isCircuitOpenInCache();
  }

  /**
   * Whether the Search API Solr connector has host and core configured.
   */
  private function isConfigured(): bool {
    $connector = $this->configFactory->get('search_api.server.ps_solr')
      ->get('backend_config.connector_config') ?? [];
    if (!is_array($connector)) {
      return FALSE;
    }

    $host = trim((string) ($connector['host'] ?? ''));
    $core = trim((string) ($connector['core'] ?? ''));
    return $host !== '' && $core !== '';
  }

  /**
   * Records a Solr failure and opens the circuit for subsequent calls.
   */
  public function recordFailure(\Throwable $exception): void {
    self::$failedThisRequest = TRUE;
    try {
      $this->cache->set(self::CACHE_KEY, TRUE, time() + self::CACHE_TTL);
    }
    catch (\Throwable $cacheException) {
      $this->logger->notice('Could not persist Solr circuit state: @message', [
        '@message' => $cacheException->getMessage(),
      ]);
    }
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
    try {
      $this->cache->delete(self::CACHE_KEY);
    }
    catch (\Throwable) {
      // Per-request success is enough when cache backend is down.
    }
  }

  /**
   * Whether a previous failure opened the circuit in cache.
   */
  private function isCircuitOpenInCache(): bool {
    try {
      return $this->cache->get(self::CACHE_KEY) !== FALSE;
    }
    catch (\Throwable $exception) {
      $this->logger->notice('Could not read Solr circuit state: @message', [
        '@message' => $exception->getMessage(),
      ]);
      return self::$failedThisRequest;
    }
  }

}
