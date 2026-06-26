<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\HealthCheck;

use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks core Drupal infrastructure dependencies.
 *
 * @HealthCheck(
 *   id = "drupal_infra",
 *   label = @Translation("Drupal infrastructure"),
 *   group = "infra",
 *   weight = 0,
 * )
 */
final class DrupalInfraHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly Connection $database,
    private readonly StateInterface $state,
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
      $container->get('database'),
      $container->get('state'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    try {
      $this->database->query('SELECT 1')->fetchField();
    }
    catch (\Throwable $exception) {
      return new HealthCheckResult(
        HealthCheckStatus::FAIL,
        (string) $this->t('Database connection failed.'),
        [
          [
            'title' => (string) $this->t('Status report'),
            'route' => 'system.status',
          ],
        ],
        ['cd src && vendor/bin/drush @ps.fr sql:query "SELECT 1"'],
        $exception->getMessage(),
      );
    }

    $cronLast = (int) $this->state->get('system.cron_last', 0);
    $cronAgeHours = $cronLast > 0
      ? (int) floor((time() - $cronLast) / 3600)
      : NULL;

    $status = HealthCheckStatus::OK;
    $message = (string) $this->t('Database reachable. PHP @version.', [
      '@version' => PHP_VERSION,
    ]);
    $detail = NULL;

    if ($cronLast === 0) {
      $status = HealthCheckStatus::WARNING;
      $message = (string) $this->t('Database reachable but cron has never run on this site.');
      $detail = (string) $this->t('PHP @version.', ['@version' => PHP_VERSION]);
    }
    elseif ($cronAgeHours !== NULL && $cronAgeHours > 24) {
      $status = HealthCheckStatus::WARNING;
      $message = (string) $this->t('Database reachable but cron last ran @hours hours ago.', [
        '@hours' => $cronAgeHours,
      ]);
      $detail = (string) $this->t('PHP @version.', ['@version' => PHP_VERSION]);
    }

    return new HealthCheckResult(
      $status,
      $message,
      [
        [
          'title' => (string) $this->t('Status report'),
          'route' => 'system.status',
        ],
        [
          'title' => (string) $this->t('Cron settings'),
          'route' => 'system.cron_settings',
        ],
      ],
      [
        'make drush fr core:cron',
        'cd src && vendor/bin/drush @ps.fr core:cron',
      ],
      $detail,
    );
  }

}
