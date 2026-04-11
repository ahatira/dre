<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * List builder for Dictionary Entry entities with drag-and-drop support.
 */
class DictionaryEntryListBuilder extends DraggableListBuilder {

  /**
   * Dictionary type ID filter.
   */
  private ?string $dictionaryType = NULL;

  /**
   * Sets the dictionary type filter.
   *
   * @param string $type
   *   The dictionary type ID.
   */
  public function setDictionaryType(string $type): void {
    $this->dictionaryType = $type;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds(): array {
    if ($this->dictionaryType === NULL) {
      return parent::getEntityIds();
    }

    $query = $this->getStorage()->getQuery()
      ->condition('dictionary_type', $this->dictionaryType)
      ->sort($this->weightKey)
      ->sort('label');

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_dictionary_entry_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'code' => $this->t('Code'),
      'label' => $this->t('Label'),
      'status' => $this->t('Status'),
      'deprecated' => $this->t('Deprecated'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entity */
    $row = [];
    $row['code']['#markup'] = $entity->getCode();
    $row['label'] = $entity->label();
    $row['status']['#markup'] = $entity->isActive() ? $this->t('Active') : $this->t('Inactive');
    $row['deprecated']['#markup'] = $entity->isDeprecated() ? $this->t('Yes') : $this->t('No');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit']['#value'] = $this->t('Save');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $this->messenger()->addStatus($this->t('The dictionary entries have been reordered.'));
  }

}
