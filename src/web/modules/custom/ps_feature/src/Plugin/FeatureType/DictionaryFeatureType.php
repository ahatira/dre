<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Attribute\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Dictionary feature type plugin.
 */
#[FeatureType(
  id: 'dictionary',
  label: new TranslatableMarkup('Dictionary'),
  description: new TranslatableMarkup('Dictionary code: {code: string}'),
)]
class DictionaryFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['code'])) {
      $errors[] = "Dictionary payload must contain 'code' key.";
    }
    elseif (!is_string($payload['code']) || empty(trim($payload['code']))) {
      $errors[] = "Dictionary 'code' must be a non-empty string.";
    }
    else {
      // Validate code exists in ps_dictionary.
      $code = trim($payload['code']);
      
      // Dictionary entry ID format: {type_id}.{code}
      // We need to find the entry by its code across all dictionary types.
      $entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
      $query = $entry_storage->getQuery()
        ->condition('code', $code)
        ->accessCheck(FALSE)
        ->range(0, 1);
      
      $result = $query->execute();
      
      if (empty($result)) {
        $errors[] = "Dictionary code '{$code}' does not exist in ps_dictionary.";
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'code' => strtoupper(trim($payload['code'] ?? '')),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    // Get all dictionary entries.
    $entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
    $entries = $entry_storage->loadMultiple();
    
    $options = ['' => t('- Select a dictionary entry -')];
    foreach ($entries as $entry) {
      $options[$entry->get('code')->value] = $entry->label() . ' (' . $entry->get('code')->value . ')';
    }
    
    return [
      'code' => [
        '#type' => 'select',
        '#title' => t('Dictionary code'),
        '#options' => $options,
        '#default_value' => $current_payload['code'] ?? '',
        '#required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $code = $payload['code'] ?? '';
    
    if (empty($code)) {
      return '';
    }
    
    // Load the dictionary entry to get the label.
    $entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
    $query = $entry_storage->getQuery()
      ->condition('code', $code)
      ->accessCheck(FALSE)
      ->range(0, 1);
    
    $ids = $query->execute();
    
    if (!empty($ids)) {
      $entry = $entry_storage->load(reset($ids));
      if ($entry) {
        return $entry->label();
      }
    }
    
    return $code;
  }

}
