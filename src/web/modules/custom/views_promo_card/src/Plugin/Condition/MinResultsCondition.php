<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views_promo_card\Service\ViewAwareConditionInterface;
use Drupal\views\ViewExecutable;

/**
 * Requires a minimum number of total view results.
 */
#[Condition(
  id: 'promo_card_min_results',
  label: new TranslatableMarkup('Minimum total results'),
)]
final class MinResultsCondition extends ConditionPluginBase implements ViewAwareConditionInterface {

  /**
   * The view being evaluated.
   */
  private ?ViewExecutable $view = NULL;

  /**
   * {@inheritdoc}
   */
  public function setView(ViewExecutable $view): void {
    $this->view = $view;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['minimum'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum total results'),
      '#description' => $this->t('Leave empty to ignore this condition.'),
      '#default_value' => $this->configuration['minimum'] ?? '',
      '#min' => 0,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $minimum = $form_state->getValue('minimum');
    if ($minimum === '' || $minimum === NULL) {
      unset($this->configuration['minimum']);
    }
    else {
      $this->configuration['minimum'] = (int) $minimum;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    if ($this->view === NULL || !array_key_exists('minimum', $this->configuration)) {
      return TRUE;
    }
    $minimum = (int) ($this->configuration['minimum'] ?? 0);
    return $this->resolveTotalResults() >= $minimum;
  }

  /**
   * Resolves the total result count for Search API and classic Views.
   */
  private function resolveTotalResults(): int {
    assert($this->view !== NULL);

    if (isset($this->view->pager->total_items)) {
      return (int) $this->view->pager->total_items;
    }

    $total = $this->view->total_rows ?? 0;
    if (is_array($total)) {
      $total = reset($total);
    }
    $total = (int) $total;
    if ($total > 0) {
      return $total;
    }

    return count($this->view->result);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return $this->t('Total results is at least @count', [
      '@count' => (int) ($this->configuration['minimum'] ?? 0),
    ]);
  }

}
