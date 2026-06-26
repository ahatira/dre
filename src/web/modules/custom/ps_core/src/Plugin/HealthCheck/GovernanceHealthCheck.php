<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\HealthCheck;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks recent governance conflicts and protected entity counts.
 *
 * @HealthCheck(
 *   id = "governance",
 *   label = @Translation("Import governance"),
 *   group = "governance",
 *   weight = 0,
 * )
 */
final class GovernanceHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  private const CONFLICT_LOOKBACK = 86400;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly Connection $database,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    $conflicts = (int) $this->database->select('watchdog', 'w')
      ->condition('type', 'ps_core')
      ->condition('variables', '%conflict_detected%', 'LIKE')
      ->condition('timestamp', time() - self::CONFLICT_LOOKBACK, '>=')
      ->countQuery()
      ->execute()
      ->fetchField();

    $lockedOffers = $this->countLockedEntities('node', 'offer');
    $lockedMedia = $this->countLockedEntities('media');

    $status = HealthCheckStatus::OK;
    if ($conflicts > 0) {
      $status = HealthCheckStatus::WARNING;
    }

    $message = (string) $this->t('@conflicts governance conflict(s) in the last 24 hours.', [
      '@conflicts' => $conflicts,
    ]);
    $detail = (string) $this->t('@offers offer(s) and @media media item(s) currently have internal lock enabled.', [
      '@offers' => $lockedOffers,
      '@media' => $lockedMedia,
    ]);

    return new HealthCheckResult(
      $status,
      $message,
      [
        [
          'title' => (string) $this->t('Governance settings'),
          'route' => 'ps_core.governance',
        ],
        [
          'title' => (string) $this->t('Recent log messages'),
          'route' => 'dblog.overview',
        ],
      ],
      [
        'cd src && vendor/bin/drush @ps.fr watchdog:show --type=ps_core --count=20',
      ],
      $detail,
    );
  }

  private function countLockedEntities(string $entityTypeId, ?string $bundle = NULL): int {
    if (!$this->entityTypeManager->hasDefinition($entityTypeId)) {
      return 0;
    }

    $storage = $this->entityTypeManager->getStorage($entityTypeId);
    if (!$storage->getEntityType()->hasKey('published')) {
      // Still try if field exists on bundle.
    }

    try {
      $query = $storage->getQuery()
        ->accessCheck(FALSE)
        ->condition('field_internal_lock', 1);
      if ($bundle !== NULL) {
        $query->condition($storage->getEntityType()->getKey('bundle') ?? 'type', $bundle);
      }
      return (int) $query->count()->execute();
    }
    catch (\Throwable) {
      return 0;
    }
  }

}
