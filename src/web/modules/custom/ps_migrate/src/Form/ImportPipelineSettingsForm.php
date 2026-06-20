<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * CRM import pipeline folder and behaviour settings.
 */
final class ImportPipelineSettingsForm extends ConfigFormBase {

  private const int BYTES_PER_MB = 1048576;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_migrate_import_pipeline_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_migrate.import_pipeline_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_migrate.import_pipeline_settings');

    $form['paths'] = [
      '#type' => 'details',
      '#title' => $this->t('Pipeline folders'),
      '#open' => TRUE,
    ];
    foreach (['incoming', 'processing', 'archive', 'failed'] as $key) {
      $form['paths'][$key] = [
        '#type' => 'textfield',
        '#title' => $this->t('@folder folder', ['@folder' => ucfirst($key)]),
        '#default_value' => $config->get("paths.{$key}"),
        '#required' => TRUE,
        '#description' => $this->t('Drupal stream wrapper URI, e.g. private://crm/incoming'),
      ];
    }

    $form['staging_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Migrate staging URI'),
      '#default_value' => $config->get('staging_uri'),
      '#required' => TRUE,
      '#description' => $this->t('Active XML copied here before migrate runs (default public://crm/offers.xml).'),
    ];

    $form['mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Default import mode'),
      '#options' => [
        'full' => $this->t('Full — all migrations with dependencies'),
        'delta' => $this->t('Delta — offers and translations only (--update)'),
      ],
      '#default_value' => $config->get('mode'),
    ];

    $form['batch_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Batch limit per run'),
      '#default_value' => $config->get('batch_limit'),
      '#min' => 0,
      '#description' => $this->t('Maximum XML files processed per pipeline run. 0 = unlimited.'),
    ];

    $maxUploadBytes = (int) $config->get('max_upload_size');
    $form['max_upload_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Max upload size (MB)'),
      '#default_value' => (int) round($maxUploadBytes / self::BYTES_PER_MB),
      '#min' => 1,
      '#step' => 1,
      '#description' => $this->t('Maximum size for CRM XML uploads via the back office.'),
    ];

    $form['cron'] = [
      '#type' => 'details',
      '#title' => $this->t('Cron'),
      '#open' => TRUE,
    ];
    $form['cron']['cron_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Poll incoming folder during cron'),
      '#default_value' => $config->get('cron_enabled'),
    ];
    $form['cron']['cron_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum interval between cron runs (seconds)'),
      '#default_value' => $config->get('cron_interval'),
      '#min' => 60,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_migrate.import_pipeline_settings')
      ->set('paths.incoming', trim((string) $form_state->getValue('incoming')))
      ->set('paths.processing', trim((string) $form_state->getValue('processing')))
      ->set('paths.archive', trim((string) $form_state->getValue('archive')))
      ->set('paths.failed', trim((string) $form_state->getValue('failed')))
      ->set('staging_uri', trim((string) $form_state->getValue('staging_uri')))
      ->set('mode', $form_state->getValue('mode'))
      ->set('batch_limit', (int) $form_state->getValue('batch_limit'))
      ->set('max_upload_size', (int) $form_state->getValue('max_upload_size') * self::BYTES_PER_MB)
      ->set('cron_enabled', (bool) $form_state->getValue('cron_enabled'))
      ->set('cron_interval', (int) $form_state->getValue('cron_interval'))
      ->save();

    /** @var \Drupal\ps_migrate\Service\ImportPipelinePathResolver $resolver */
    $resolver = \Drupal::service('ps_migrate.import_pipeline_path_resolver');
    $resolver->ensureConfiguredDirectories();

    parent::submitForm($form, $form_state);
  }

}
