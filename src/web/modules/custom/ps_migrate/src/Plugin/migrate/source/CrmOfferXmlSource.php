<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader;
use Drupal\ps_migrate\Service\CrmOfferXmlMode;
use Drupal\ps_migrate\Service\CrmOfferXmlXpathRowBuilder;
use Drupal\ps_migrate\Service\FeatureTechnicalElementRowProvider;
use Drupal\ps_migrate\Service\OfferTranslationRowProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Unified CRM offer XML source plugin with cache-aware extraction modes.
 */
#[MigrateSource(id: 'ps_crm_offer_xml')]
final class CrmOfferXmlSource extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    private readonly CrmOfferXmlDocumentLoader $documentLoader,
    private readonly CrmOfferXmlXpathRowBuilder $rowBuilder,
    private readonly FeatureTechnicalElementRowProvider $featureRowProvider,
    private readonly OfferTranslationRowProvider $translationRowProvider,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('ps_migrate.crm_offer_xml_document_loader'),
      $container->get('ps_migrate.crm_offer_xml_xpath_row_builder'),
      $container->get('ps_migrate.feature_technical_element_row_provider'),
      $container->get('ps_migrate.offer_translation_row_provider'),
    );
  }

  public function __toString(): string {
    $urls = $this->configuredUrls();
    return $urls === [] ? 'no-urls-configured' : implode(', ', $urls);
  }

  protected function initializeIterator(): \Iterator {
    $mode = (string) ($this->configuration['mode'] ?? '');
    if ($mode === '') {
      throw new MigrateException('CRM offer XML source requires a mode.');
    }

    $urls = $this->configuredUrls();
    if ($urls === []) {
      throw new MigrateException('CRM offer XML source requires at least one URL.');
    }

    if (CrmOfferXmlMode::isDelegateMode($mode)) {
      return new \ArrayIterator(match ($mode) {
        CrmOfferXmlMode::FEATURE_GROUPS => $this->featureRowProvider->buildGroupRows($urls),
        CrmOfferXmlMode::FEATURE_DEFINITIONS => $this->featureRowProvider->buildDefinitionRows($urls),
        CrmOfferXmlMode::OFFER_TRANSLATIONS => $this->translationRowProvider->buildRows($urls),
        default => [],
      });
    }

    $itemSelector = CrmOfferXmlMode::itemSelector($mode);
    if ($itemSelector === NULL) {
      throw new MigrateException(sprintf('Unknown CRM offer XML source mode: %s', $mode));
    }

    $fields = $this->configuration['fields'] ?? [];
    if (!is_array($fields)) {
      $fields = [];
    }

    $namespaces = $this->configuration['namespaces'] ?? [];
    if (!is_array($namespaces)) {
      $namespaces = [];
    }

    $rows = [];
    foreach ($urls as $url) {
      foreach ($this->documentLoader->selectItems($url, $itemSelector, $namespaces) as $item) {
        if (!$item instanceof \SimpleXMLElement) {
          continue;
        }

        $row = $this->rowBuilder->buildRow($item, $fields);
        if ($row !== []) {
          $rows[] = $row;
        }
      }
    }

    return new \ArrayIterator($rows);
  }

  /**
   * @return string[]
   */
  private function configuredUrls(): array {
    $urls = $this->configuration['urls'] ?? ($this->configuration['files'] ?? []);
    if (!is_array($urls)) {
      $urls = [$urls];
    }

    $urls = array_map(static fn(mixed $url): string => trim((string) $url), $urls);
    return array_values(array_filter($urls, static fn(string $url): bool => $url !== ''));
  }

}
