<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Form handler for Dictionary Entry add and edit forms.
 */
class DictionaryEntryForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entry */
    $entry = $this->entity;

    // For new entries, get the dictionary type from the route.
    if ($entry->isNew()) {
      $route_match = $this->routeMatch();
      if ($route_match) {
        $type_id = $route_match->getParameter('ps_dictionary_type');
        if ($type_id) {
          $entry->set('dictionary_type', $type_id);
        }
      }
    }

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#maxlength' => 64,
      '#default_value' => $entry->getCode(),
      '#description' => $this->t('Machine-readable code (uppercase recommended).'),
      '#required' => TRUE,
      '#disabled' => !$entry->isNew(),
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entry->label(),
      '#description' => $this->t('Human-readable label for UI display.'),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $entry->getDescription(),
      '#description' => $this->t('Optional description for administrators.'),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $entry->getWeight(),
      '#description' => $this->t('Lower weights appear first in lists.'),
    ];

    $form['deprecated'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Deprecated'),
      '#default_value' => $entry->isDeprecated(),
      '#description' => $this->t('Mark as deprecated to discourage use.'),
    ];

    // Add dynamic metadata fields based on type schema.
    $this->buildMetadataFields($form, $entry);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entry */
    $entry = $this->entity;

    if ($entry->isNew()) {
      $type = $entry->get('dictionary_type');
      $code = $form_state->getValue('code');

      if ($type && is_string($code)) {
        $id = $type . '_' . strtolower($code);
        $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');

        if ($storage->load($id)) {
          $form_state->setErrorByName('code', $this->t('The entry %id already exists. Choose another code.', ['%id' => $id]));
        }
      }
    }
  }

  /**
   * Builds dynamic metadata fields based on type schema.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entry
   *   The dictionary entry entity.
   */
  protected function buildMetadataFields(array &$form, DictionaryEntryInterface $entry): void {
    $type_id = $entry->getType();
    $type = \Drupal::entityTypeManager()
      ->getStorage('ps_dictionary_type')
      ->load($type_id);

    if (!$type) {
      return;
    }

    $metadata_schema_yaml = $type->get('metadata_schema');
    if (empty($metadata_schema_yaml)) {
      return;
    }

    try {
      $schema = Yaml::parse($metadata_schema_yaml);
    }
    catch (ParseException $e) {
      \Drupal::messenger()->addWarning($this->t('Invalid metadata schema for this type.'));
      return;
    }

    if (empty($schema) || !is_array($schema)) {
      return;
    }

    $form['metadata'] = [
      '#type' => 'details',
      '#title' => $this->t('Metadata'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $current_metadata = $entry->getMetadata();

    foreach ($schema as $field_name => $field_def) {
      if (!is_array($field_def)) {
        continue;
      }

      $field_type = $field_def['type'] ?? 'textfield';
      $field_label = $field_def['label'] ?? $field_name;
      $field_required = $field_def['required'] ?? FALSE;
      $field_default = $field_def['default'] ?? NULL;

      $current_value = $current_metadata[$field_name] ?? $field_default;

      $field = [
        '#title' => $this->t('@label', ['@label' => $field_label]),
        '#required' => $field_required,
      ];

      switch ($field_type) {
        case 'textarea':
          $field['#type'] = 'textarea';
          $field['#default_value'] = $current_value ?? '';
          $field['#rows'] = $field_def['rows'] ?? 3;
          break;

        case 'number':
          $field['#type'] = 'number';
          $field['#default_value'] = $current_value;
          if (isset($field_def['min'])) {
            $field['#min'] = $field_def['min'];
          }
          if (isset($field_def['max'])) {
            $field['#max'] = $field_def['max'];
          }
          if (isset($field_def['step'])) {
            $field['#step'] = $field_def['step'];
          }
          break;

        case 'checkbox':
          $field['#type'] = 'checkbox';
          $field['#default_value'] = (bool) $current_value;
          break;

        case 'select':
        case 'list_string':
          $field['#type'] = 'select';
          $field['#default_value'] = $current_value;
          $field['#options'] = $field_def['options'] ?? [];
          if (!$field_required) {
            $field['#empty_option'] = $this->t('- Select -');
          }
          break;

        case 'textfield':
        default:
          $field['#type'] = 'textfield';
          $field['#default_value'] = $current_value ?? '';
          if (isset($field_def['maxlength'])) {
            $field['#maxlength'] = $field_def['maxlength'];
          }
          break;
      }

      if (isset($field_def['description'])) {
        $field['#description'] = $this->t('@desc', ['@desc' => $field_def['description']]);
      }

      $form['metadata'][$field_name] = $field;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entry */
    $entry = $this->entity;

    // Generate ID for new entries.
    if ($entry->isNew()) {
      $type = $entry->get('dictionary_type');
      $code = $form_state->getValue('code');
      $entry->set('id', $type . '_' . strtolower($code));
    }

    // Save metadata values.
    $metadata = $form_state->getValue('metadata');
    if (is_array($metadata)) {
      $entry->setMetadata($metadata);
    }

    $status = $entry->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created entry %label.', [
        '%label' => $entry->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Saved entry %label.', [
        '%label' => $entry->label(),
      ]));
    }

    // Redirect to entries list for type.
    $form_state->setRedirect('entity.ps_dictionary_type.entries', [
      'ps_dictionary_type' => $entry->getType(),
    ]);

    return $status;
  }

}
