<?php

declare(strict_types=1);

namespace Drupal\ps_context\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * List builder for ps_context_rule config entities.
 */
final class PsContextRuleListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['conditions'] = $this->t('Conditions');
    $header['actions_count'] = $this->t('Actions');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface $entity */
    $conditions = $entity->getConditions();

    if (empty($conditions)) {
      $cond_summary = $this->t('Always applies');
    }
    else {
      $logic = $entity->getConditionsLogic();
      $parts = array_map(
        static fn(array $c): string => ($c['field_name'] !== '' ? $c['field_name'] : '?') . ' ' . ($c['operator'] ?? '=') . ' ' . ($c['value'] ?? ''),
        $conditions,
      );
      $cond_summary = implode(' ' . $logic . ' ', $parts);
    }

    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['conditions'] = $cond_summary;
    $row['actions_count'] = count($entity->getActions());
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $this->messenger()->addMessage(
      $this->t('Rules define offer-form behaviour (show or hide tabs and fields, defaults, validation) from asset type, operation type and optional field values. Enabled rules are evaluated in weight order — lower weights first.'),
      'info',
    );
    return parent::render();
  }

}
