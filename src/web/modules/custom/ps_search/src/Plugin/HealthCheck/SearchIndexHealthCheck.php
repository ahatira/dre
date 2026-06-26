<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\HealthCheck;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Drupal\ps_core\Plugin\HealthCheck\HealthCheckBase;
use Drupal\ps_search\Service\SearchSolrCircuitBreaker;
use Drupal\search_api\Entity\Index;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks Search API offers index and Solr server availability.
 *
 * @HealthCheck(
 *   id = "search_solr",
 *   label = @Translation("Search index & Solr"),
 *   group = "search",
 *   weight = 0,
 * )
 */
final class SearchIndexHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  private const INDEX_ID = 'offers';

  private const SERVER_ID = 'ps_solr';

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly SearchSolrCircuitBreaker $circuitBreaker,
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
      $container->get('config.factory'),
      $container->get('ps_search.solr_circuit_breaker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    $index = Index::load(self::INDEX_ID);
    if ($index === NULL) {
      return new HealthCheckResult(
        HealthCheckStatus::FAIL,
        (string) $this->t('Search API index "@index" is not configured.', [
          '@index' => self::INDEX_ID,
        ]),
        [],
        ['make index-solr'],
      );
    }

    $connector = $this->configFactory->get('search_api.server.' . self::SERVER_ID)
      ->get('backend_config.connector_config') ?? [];
    $host = trim((string) (is_array($connector) ? ($connector['host'] ?? '') : ''));
    $core = trim((string) (is_array($connector) ? ($connector['core'] ?? '') : ''));

    if ($host === '' || $core === '') {
      return new HealthCheckResult(
        HealthCheckStatus::FAIL,
        (string) $this->t('Solr server "@server" is missing host or core configuration.', [
          '@server' => self::SERVER_ID,
        ]),
        [],
        ['make index-solr'],
        (string) $this->t('Index @index status: @status.', [
          '@index' => self::INDEX_ID,
          '@status' => $index->status() ? 'enabled' : 'disabled',
        ]),
      );
    }

    if ($this->circuitBreaker->isUnavailable()) {
      return new HealthCheckResult(
        HealthCheckStatus::WARNING,
        (string) $this->t('Solr circuit is open — recent query failures are being skipped.'),
        [],
        [
          'make index-solr',
          'cd src && vendor/bin/drush @ps.fr search-api:status',
        ],
        (string) $this->t('Server @host / core @core.', [
          '@host' => $host,
          '@core' => $core,
        ]),
      );
    }

    $server = $index->getServerInstance();
    $available = $server !== NULL && $server->isAvailable();
    $tracked = $index->getTrackerInstance()->getTotalItemsCount();
    $indexed = $index->getTrackerInstance()->getIndexedItemsCount();

    if (!$available) {
      return new HealthCheckResult(
        HealthCheckStatus::FAIL,
        (string) $this->t('Solr server is configured but not reachable.'),
        [],
        [
          'make index-solr',
          'cd src && vendor/bin/drush @ps.fr search-api:reset-tracker offers',
          'cd src && vendor/bin/drush @ps.fr search-api:index offers',
        ],
        (string) $this->t('@indexed / @tracked items indexed on @host/@core.', [
          '@indexed' => $indexed,
          '@tracked' => $tracked,
          '@host' => $host,
          '@core' => $core,
        ]),
      );
    }

    $status = HealthCheckStatus::OK;
    $message = (string) $this->t('Solr reachable — @indexed / @tracked offers indexed.', [
      '@indexed' => $indexed,
      '@tracked' => $tracked,
    ]);

    if ($tracked > 0 && $indexed < $tracked) {
      $status = HealthCheckStatus::WARNING;
      $message = (string) $this->t('Solr reachable but index is incomplete (@indexed / @tracked).', [
        '@indexed' => $indexed,
        '@tracked' => $tracked,
      ]);
    }

    return new HealthCheckResult(
      $status,
      $message,
      [],
      [
        'make index-solr',
        'cd src && vendor/bin/drush @ps.fr search-api:status',
      ],
      (string) $this->t('Server @host · core @core.', [
        '@host' => $host,
        '@core' => $core,
      ]),
    );
  }

}
