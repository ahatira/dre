<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\ps_dictionary\Service\DictionaryImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Keep existing dictionary labels, fallback to source value/code for new ones.
 *
 * @MigrateProcessPlugin(
 *   id = "dictionary_label_or_existing"
 * )
 */
final class DictionaryLabelOrExisting extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly DictionaryImportGovernance $importGovernance,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('ps_dictionary.import_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    $id = (string) ($row->getSourceProperty('id') ?? '');
    if ($id !== '' && $this->importGovernance->shouldPreserveExistingLabelsFromCrmLookup()) {
      $existing = $this->entityTypeManager->getStorage('ps_dictionary_entry')->load($id);
      if ($existing) {
        return (string) $existing->label();
      }
    }

    $label = trim((string) $value);
    if ($label !== '') {
      return $label;
    }

    return trim((string) ($row->getSourceProperty('code') ?? ''));
  }

}
