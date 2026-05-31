<?php

namespace Drupal\ps_dictionary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\DictionaryEntryListBuilder;
use Drupal\ps_dictionary\Entity\DictionaryType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Controller for listing dictionary entries by type (taxonomy-like UX).
 */
class DictionaryEntryListController extends ControllerBase {
  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Title callback for dictionary entry collection by type.
   */
  public function title(DictionaryType $ps_dictionary_type): TranslatableMarkup {
    return $this->t('Entries for @type', ['@type' => $ps_dictionary_type->label()]);
  }

  /**
   * Lists all dictionary entries for a given type.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryType $ps_dictionary_type
   *   The dictionary type entity from the route.
   *
   * @return array
   *   Render array for the entries list.
   */
  public function list(DictionaryType $ps_dictionary_type) {
    $listBuilder = $this->entityTypeManager->getListBuilder('ps_dictionary_entry');
    if ($listBuilder instanceof DictionaryEntryListBuilder) {
      $listBuilder->setDictionaryTypeId($ps_dictionary_type->id());
    }

    return $listBuilder->render();
  }
}
