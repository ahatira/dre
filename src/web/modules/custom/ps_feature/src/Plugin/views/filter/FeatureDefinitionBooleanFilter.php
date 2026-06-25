<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\ps_feature\Plugin\views\query\FeatureDefinitionEntityQuery;
use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\filter\BooleanFilter;

/**
 * Boolean filter for feature definition entity fields.
 */
#[ViewsFilter('ps_feature_definition_boolean')]
final class FeatureDefinitionBooleanFilter extends BooleanFilter {

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    if (!$this->query instanceof FeatureDefinitionEntityQuery) {
      return;
    }

    $this->ensureMyTable();
    if ($this->value === 'All' || $this->value === '') {
      return;
    }

    $this->query->addWhere(
      $this->options['group'],
      $this->realField,
      (bool) $this->value,
      '=',
    );
  }

}
