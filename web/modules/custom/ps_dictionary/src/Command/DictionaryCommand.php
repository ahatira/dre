<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Command;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Drush\Style\DrushStyle;
use Symfony\Component\Yaml\Yaml;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Drush commands for ps_dictionary module.
 *
 * Provides commands for listing, exporting, and managing dictionary caches.
 */
final class DictionaryCommand extends DrushCommands {

  /**
   * Constructs a DictionaryCommand object.
   *
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct();
  }

  /**
   * List all dictionary types.
   *
   * @command ps:dictionary-list
   * @aliases ps-dict-list
   */
  #[CLI\Command(name: 'ps:dictionary-list', aliases: ['ps-dict-list'])]
  #[CLI\Usage(name: 'drush ps:dictionary-list', description: 'List all dictionary types')]
  public function listTypes(): void {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_type');
    $types = $storage->loadMultiple();

    if (empty($types)) {
      /** @phpstan-ignore-next-line */
      $this->style()->warning('No dictionary types found.');
      return;
    }

    $rows = [];
    foreach ($types as $type) {
      $entries = $this->dictionaryManager->getEntries($type->id(), FALSE);
      $rows[] = [
        $type->id(),
        $type->label(),
        count($entries),
      ];
    }

    /** @phpstan-ignore-next-line */
    $this->style()->table(['ID', 'Label', 'Entries'], $rows);
  }

  /**
   * Show entries for a dictionary type.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param array $options
   *   Command options.
   *
   * @command ps:dictionary-show
   * @aliases ps-dict-show
   */
  #[CLI\Command(name: 'ps:dictionary-show', aliases: ['ps-dict-show'])]
  #[CLI\Argument(name: 'type', description: 'Dictionary type ID')]
  #[CLI\Option(name: 'format', description: 'Output format: yaml or json')]
  #[CLI\Usage(name: 'drush ps:dictionary-show property_type', description: 'Show property_type entries')]
  #[CLI\Usage(name: 'drush ps:dictionary-show property_type --format=json', description: 'Show as JSON')]
  public function showEntries(string $type, array $options = ['format' => 'yaml']): void {
    $entries = $this->dictionaryManager->getEntries($type, FALSE);

    if (empty($entries)) {
      $this->style()->warning("No entries found for type: {$type}");
      return;
    }

    $data = [
      'type' => $type,
      'entries' => [],
    ];

    foreach ($entries as $entry) {
      $data['entries'][] = [
        'code' => $entry->getCode(),
        'label' => $entry->label(),
        'description' => $entry->getDescription(),
        'weight' => $entry->getWeight(),
        'status' => $entry->isActive() ? 'active' : 'inactive',
        'deprecated' => $entry->isDeprecated(),
        'metadata' => $entry->getMetadata(),
      ];
    }

    if ($options['format'] === 'json') {
      /** @phpstan-ignore-next-line */
      $this->style()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    else {
      /** @phpstan-ignore-next-line */
      $this->style()->write(Yaml::dump($data, 4, 2));
    }
  }

  /**
   * Export dictionary as JSON or YAML.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param array $options
   *   Command options.
   *
   * @command ps:dictionary-export
   * @aliases ps-dict-export
   */
  #[CLI\Command(name: 'ps:dictionary-export', aliases: ['ps-dict-export'])]
  #[CLI\Argument(name: 'type', description: 'Dictionary type ID')]
  #[CLI\Option(name: 'format', description: 'Output format: yaml or json (default: yaml)')]
  #[CLI\Usage(name: 'drush ps:dictionary-export property_type', description: 'Export as YAML')]
  #[CLI\Usage(name: 'drush ps:dictionary-export property_type --format=json', description: 'Export as JSON')]
  public function exportDictionary(string $type, array $options = ['format' => 'yaml']): void {
    $this->showEntries($type, $options);
  }

  /**
   * Clear dictionary cache.
   *
   * @param string|null $type
   *   Dictionary type ID (optional).
   *
   * @command ps:dictionary-cache-clear
   * @aliases ps-dict-cc
   */
  #[CLI\Command(name: 'ps:dictionary-cache-clear', aliases: ['ps-dict-cc'])]
  #[CLI\Argument(name: 'type', description: 'Dictionary type ID (optional)')]
  #[CLI\Usage(name: 'drush ps:dictionary-cache-clear', description: 'Clear all dictionary caches')]
  #[CLI\Usage(name: 'drush ps:dictionary-cache-clear property_type', description: 'Clear cache for property_type')]
  public function clearCache(?string $type = NULL): void {
    $this->dictionaryManager->clearCache($type);

    if ($type) {
      /** @phpstan-ignore-next-line */
      $this->style()->success("Cleared cache for dictionary type: {$type}");
    }
    else {
      /** @phpstan-ignore-next-line */
      $this->style()->success('Cleared all dictionary caches');
    }
  }

  /**
   * Provides a typed DrushStyle instance for IO helpers.
   *
   * @return \Drush\Style\DrushStyle
   *   The DrushStyle instance.
   *
   * @phpstan-ignore-next-line
   */
  private function style(): DrushStyle {
    /** @var \Drush\Style\DrushStyle $io */
    $io = $this->io();
    return $io;
  }

}
