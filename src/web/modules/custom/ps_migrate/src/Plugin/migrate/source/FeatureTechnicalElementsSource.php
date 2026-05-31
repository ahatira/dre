<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\ps_migrate\Service\FeatureTechnicalElementSourceLoader;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Drupal\ps_migrate\ValueObject\FeatureTechnicalElement;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for CRM technical elements used by feature migrations.
 */
#[MigrateSource(id: 'ps_feature_technical_elements')]
final class FeatureTechnicalElementsSource extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  /**
    * @var \Drupal\ps_migrate\Service\FeatureTechnicalElementSourceLoader
   */
    private FeatureTechnicalElementSourceLoader $sourceLoader;

  /**
   * @var \Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder
   */
  private FeatureMigrationKeyBuilder $keyBuilder;

  /**
   * @var \Drupal\ps_migrate\Service\FeatureTechnicalElementValidator
   */
  private FeatureTechnicalElementValidator $validator;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    FeatureTechnicalElementSourceLoader $sourceLoader,
    FeatureMigrationKeyBuilder $keyBuilder,
    FeatureTechnicalElementValidator $validator,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->sourceLoader = $sourceLoader;
    $this->keyBuilder = $keyBuilder;
    $this->validator = $validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('ps_migrate.feature_technical_element_source_loader'),
      $container->get('ps_migrate.feature_migration_key_builder'),
      $container->get('ps_migrate.feature_technical_element_validator'),
    );
  }

  /**
   * Returns a string representation of configured XML files.
   */
  public function __toString(): string {
    $files = $this->configuration['files'] ?? [];
    if (!is_array($files)) {
      $files = [$files];
    }

    $files = array_values(array_filter(array_map('trim', $files), static fn(string $file): bool => $file !== ''));
    return $files === [] ? 'no-files-configured' : implode(', ', $files);
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator(): \Iterator {
    $mode = (string) ($this->configuration['mode'] ?? 'definitions');
    $rows = [];

    foreach ($this->loadElementsFromFiles() as $element) {
      if ($mode === 'groups') {
        $rows = $this->appendGroupRow($rows, $element);
        continue;
      }

      $definition_row = $this->buildDefinitionRow($element);
      if ($definition_row === NULL) {
        continue;
      }
      if (isset($rows[$definition_row['definition_id']])) {
        continue;
      }
      $rows[$definition_row['definition_id']] = $definition_row;
    }

    return new \ArrayIterator(array_values($rows));
  }

  /**
   * {@inheritdoc}
   */
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
   * Loads all technical elements from configured XML files.
   *
   * @return array<int, \Drupal\ps_migrate\ValueObject\FeatureTechnicalElement>
   *   Parsed elements.
   */
  private function loadElementsFromFiles(): array {
    $files = $this->configuration['files'] ?? [];
    if (!is_array($files)) {
      $files = [$files];
    }

    $elements = [];
    foreach ($files as $file) {
      $file = trim((string) $file);
      if ($file === '') {
        continue;
      }

      foreach ($this->sourceLoader->loadFromFile($file) as $element) {
        $elements[] = $element;
      }
    }

    return $elements;
  }

  /**
   * Appends a unique group row for the given technical element.
   */
  private function appendGroupRow(array $rows, FeatureTechnicalElement $element): array {
    $group_id = $this->keyBuilder->buildGroupId($element->getGroupCode());
    if ($group_id === '' || isset($rows[$group_id])) {
      return $rows;
    }

    $rows[$group_id] = [
      'group_id' => $group_id,
      'group_code' => $element->getGroupCode(),
      'label' => $element->getGroupCode(),
      'description' => $element->getGroupCode(),
      'weight' => $element->getSourceIndex(),
      'status' => 1,
    ];

    return $rows;
  }

  /**
   * Builds a definition row from a technical element.
   */
  private function buildDefinitionRow(FeatureTechnicalElement $element): ?array {
    $group_id = $this->keyBuilder->buildGroupId($element->getGroupCode());
    $definition_id = $this->keyBuilder->buildDefinitionId($element->getGroupCode(), $element->getFeatureCode());
    $payload_defaults = [];

    if ($element->getUnit() !== NULL) {
      $payload_defaults['unit'] = $element->getUnit();
    }

    $record = [
      'definition_id' => $definition_id,
      'group_id' => $group_id,
      'group_code' => $element->getGroupCode(),
      'feature_code' => $element->getFeatureCode(),
      'label' => $element->getLabel(),
      'description' => $element->getComplement() ?? $element->getLabel(),
      'type_driver' => $this->guessTypeDriver($element),
      'weight' => $element->getSourceIndex(),
      'status' => 1,
      'payload_defaults' => $payload_defaults,
      'required_asset_types' => [],
      'source_index' => $element->getSourceIndex(),
    ];

    $validation = $this->validator->validate([
      'group_code' => $record['group_code'],
      'feature_code' => $record['feature_code'],
      'definition_id' => $record['definition_id'],
      'type_driver' => $record['type_driver'],
      'payload' => [
        'value' => $element->getValue(),
        'unit' => $element->getUnit(),
        'complement' => $element->getComplement(),
      ],
    ]);
    if ($validation['errors'] !== []) {
      return NULL;
    }

    return $record;
  }

  /**
   * Guesses a type driver from the element payload shape.
   */
  private function guessTypeDriver(FeatureTechnicalElement $element): string {
    $value = $element->getValue();
    $unit = $element->getUnit();

    if ($value === NULL && $unit === NULL) {
      return 'flag';
    }

    if ($value !== NULL && is_numeric(str_replace(',', '.', $value))) {
      return 'numeric';
    }

    if ($value !== NULL && preg_match('/^(yes|no|true|false|oui|non|0|1)$/i', $value) === 1) {
      return 'yes_no';
    }

    return 'text';
  }

}