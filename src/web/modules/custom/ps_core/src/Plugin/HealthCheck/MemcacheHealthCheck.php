<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\HealthCheck;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks Memcache connectivity when the module is enabled.
 *
 * @HealthCheck(
 *   id = "memcache",
 *   label = @Translation("Memcache"),
 *   group = "cache",
 *   weight = 0,
 * )
 */
final class MemcacheHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly CacheBackendInterface $cache,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cache.default'),
      $container->get('module_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    if (!$this->moduleHandler->moduleExists('memcache')) {
      return new HealthCheckResult(
        HealthCheckStatus::INFO,
        (string) $this->t('Memcache module is not enabled — using default cache backend.'),
        [],
        [],
      );
    }

    $probeKey = 'ps_core:health:memcache_probe:' . time();
    $probeValue = 'ok';
    try {
      $this->cache->set($probeKey, $probeValue, time() + 30);
      $cached = $this->cache->get($probeKey);
      $this->cache->delete($probeKey);
    }
    catch (\Throwable $exception) {
      return new HealthCheckResult(
        HealthCheckStatus::FAIL,
        (string) $this->t('Memcache read/write probe failed.'),
        [
          [
            'title' => (string) $this->t('Memcache statistics'),
            'route' => 'memcache.admin',
          ],
        ],
        ['make drush fr cr'],
        $exception->getMessage(),
      );
    }

    if ($cached === FALSE || ($cached->data ?? NULL) !== $probeValue) {
      return new HealthCheckResult(
        HealthCheckStatus::WARNING,
        (string) $this->t('Cache backend responded but the probe value did not round-trip.'),
        [
          [
            'title' => (string) $this->t('Memcache statistics'),
            'route' => 'memcache.admin',
          ],
        ],
        ['make drush fr cr'],
      );
    }

    $backendClass = $this->cache::class;
    return new HealthCheckResult(
      HealthCheckStatus::OK,
      (string) $this->t('Default cache backend probe succeeded (@backend).', [
        '@backend' => $backendClass,
      ]),
      [
        [
          'title' => (string) $this->t('Memcache statistics'),
          'route' => 'memcache.admin',
        ],
      ],
      ['make drush fr cr'],
    );
  }

}
