<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate_plus\data_fetcher;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\Attribute\DataFetcher;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\File;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * File fetcher that reuses the pipeline XML parse cache when available.
 */
#[DataFetcher(
  id: 'ps_migrate_cached_file',
  title: new TranslatableMarkup('PS Migrate cached file')
)]
final class CachedFile extends File implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly XmlParseCacheService $xmlParseCache,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.xml_parse_cache'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse($url): ResponseInterface {
    if ($this->xmlParseCache->isActive() && $this->xmlParseCache->matchesUrl((string) $url)) {
      return new Response(200, [], $this->xmlParseCache->getRawContent((string) $url));
    }

    return parent::getResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseContent(string $url): string {
    if ($this->xmlParseCache->isActive() && $this->xmlParseCache->matchesUrl($url)) {
      return $this->xmlParseCache->getRawContent($url);
    }

    $response = @file_get_contents($url);
    if ($response === FALSE) {
      throw new MigrateException('file parser plugin: could not retrieve data from ' . $url);
    }

    return $response;
  }

}
