<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Unified list builder for dictionary entries (weights + batch delete).
 */
final class DictionaryEntryListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'entries';

  private ?string $dictionaryTypeId = NULL;

  public function __construct(
    EntityTypeInterface $entityType,
    EntityStorageInterface $storage,
    private readonly MessengerInterface $statusMessenger,
  ) {
    parent::__construct($entityType, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): self {
    return new self(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('messenger'),
    );
  }

  /**
   * Sets the dictionary type to display.
   */
  public function setDictionaryTypeId(string $dictionaryTypeId): self {
    $this->dictionaryTypeId = $dictionaryTypeId;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    $suffix = $this->dictionaryTypeId !== NULL ? '_' . strtr($this->dictionaryTypeId, '.', '_') : '';
    return 'ps_dictionary_entry_list' . $suffix;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['select'] = $this->t('Select');
    $header['id'] = $this->t('ID');
    $header['type'] = $this->t('Type');
    $header['code'] = $this->t('Code');
    $header['label'] = $this->t('Label');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row['select'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Select @entry', ['@entry' => $entity->id()]),
      '#title_display' => 'invisible',
    ];
    $row['id'] = ['#plain_text' => $entity->id()];
    $row['type'] = ['#plain_text' => $entity->get('type')];
    $row['code'] = ['#plain_text' => $entity->get('code')];
    $row['label'] = $entity->label();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function load(): array {
    if ($this->dictionaryTypeId === NULL) {
      return [];
    }

    $entities = parent::load();
    $entities = array_filter($entities, fn($entity) => $entity->get('type') === $this->dictionaryTypeId);
    uasort($entities, static function ($a, $b): int {
      $aWeight = (int) $a->get('weight');
      $bWeight = (int) $b->get('weight');
      if ($aWeight === $bWeight) {
        return strcmp((string) $a->id(), (string) $b->id());
      }
      return $aWeight <=> $bWeight;
    });

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $form['bulk_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#options' => [
        'save_weights' => $this->t('Save weights'),
        'delete_selected' => $this->t('Delete selected entries'),
      ],
      '#default_value' => 'save_weights',
      '#required' => TRUE,
    ];

    $form['actions']['submit']['#value'] = $this->t('Apply');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $action = (string) $form_state->getValue('bulk_action');

    if ($action === 'delete_selected') {
      $selected = [];
      foreach (($form_state->getValue($this->entitiesKey) ?? []) as $id => $values) {
        if (is_array($values) && !empty($values['select'])) {
          $selected[] = (string) $id;
        }
      }

      if ($selected === []) {
        $this->statusMessenger->addWarning($this->t('No entries selected.'));
      }
      else {
        $deleted = 0;
        foreach ($selected as $id) {
          if (isset($this->entities[$id])) {
            $this->entities[$id]->delete();
            $deleted++;
          }
        }
        $this->statusMessenger->addStatus($this->t('@count dictionary entries deleted.', ['@count' => $deleted]));
      }

      if ($this->dictionaryTypeId !== NULL) {
        $form_state->setRedirect('ps_dictionary.entry_collection', ['ps_dictionary_type' => $this->dictionaryTypeId]);
      }
      return;
    }

    parent::submitForm($form, $form_state);
    $this->statusMessenger->addStatus($this->t('Weights saved.'));
    if ($this->dictionaryTypeId !== NULL) {
      $form_state->setRedirect('ps_dictionary.entry_collection', ['ps_dictionary_type' => $this->dictionaryTypeId]);
    }
  }

}
