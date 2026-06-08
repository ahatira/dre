<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings for feature filter sync and More filters combine behaviour.
 */
final class FeatureFilterSyncSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_feature_filter_sync_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_search.feature_filter_sync'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.feature_filter_sync');

    $form['sync_view_filters'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sync per-feature filters into the search view'),
      '#description' => $this->t('When enabled, exposed Search API filters are created for each feature with “Expose as filter”. Run <code>drush ps:search:features:sync-index</code> after changes.'),
      '#default_value' => $config->get('sync_view_filters'),
    ];

    $form['feature_filters_combine'] = [
      '#type' => 'radios',
      '#title' => $this->t('Combine active feature filters'),
      '#description' => $this->t('Controls how multiple active feature filters are combined in search results and the live count. Within a single multi-value filter (tags), values are always combined with OR.'),
      '#options' => [
        'and' => $this->t('AND — offer must match every active feature filter (recommended, narrows results)'),
        'or' => $this->t('OR — offer may match any active feature filter (broadens results)'),
      ],
      '#default_value' => $config->get('feature_filters_combine') ?? 'and',
    ];

    $form['cron_run_immediate_index'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Run immediate indexing during cron sync'),
      '#default_value' => $config->get('cron_run_immediate_index'),
    ];

    $form['cron_index_only_on_change'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Index only when sync changed configuration'),
      '#default_value' => $config->get('cron_index_only_on_change'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.feature_filter_sync')
      ->set('sync_view_filters', (bool) $form_state->getValue('sync_view_filters'))
      ->set('feature_filters_combine', $form_state->getValue('feature_filters_combine'))
      ->set('cron_run_immediate_index', (bool) $form_state->getValue('cron_run_immediate_index'))
      ->set('cron_index_only_on_change', (bool) $form_state->getValue('cron_index_only_on_change'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
