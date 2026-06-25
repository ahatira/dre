<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\ps_migrate\Service\OfferTranslationRowProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Expands one offer row into one row per target translation language.
 *
 * @deprecated in ps_migrate:8.x-1.x and is removed from ps_migrate:9.x. Use
 *   ps_crm_offer_xml with mode offer_translations instead.
 */
#[MigrateSource(id: 'ps_offer_translation_targets')]
final class OfferTranslationTargetsSource extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    private readonly OfferTranslationRowProvider $rowProvider,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('ps_migrate.offer_translation_row_provider'),
    );
  }

  public function fields(): array {
    return [
      'business_id' => (string) $this->t('Business ID'),
      'offer_xml_node' => (string) $this->t('Full offer XML node'),
      'technical_elements' => (string) $this->t('Technical elements node'),
      'operation_code' => (string) $this->t('Operation code'),
      'type_code' => (string) $this->t('Asset type code'),
      'address_city' => (string) $this->t('City'),
      'all_surface_values' => (string) $this->t('All surface values'),
      'address_country' => (string) $this->t('Country ISO code'),
      'source_xml_language' => (string) $this->t('Canonical XML source language'),
      'source_langcode' => (string) $this->t('Canonical Drupal source language'),
      'target_xml_language' => (string) $this->t('Target XML language'),
      'target_langcode' => (string) $this->t('Target Drupal language'),
    ];
  }

  public function getIds(): array {
    return [
      'business_id' => ['type' => 'string'],
      'target_langcode' => ['type' => 'string'],
    ];
  }

  public function __toString(): string {
    $urls = $this->configuredUrls();
    return $urls === [] ? 'no-urls-configured' : implode(', ', $urls);
  }

  protected function initializeIterator(): \Iterator {
    return new \ArrayIterator($this->rowProvider->buildRows($this->configuredUrls()));
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
