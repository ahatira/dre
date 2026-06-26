<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Resolves CRM import migration execution order from pipeline settings.
 */
final class ImportPipelineMigrationOrderResolver {

  /**
   * Default full pipeline order (dependencies before offers).
   *
   * @var list<string>
   */
  public const DEFAULT_FULL_ORDER = [
    'ps_agent_avatar_file_from_xml',
    'ps_agent_from_xml',
    'ps_feature_groups_from_xml',
    'ps_feature_definitions_from_xml',
    'ps_file_from_xml',
    'ps_media_from_xml',
    'ps_media_virtual_tour_from_xml',
    'ps_surface_division_from_xml',
    'ps_offer_from_xml',
    'ps_offer_translations_from_xml',
  ];

  /**
   * Default delta pipeline order.
   *
   * @var list<string>
   */
  public const DEFAULT_DELTA_ORDER = [
    'ps_offer_from_xml',
    'ps_offer_translations_from_xml',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly MigrationPluginManagerInterface $migrationPluginManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Returns migration IDs to execute for the given import mode.
   *
   * @return list<string>
   *   Ordered migration plugin IDs.
   */
  public function getOrder(string $mode): array {
    $configKey = $mode === 'delta' ? 'migration_order_delta' : 'migration_order_full';
    $defaults = $mode === 'delta' ? self::DEFAULT_DELTA_ORDER : self::DEFAULT_FULL_ORDER;
    $configured = $this->configFactory->get('ps_migrate.import_pipeline_settings')->get($configKey);

    if (!is_array($configured) || $configured === []) {
      return $defaults;
    }

    $order = [];
    foreach ($configured as $migrationId) {
      $migrationId = trim((string) $migrationId);
      if ($migrationId === '') {
        continue;
      }
      if (!$this->migrationPluginManager->hasDefinition($migrationId)) {
        $this->logger->warning('Ignoring unknown migration ID in pipeline order config: @id', [
          '@id' => $migrationId,
        ]);
        continue;
      }
      $order[] = $migrationId;
    }

    return $order === [] ? $defaults : $order;
  }

}
