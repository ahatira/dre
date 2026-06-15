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
 * Limits injection to specific pager pages.
 */
#[Condition(
  id: 'promo_card_pager_page',
  label: new TranslatableMarkup('Pager page'),
)]
final class PagerPageCondition extends ConditionPluginBase implements ViewAwareConditionInterface {

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
    return ['max_page' => 0] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['max_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum pager page'),
      '#description' => $this->t('Use 0 for first page only (recommended with load-more).'),
      '#default_value' => $this->configuration['max_page'] ?? 0,
      '#min' => 0,
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['max_page'] = (int) $form_state->getValue('max_page');
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    if ($this->view === NULL) {
      return FALSE;
    }
    return (int) $this->view->getCurrentPage() <= (int) ($this->configuration['max_page'] ?? 0);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return $this->t('Pager page is at most @page', [
      '@page' => (int) ($this->configuration['max_page'] ?? 0),
    ]);
  }

}
