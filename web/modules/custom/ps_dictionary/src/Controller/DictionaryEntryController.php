<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Entity\DictionaryTypeInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;

/**
 * Controller for dictionary entry operations.
 */
class DictionaryEntryController extends ControllerBase {

  /**
   * Lists dictionary entries for a given type.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryTypeInterface $ps_dictionary_type
   *   The dictionary type.
   *
   * @return array<string, mixed>
   *   A render array.
   */
  public function listEntries(DictionaryTypeInterface $ps_dictionary_type): array {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryListBuilder $list_builder */
    $list_builder = $this->entityTypeManager()->getListBuilder('ps_dictionary_entry');
    $list_builder->setDictionaryType($ps_dictionary_type->id());

    return [
      '#type' => 'container',
      'table' => $list_builder->render(),
      '#cache' => [
        'tags' => ['ps_dictionary:' . $ps_dictionary_type->id()],
      ],
    ];
  }

  /**
   * Title callback for the entries list.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryTypeInterface $ps_dictionary_type
   *   The dictionary type.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function getTitle(DictionaryTypeInterface $ps_dictionary_type): TranslatableMarkup {
    return $this->t('Entries: @label', [
      '@label' => $ps_dictionary_type->label(),
    ]);
  }

  /**
   * Displays a dictionary entry detail page.
   *
   * For ConfigEntity, this typically redirects to the edit form since
   * there's no separate detail view. This serves as the canonical route
   * handler.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $ps_dictionary_entry
   *   The dictionary entry.
   *
   * @return array<string, mixed>
   *   A render array showing the entry details or redirect to edit form.
   */
  public function view(DictionaryEntryInterface $ps_dictionary_entry): array {
    // For a ConfigEntity, we can display the entry details or redirect to edit.
    // Here we'll show a simple display of the entry information.
    return [
      '#theme' => 'dictionary_entry_detail',
      '#entry' => $ps_dictionary_entry,
      '#cache' => [
        'tags' => ['ps_dictionary_entry:' . $ps_dictionary_entry->id()],
      ],
    ];
  }

}
