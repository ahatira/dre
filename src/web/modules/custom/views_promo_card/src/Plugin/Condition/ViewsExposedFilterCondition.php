<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views\ViewExecutable;
use Drupal\views_promo_card\Service\ViewAwareConditionInterface;

/**
 * Matches an exposed filter value on the current view.
 */
#[Condition(
  id: 'promo_card_views_exposed_filter',
  label: new TranslatableMarkup('Views exposed filter'),
)]
final class ViewsExposedFilterCondition extends ConditionPluginBase implements ViewAwareConditionInterface {

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
    return [
      'filter_id' => '',
      'value' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['filter_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Filter ID'),
      '#description' => $this->t('Machine name of the exposed filter identifier (e.g. asset_type, operation_type).'),
      '#default_value' => $this->configuration['filter_id'] ?? '',
    ];
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Expected value'),
      '#default_value' => $this->configuration['value'] ?? '',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['filter_id'] = trim((string) $form_state->getValue('filter_id'));
    $this->configuration['value'] = trim((string) $form_state->getValue('value'));
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    if ($this->view === NULL) {
      return FALSE;
    }
    $filter_id = (string) ($this->configuration['filter_id'] ?? '');
    $expected = (string) ($this->configuration['value'] ?? '');
    if ($filter_id === '') {
      return FALSE;
    }
    $exposed = $this->view->getExposedInput();
    if (!array_key_exists($filter_id, $exposed)) {
      return FALSE;
    }
    $actual = $exposed[$filter_id];
    if (is_array($actual)) {
      return in_array($expected, $actual, TRUE);
    }
    return (string) $actual === $expected;
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return $this->t('Exposed filter @filter equals @value', [
      '@filter' => (string) ($this->configuration['filter_id'] ?? ''),
      '@value' => (string) ($this->configuration['value'] ?? ''),
    ]);
  }

}
