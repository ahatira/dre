<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate_plus\data_parser;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\migrate_plus\Attribute\DataParser;
use Drupal\migrate_plus\DataFetcherPluginManager;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\SimpleXml;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SimpleXML parser that reuses the pipeline XML parse cache when available.
 */
#[DataParser(
  id: 'ps_migrate_cached_simple_xml',
  title: new TranslatableMarkup('PS Migrate cached Simple XML')
)]
final class CachedSimpleXml extends SimpleXml implements ContainerFactoryPluginInterface {

  /**
   * @var \SimpleXMLElement[]|bool
   */
  protected $matches = [];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    DataFetcherPluginManager $fetcherPluginManager,
    private readonly XmlParseCacheService $xmlParseCache,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $fetcherPluginManager);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    libxml_use_internal_errors(TRUE);
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migrate_plus.data_fetcher'),
      $container->get('ps_migrate.xml_parse_cache'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function openSourceUrl($url): bool {
    if ($this->xmlParseCache->isActive() && $this->xmlParseCache->matchesUrl((string) $url)) {
      libxml_clear_errors();

      $xml = $this->xmlParseCache->getDocument();
      $this->registerNamespaces($xml);
      $xpath = $this->configuration['item_selector'];
      $this->matches = $xml->xpath($xpath) ?: [];
      return TRUE;
    }

    return parent::openSourceUrl($url);
  }

}
