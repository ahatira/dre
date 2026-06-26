<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\migrate\Plugin\Migration;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Applies runtime migration source overrides for the CRM import pipeline.
 */
final class ImportPipelineMigrationSourceAlterer {

  public function __construct(
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly XmlParseCacheService $xmlParseCache,
  ) {}

  /**
   * Applies pipeline staging URI and XML parse cache overrides.
   */
  public function applyPipelineOverrides(MigrationInterface $migration): void {
    $this->applyStagingUri($migration);
    $this->applyXmlParseCache($migration);
  }

  /**
   * Replaces hardcoded CRM XML paths with the configured staging URI.
   */
  private function applyStagingUri(MigrationInterface $migration): void {
    if (!$migration instanceof Migration) {
      return;
    }

    if (!in_array('ps_crm_import', $migration->getMigrationTags(), TRUE)) {
      return;
    }

    $source = $migration->getSourceConfiguration();
    $plugin = (string) ($source['plugin'] ?? '');
    if ($plugin !== 'ps_crm_offer_xml') {
      return;
    }

    $stagingUri = $this->pathResolver->getStagingUri();
    if (array_key_exists('urls', $source)) {
      $source['urls'] = [$stagingUri];
    }
    if (array_key_exists('files', $source)) {
      $source['files'] = [$stagingUri];
    }
    if (!array_key_exists('urls', $source) && !array_key_exists('files', $source)) {
      $source['urls'] = [$stagingUri];
    }

    $this->replaceSourceConfiguration($migration, $source);
  }

  /**
   * Swaps file/simple_xml/xml fetcher-parser plugins when cache is warm.
   */
  private function applyXmlParseCache(MigrationInterface $migration): void {
    if (!$this->xmlParseCache->isActive() || !$migration instanceof Migration) {
      return;
    }

    $source = $migration->getSourceConfiguration();
    if (($source['plugin'] ?? '') === 'ps_crm_offer_xml') {
      return;
    }

    $changed = FALSE;

    if (($source['data_fetcher_plugin'] ?? '') === 'file') {
      $source['data_fetcher_plugin'] = 'ps_migrate_cached_file';
      $changed = TRUE;
    }

    $parser = (string) ($source['data_parser_plugin'] ?? '');
    if ($parser === 'simple_xml') {
      $source['data_parser_plugin'] = 'ps_migrate_cached_simple_xml';
      $changed = TRUE;
    }
    elseif ($parser === 'xml') {
      $source['data_parser_plugin'] = 'ps_migrate_cached_xml';
      $changed = TRUE;
    }

    if (!$changed) {
      return;
    }

    $this->replaceSourceConfiguration($migration, $source);
  }

  /**
   * Replaces migration source configuration at runtime.
   *
   * @param \Drupal\migrate\Plugin\Migration $migration
   *   Migration plugin instance.
   * @param array<string, mixed> $source
   *   Updated source plugin configuration.
   */
  private function replaceSourceConfiguration(Migration $migration, array $source): void {
    $reflection = new \ReflectionObject($migration);

    $sourceProperty = $reflection->getProperty('source');
    $sourceProperty->setAccessible(TRUE);
    $sourceProperty->setValue($migration, $source);

    if ($reflection->hasProperty('sourcePlugin')) {
      $pluginProperty = $reflection->getProperty('sourcePlugin');
      $pluginProperty->setAccessible(TRUE);
      $pluginProperty->setValue($migration, NULL);
    }
  }

}
