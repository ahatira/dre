<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Looks up a dictionary entity by type and code.
 *
 * If found, returns the dictionary ID for the reference field.
 * If not found, returns NULL (the raw code will be stored separately).
 *
 * Usage:
 * @code
 * field_asset_type:
 *   plugin: ps_dictionary_lookup
 *   source: type_code
 *   dictionary_type: asset_type
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "ps_dictionary_lookup",
 *   handle_multiples = FALSE
 * )
 */
final class DictionaryLookup extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a DictionaryLookup plugin.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
    if (empty($value)) {
      return NULL;
    }

    $dictionary_type = $this->configuration['dictionary_type'] ?? NULL;
    if (empty($dictionary_type)) {
      throw new \InvalidArgumentException('The "dictionary_type" configuration is required for ps_dictionary_lookup plugin.');
    }

    // Apply static map if configured (e.g., LOG => ENT).
    $map = $this->configuration['map'] ?? [];
    if (!empty($map) && isset($map[$value])) {
      $value = $map[$value];
    }

    // Normalize code: uppercase and trim.
    $code = mb_strtoupper(trim((string) $value));

    // Build dictionary ID: {type}.{code_lowercase}.
    $dictionary_id = $dictionary_type . '.' . mb_strtolower($code);

    // Try to load the dictionary entity.
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entity = $storage->load($dictionary_id);

    // Return dictionary ID if found, NULL otherwise.
    return $entity ? $dictionary_id : NULL;
  }

}
