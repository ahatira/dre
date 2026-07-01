<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Attribute\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * List feature type plugin.
 */
#[FeatureType(
  id: 'list',
  label: new TranslatableMarkup('Multi-value list'),
  description: new TranslatableMarkup('List of dictionary codes: {codes: array}'),
)]
class ListFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['codes'])) {
      $errors[] = "List payload must contain 'codes' key.";
    }
    elseif (!is_array($payload['codes'])) {
      $errors[] = "List 'codes' must be an array.";
    }
    elseif (empty($payload['codes'])) {
      $errors[] = "List 'codes' must contain at least one element.";
    }
    else {
      $entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
      
      foreach ($payload['codes'] as $code) {
        if (!is_string($code) || empty(trim($code))) {
          $errors[] = "Each code in List 'codes' must be a non-empty string.";
          break;
        }
        
        // Validate each code exists in ps_dictionary.
        $code_trimmed = trim($code);
        $query = $entry_storage->getQuery()
          ->condition('code', $code_trimmed)
          ->accessCheck(FALSE)
          ->range(0, 1);
        
        $result = $query->execute();
        
        if (empty($result)) {
          $errors[] = "Dictionary code '{$code_trimmed}' does not exist in ps_dictionary.";
        }
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    $codes = $payload['codes'] ?? [];
    return [
      'codes' => array_values(array_map(function ($code) {
        return strtoupper(trim($code));
      }, $codes)),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    $codes = $current_payload['codes'] ?? [];
    $codes_text = implode("\n", $codes);
    
    return [
      'codes_text' => [
        '#type' => 'textarea',
        '#title' => t('Dictionary codes'),
        '#default_value' => $codes_text,
        '#required' => TRUE,
        '#description' => t('Enter one dictionary code per line.'),
        '#rows' => 5,
      ],
      // This will be processed in the widget's massageFormValues.
      '#codes_from_text' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $codes = $payload['codes'] ?? [];
    
    if (empty($codes)) {
      return t('(empty list)');
    }
    
    // Load labels from dictionary.
    $entry_storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
    $labels = [];
    
    foreach ($codes as $code) {
      $query = $entry_storage->getQuery()
        ->condition('code', $code)
        ->accessCheck(FALSE)
        ->range(0, 1);
      
      $ids = $query->execute();
      
      if (!empty($ids)) {
        $entry = $entry_storage->load(reset($ids));
        if ($entry) {
          $labels[] = $entry->label();
        }
        else {
          $labels[] = $code;
        }
      }
      else {
        $labels[] = $code;
      }
    }
    
    return implode(', ', $labels);
  }

}
