<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;

/**
 * Runs all health check plugins with short-lived caching.
 */
final class HealthCheckCollector {

  private const CACHE_ID = 'ps_core:health:results';

  private const CACHE_TTL = 60;

  public function __construct(
    private readonly HealthCheckManager $healthCheckManager,
    private readonly CacheBackendInterface $cache,
  ) {}

  /**
   * Returns grouped health check results keyed by group id.
   *
   * @return array<string, list<array{
   *   id: string,
   *   label: string,
   *   weight: int,
   *   result: \Drupal\ps_core\HealthCheck\HealthCheckResult
   * }>>
   */
  public function collectGroupedResults(bool $refresh = FALSE): array {
    if (!$refresh) {
      $cached = $this->cache->get(self::CACHE_ID);
      if ($cached !== FALSE && is_array($cached->data)) {
        return $this->hydrateGroupedResults($cached->data);
      }
    }

    $grouped = [];
    $definitions = $this->healthCheckManager->getDefinitions();
    uasort(
      $definitions,
      static fn(array $a, array $b): int => ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0),
    );

    foreach (array_keys($definitions) as $pluginId) {
      /** @var \Drupal\ps_core\Plugin\HealthCheck\HealthCheckInterface $plugin */
      $plugin = $this->healthCheckManager->createInstance($pluginId);
      $definition = $definitions[$pluginId];
      $group = $plugin->getGroup();
      $grouped[$group][] = [
        'id' => $pluginId,
        'label' => (string) $definition['label'],
        'weight' => (int) ($definition['weight'] ?? 0),
        'result' => $plugin->run(),
      ];
    }

    foreach ($grouped as &$checks) {
      usort(
        $checks,
        static fn(array $a, array $b): int => $a['weight'] <=> $b['weight'],
      );
    }
    unset($checks);

    $this->cache->set(
      self::CACHE_ID,
      $this->serializeGroupedResults($grouped),
      time() + self::CACHE_TTL,
      ['ps_core:health'],
    );

    return $grouped;
  }

  /**
   * @param array<string, list<array<string, mixed>>> $grouped
   *
   * @return array<string, list<array<string, mixed>>>
   */
  private function serializeGroupedResults(array $grouped): array {
    $serialized = [];
    foreach ($grouped as $group => $checks) {
      foreach ($checks as $check) {
        /** @var \Drupal\ps_core\HealthCheck\HealthCheckResult $result */
        $result = $check['result'];
        $serialized[$group][] = [
          'id' => $check['id'],
          'label' => $check['label'],
          'weight' => $check['weight'],
          'result' => [
            'status' => $result->status,
            'message' => $result->message,
            'links' => $result->links,
            'commands' => $result->commands,
            'detail' => $result->detail,
          ],
        ];
      }
    }
    return $serialized;
  }

  /**
   * @param array<string, list<array<string, mixed>>> $serialized
   *
   * @return array<string, list<array<string, mixed>>>
   */
  private function hydrateGroupedResults(array $serialized): array {
    $grouped = [];
    foreach ($serialized as $group => $checks) {
      foreach ($checks as $check) {
        $grouped[$group][] = [
          'id' => $check['id'],
          'label' => $check['label'],
          'weight' => $check['weight'],
          'result' => new HealthCheckResult(
            $check['result']['status'],
            $check['result']['message'],
            $check['result']['links'] ?? [],
            $check['result']['commands'] ?? [],
            $check['result']['detail'] ?? NULL,
          ),
        ];
      }
    }
    return $grouped;
  }

}
