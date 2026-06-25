<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\ps_feature\Plugin\views\query\FeatureDefinitionEntityQuery;
use Drupal\views\Plugin\views\filter\StringFilter;

/**
 * String filter handler for feature definition Views queries.
 */
abstract class FeatureDefinitionStringFilterBase extends StringFilter {

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    $this->ensureMyTable();
    $field = $this->realField;
    $info = $this->operators();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($field);
    }
  }

  /**
   * Adds an entity condition to the custom query plugin.
   */
  protected function addEntityCondition(string $field, mixed $value, ?string $operator = '='): void {
    if (!$this->query instanceof FeatureDefinitionEntityQuery) {
      return;
    }
    $this->query->addWhere($this->options['group'], $field, $value, $operator);
  }

  /**
   * Adds a contains condition.
   */
  protected function opContains($field): void {
    $this->addEntityCondition($field, $this->value, 'CONTAINS');
  }

  /**
   * Adds an equals condition.
   */
  public function opEqual($field): void {
    $this->addEntityCondition($field, $this->value, '=');
  }

  /**
   * Adds a not-equal condition.
   */
  protected function opNotEqual($field): void {
    $this->addEntityCondition($field, $this->value, '<>');
  }

  /**
   * Adds a starts-with condition.
   */
  protected function opStartsWith($field): void {
    $this->addEntityCondition($field, $this->value, 'CONTAINS');
  }

  /**
   * Adds a not-contains condition.
   */
  protected function opNotLike($field): void {
    $this->addEntityCondition($field, $this->value, 'NOT LIKE');
  }

  /**
   * Adds an IN condition.
   */
  protected function opSimple(string $field): void {
    $this->addEntityCondition($field, $this->value, $this->operator);
  }

}
