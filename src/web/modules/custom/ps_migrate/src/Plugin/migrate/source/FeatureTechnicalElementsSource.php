<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\ps_migrate\Service\FeatureTechnicalElementRowProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for CRM technical elements used by feature migrations.
 *
 * @deprecated in ps_migrate:8.x-1.x and is removed from ps_migrate:9.x. Use
 *   ps_crm_offer_xml with mode feature_groups or feature_definitions instead.
 */
#[MigrateSource(id: 'ps_feature_technical_elements')]
final class FeatureTechnicalElementsSource extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    private readonly FeatureTechnicalElementRowProvider $rowProvider,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('ps_migrate.feature_technical_element_row_provider'),
    );
  }

  public function __toString(): string {
    $files = $this->configuredFiles();
    return $files === [] ? 'no-files-configured' : implode(', ', $files);
  }

  protected function initializeIterator(): \Iterator {
    $mode = (string) ($this->configuration['mode'] ?? 'definitions');
    $files = $this->configuredFiles();

    $rows = $mode === 'groups'
      ? $this->rowProvider->buildGroupRows($files)
      : $this->rowProvider->buildDefinitionRows($files);

    return new \ArrayIterator($rows);
  }

  public function fields(): array {
    return [
      'group_id' => $this->t('Feature group ID'),
      'group_code' => $this->t('Feature group code'),
      'definition_id' => $this->t('Feature definition ID'),
      'feature_code' => $this->t('Feature code'),
      'label' => $this->t('Label'),
      'type_driver' => $this->t('Type driver'),
      'weight' => $this->t('Weight'),
      'status' => $this->t('Status'),
      'description' => $this->t('Description'),
      'payload_defaults' => $this->t('Payload defaults'),
      'required_asset_types' => $this->t('Required asset types'),
      'source_index' => $this->t('Source index'),
    ];
  }

  public function getIds(): array {
    $mode = (string) ($this->configuration['mode'] ?? 'definitions');

    if ($mode === 'groups') {
      return [
        'group_id' => [
          'type' => 'string',
        ],
      ];
    }

    return [
      'definition_id' => [
        'type' => 'string',
      ],
    ];
  }

  /**
   * @return string[]
   */
  private function configuredFiles(): array {
    $files = $this->configuration['files'] ?? ($this->configuration['urls'] ?? []);
    if (!is_array($files)) {
      $files = [$files];
    }

    $files = array_map(static fn(mixed $file): string => trim((string) $file), $files);
    return array_values(array_filter($files, static fn(string $file): bool => $file !== ''));
  }

}
