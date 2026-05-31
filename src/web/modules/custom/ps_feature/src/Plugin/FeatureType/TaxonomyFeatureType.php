<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Taxonomy feature type plugin.
 *
 * @FeatureType(
 *   id = "taxonomy",
 *   label = @Translation("Taxonomy"),
 *   description = @Translation("Reference to a taxonomy term: {tid: int}")
 * )
 */
class TaxonomyFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['tid'])) {
      $errors[] = "Taxonomy payload must contain 'tid' key.";
    }
    elseif (!is_int($payload['tid']) && !ctype_digit((string) $payload['tid'])) {
      $errors[] = "Taxonomy 'tid' must be a valid term ID.";
    }
    else {
      // Validate tid exists.
      $tid = (int) $payload['tid'];
      $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
      $term = $term_storage->load($tid);
      
      if (!$term) {
        $errors[] = "Taxonomy term ID '{$tid}' does not exist.";
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'tid' => (int) ($payload['tid'] ?? 0),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    // Get all taxonomy terms from all vocabularies.
    $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $terms = $term_storage->loadMultiple();
    
    $options = ['' => t('- Select a taxonomy term -')];
    foreach ($terms as $term) {
      $vocab = $term->bundle();
      $options[$term->id()] = $term->label() . ' (' . $vocab . ')';
    }
    
    return [
      'tid' => [
        '#type' => 'select',
        '#title' => t('Taxonomy term'),
        '#options' => $options,
        '#default_value' => $current_payload['tid'] ?? '',
        '#required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $tid = $payload['tid'] ?? 0;
    
    if (empty($tid)) {
      return '';
    }
    
    // Load the taxonomy term to get the label.
    $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $term = $term_storage->load($tid);
    
    if ($term) {
      return $term->label();
    }
    
    return (string) $tid;
  }

}
