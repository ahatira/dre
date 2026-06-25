<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\ps_feature\Plugin\views\query\FeatureDefinitionEntityQuery;
use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\filter\StringFilter;

/**
 * Full-text search across feature name and code.
 */
#[ViewsFilter('ps_feature_definition_search')]
final class FeatureDefinitionSearchFilter extends StringFilter {

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    if (!$this->query instanceof FeatureDefinitionEntityQuery) {
      return;
    }
    if (!is_string($this->value) || trim($this->value) === '') {
      return;
    }
    $this->query->addSearchCondition(trim($this->value));
  }

}
