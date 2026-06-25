<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\query;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\ps_feature\Plugin\views\query\FeatureDefinitionEntityQuery;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views\Attribute\ViewsQuery;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Views query plugin for fb_feature_definition config entities.
 */
#[ViewsQuery(
  id: 'ps_feature_definition_entity',
  title: new TranslatableMarkup('Feature definition entity query'),
  help: new TranslatableMarkup('Queries feature definitions via the config entity storage.'),
)]
final class FeatureDefinitionEntityQuery extends QueryPluginBase {

  /**
   * Entity query conditions grouped by Views filter group.
   *
   * @var array<int, array{type: string, field?: string, value?: mixed, operator?: string}>
   */
  protected array $entityConditions = [];

  /**
   * Full-text search value applied across label and code.
   */
  protected ?string $searchValue = NULL;

  /**
   * Sort definitions keyed by field.
   *
   * @var array<string, string>
   */
  protected array $sorts = [];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->groupOperator = 'AND';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function ensureTable($table, $relationship = NULL): string {
    return (string) $table;
  }

  /**
   * {@inheritdoc}
   */
  public function addField($table, $field, $alias = '', $params = []): string {
    if ($alias !== '' && $alias !== NULL) {
      return (string) $alias;
    }
    if ($field === NULL || $field === '') {
      return 'operations';
    }
    return (string) $field;
  }

  /**
   * {@inheritdoc}
   */
  public function addWhere($group, $field, $value = NULL, $operator = NULL): void {
    if (empty($group)) {
      $group = 0;
    }
    $this->entityConditions[(int) $group][] = [
      'type' => 'condition',
      'field' => $this->normalizeField((string) $field),
      'value' => $value,
      'operator' => $operator ?? '=',
    ];
  }

  /**
   * Adds a full-text search condition across feature name and code.
   */
  public function addSearchCondition(string $value): void {
    $value = trim($value);
    if ($value === '') {
      return;
    }
    $this->searchValue = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function addOrderBy($table, $field = NULL, $order = 'ASC'): void {
    if ($field === NULL) {
      return;
    }
    $this->sorts[$this->normalizeField((string) $field)] = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view): void {
    $storage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $query = $storage->getQuery()->accessCheck(FALSE);
    $this->applyConditions($query);

    if ($this->searchValue !== NULL) {
      $search = $this->searchValue;
      $or = $query->orConditionGroup()
        ->condition('label', $search, 'CONTAINS')
        ->condition('code', $search, 'CONTAINS');
      $query->condition($or);
    }

    if ($this->sorts !== []) {
      foreach ($this->sorts as $field => $direction) {
        $query->sort($field, $direction);
      }
    }
    else {
      $query->sort('weight')->sort('label');
    }

    $view->initPager();
    $view->pager->query();

    $all_ids = array_values($query->execute());
    $view->total_rows = count($all_ids);

    if ($this->limit !== NULL) {
      $offset = (int) ($this->offset ?? 0);
      $all_ids = array_slice($all_ids, $offset, (int) $this->limit);
    }

    $entities = $storage->loadMultiple($all_ids);
    $base_field = $view->storage->get('base_field');
    $view->result = [];
    foreach ($all_ids as $id) {
      if (!isset($entities[$id])) {
        continue;
      }
      $row = new ResultRow();
      $row->_entity = $entities[$id];
      if (is_string($base_field) && $base_field !== '') {
        $row->{$base_field} = $id;
      }
      $view->result[] = $row;
    }

    // Match core Sql query: numeric index aligns bulk form placeholders with keys.
    array_walk($view->result, function (ResultRow $row, int $index): void {
      $row->index = $index;
    });

    $view->pager->total_items = $view->total_rows;
    $view->pager->updatePageInfo();
  }

  /**
   * Applies grouped entity conditions to the storage query.
   */
  private function applyConditions(QueryInterface $query): void {
    if ($this->entityConditions === []) {
      return;
    }

    $groups = $this->entityConditions;
    ksort($groups);

    if ($this->groupOperator === 'OR' && count($groups) > 1) {
      $root = $query->orConditionGroup();
      foreach ($groups as $group_conditions) {
        $root->condition($this->buildConditionGroup($query, $group_conditions));
      }
      $query->condition($root);
      return;
    }

    foreach ($groups as $group_conditions) {
      $query->condition($this->buildConditionGroup($query, $group_conditions));
    }
  }

  /**
   * Builds one AND condition group for the entity query.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The entity query being built.
   * @param array<int, array{type: string, field?: string, value?: mixed, operator?: string}> $conditions
   *   Views filter conditions for one group.
   *
   * @return \Drupal\Core\Entity\Query\ConditionInterface|\Drupal\Core\Entity\Query\QueryInterface
   *   A condition group or query fragment.
   */
  private function buildConditionGroup(QueryInterface $query, array $conditions) {
    $group = $query->andConditionGroup();
    foreach ($conditions as $condition) {
      if (($condition['type'] ?? '') !== 'condition') {
        continue;
      }
      $field = $condition['field'] ?? '';
      if ($field === '') {
        continue;
      }
      $operator = $this->mapOperator((string) ($condition['operator'] ?? '='));
      $value = $condition['value'];

      if ($operator === 'IS NULL') {
        $group->condition($field, '', '=');
        continue;
      }
      if ($operator === 'IS NOT NULL') {
        $group->condition($field, '', '<>');
        continue;
      }

      $group->condition($field, $value, $operator);
    }
    return $group;
  }

  /**
   * Strips table alias prefixes from field names.
   */
  private function normalizeField(string $field): string {
    if (str_contains($field, '.')) {
      return (string) substr($field, strrpos($field, '.') + 1);
    }
    return $field;
  }

  /**
   * Maps SQL-style operators to config entity query operators.
   */
  private function mapOperator(string $operator): string {
    return match (strtoupper($operator)) {
      'LIKE', 'CONTAINS' => 'CONTAINS',
      'NOT LIKE' => '<>',
      'IN' => 'IN',
      'NOT IN' => 'NOT IN',
      '!=' => '<>',
      'IS NULL' => 'IS NULL',
      'IS NOT NULL' => 'IS NOT NULL',
      default => $operator,
    };
  }

}
