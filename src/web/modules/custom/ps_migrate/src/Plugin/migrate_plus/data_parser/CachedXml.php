<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate_plus\data_parser;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\migrate_plus\Attribute\DataParser;
use Drupal\migrate_plus\DataFetcherPluginManager;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Xml;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Streaming XML parser that reuses the pipeline temp file when available.
 */
#[DataParser(
  id: 'ps_migrate_cached_xml',
  title: new TranslatableMarkup('PS Migrate cached XML')
)]
final class CachedXml extends Xml implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    DataFetcherPluginManager $fetcherPluginManager,
    FileSystemInterface $file_system,
    private readonly XmlParseCacheService $xmlParseCache,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $fetcherPluginManager, $file_system);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migrate_plus.data_fetcher'),
      $container->get('file_system'),
      $container->get('ps_migrate.xml_parse_cache'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function openSourceUrl($url): bool {
    if ($this->xmlParseCache->isActive() && $this->xmlParseCache->matchesUrl((string) $url)) {
      $this->reader->close();
      libxml_clear_errors();
      $this->tempFileName = $this->xmlParseCache->getTempFilePath();
      return $this->reader->open($this->tempFileName, NULL, \LIBXML_NOWARNING);
    }

    return parent::openSourceUrl($url);
  }

}
