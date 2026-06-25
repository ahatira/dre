<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\migrate\Plugin\Migration;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Applies runtime source plugin swaps for XML parse cache (Phase A).
 */
final class ImportPipelineMigrationSourceAlterer {

  public function __construct(
    private readonly XmlParseCacheService $xmlParseCache,
  ) {}

  /**
   * Swaps file/simple_xml/xml fetcher-parser plugins when cache is warm.
   */
  public function applyXmlParseCache(MigrationInterface $migration): void {
    if (!$this->xmlParseCache->isActive() || !$migration instanceof Migration) {
      return;
    }

    $source = $migration->getSourceConfiguration();
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
   * @param array<string, mixed> $source
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
