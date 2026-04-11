<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Form handler for Dictionary Type add and edit forms.
 */
class DictionaryTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_dictionary\Entity\DictionaryTypeInterface $dictionary_type */
    $dictionary_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dictionary_type->label(),
      '#description' => $this->t('The human-readable name of this dictionary type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dictionary_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_dictionary\Entity\DictionaryType::load',
        'source' => ['label'],
      ],
      '#disabled' => !$dictionary_type->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $dictionary_type->getDescription(),
      '#description' => $this->t('A description of this dictionary type.'),
    ];

    // Metadata schema is a technical field, not exposed in UI.
    // Use Drush or direct config editing to manage metadata schema.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $metadata_schema = $form_state->getValue('metadata_schema');
    if (!empty($metadata_schema)) {
      try {
        Yaml::parse($metadata_schema);
      }
      catch (ParseException $e) {
        $form_state->setErrorByName('metadata_schema', $this->t('Invalid YAML syntax: @error', [
          '@error' => $e->getMessage(),
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $dictionary_type = $this->entity;
    $status = $dictionary_type->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created the %label dictionary type.', [
        '%label' => $dictionary_type->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Saved the %label dictionary type.', [
        '%label' => $dictionary_type->label(),
      ]));
    }

    $form_state->setRedirectUrl($dictionary_type->toUrl('collection'));

    return $status;
  }

}
