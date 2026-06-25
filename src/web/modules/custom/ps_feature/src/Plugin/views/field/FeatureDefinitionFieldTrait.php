<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\views\ResultRow;

/**
 * Helper methods for feature definition Views field handlers.
 */
trait FeatureDefinitionFieldTrait {

  /**
   * Loads the feature definition entity from a Views result row.
   */
  protected function getDefinition(ResultRow $row): ?FeatureDefinition {
    if ($row->_entity instanceof FeatureDefinition) {
      return $row->_entity;
    }
    return NULL;
  }

}
