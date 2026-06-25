<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\BulkForm;

/**
 * Bulk operations field for feature definition config entities.
 */
#[ViewsField('ps_feature_definition_bulk_form')]
final class FeatureDefinitionBulkForm extends BulkForm {

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    // Config entities do not support the SQL translation renderer used by core.
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return ['url'];
  }

}
