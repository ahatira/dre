<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_migrate\Service\ImportPipelineAdminSummary;
use Drupal\ps_migrate\Service\ImportPipelinePathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CRM import pipeline folder and behaviour settings.
 */
final class ImportPipelineSettingsForm extends ConfigFormBase {

  private const int BYTES_PER_MB = 1048576;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ImportPipelineAdminSummary $adminSummary,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('ps_migrate.import_pipeline_admin_summary'),
      $container->get('ps_migrate.import_pipeline_path_resolver'),
      $container->get('module_handler'),
    );
  }

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

    $form['#attached']['library'][] = 'ps_migrate/import_admin_overview';

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
        '#description' => $this->t('Drupal stream wrapper URI, e.g. @example', [
          '@example' => ImportPipelineAdminSummary::DEFAULT_PATH_INCOMING,
        ]),
      ];
    }

    $form['staging_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Migrate staging URI'),
      '#default_value' => $config->get('staging_uri'),
      '#required' => TRUE,
      '#description' => $this->t('Active XML copied here before migrate runs. Migrations receive this URI at runtime during import.'),
    ];
    if ($this->adminSummary->stagingUriDiffersFromCmiDefault()) {
      $form['staging_uri_warning'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'messages',
            'messages--warning',
            'ps-migrate-import-settings__staging-warning',
          ],
        ],
        'text' => [
          '#markup' => $this->t('Differs from exported migration CMI default (@default). Runtime override is expected — YAML exports still reference the default until changed in config sync.', [
            '@default' => ImportPipelineAdminSummary::DEFAULT_CMI_STAGING_URI,
          ]),
        ],
      ];
    }

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

    $form['queue'] = [
      '#type' => 'details',
      '#title' => $this->t('Queue'),
      '#open' => TRUE,
    ];
    $form['queue']['queue_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enqueue incoming files for async processing'),
      '#default_value' => $config->get('queue_enabled'),
    ];
    $form['queue']['queue_process_on_cron'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Process queue items during cron'),
      '#default_value' => $config->get('queue_process_on_cron'),
    ];
    $form['queue']['queue_items_per_cron'] = [
      '#type' => 'number',
      '#title' => $this->t('Queue items processed per cron run'),
      '#default_value' => $config->get('queue_items_per_cron'),
      '#min' => 1,
    ];

    $form['alerts'] = [
      '#type' => 'details',
      '#title' => $this->t('Alerts'),
      '#open' => TRUE,
    ];
    $form['alerts']['alert_email_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send email alerts on import failure'),
      '#default_value' => $config->get('alert_email_enabled'),
    ];
    $form['alerts']['alert_email_recipients'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alert email recipients'),
      '#default_value' => $config->get('alert_email_recipients'),
      '#description' => $this->t('Comma-separated email addresses.'),
    ];
    $form['alerts']['alert_email_on_warning'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send email alerts on high skip rate'),
      '#default_value' => $config->get('alert_email_on_warning'),
    ];
    $form['alerts']['alert_skip_threshold_percent'] = [
      '#type' => 'number',
      '#title' => $this->t('Skip rate threshold (%)'),
      '#default_value' => $config->get('alert_skip_threshold_percent'),
      '#min' => 1,
      '#max' => 100,
    ];

    $form['execution'] = [
      '#type' => 'details',
      '#title' => $this->t('Execution'),
      '#open' => TRUE,
    ];
    $form['execution']['post_run_index_solr'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Index Solr offers after successful import'),
      '#default_value' => $config->get('post_run_index_solr'),
      '#description' => $this->t('Runs Search API indexing when a pipeline run completes successfully.'),
    ];
    $form['execution']['post_run_search_api_index'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search API index ID'),
      '#default_value' => $config->get('post_run_search_api_index') ?: 'offers',
      '#required' => TRUE,
      '#description' => $this->t('Search API index machine name to index after import (default: offers).'),
      '#states' => [
        'visible' => [
          ':input[name="post_run_index_solr"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['execution']['migration_orders'] = [
      '#type' => 'details',
      '#title' => $this->t('Migration order'),
      '#open' => FALSE,
      '#description' => $this->t('One migration plugin ID per line. Leave empty lines out. Unknown IDs are ignored at runtime with a log warning.'),
    ];
    $form['execution']['migration_orders']['migration_order_full'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Full import migration order'),
      '#default_value' => $this->formatMigrationOrder($config->get('migration_order_full')),
      '#rows' => 10,
    ];
    $form['execution']['migration_orders']['migration_order_delta'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Delta import migration order'),
      '#default_value' => $this->formatMigrationOrder($config->get('migration_order_delta')),
      '#rows' => 4,
    ];

    $form['governance'] = [
      '#type' => 'details',
      '#title' => $this->t('Data governance'),
      '#open' => TRUE,
    ];
    $form['governance']['lock_strategy_default'] = [
      '#type' => 'select',
      '#title' => $this->t('Default lock strategy'),
      '#options' => [
        'log_only' => $this->t('Log only — CRM overwrites protected entities'),
        'skip_row' => $this->t('Skip row — do not update protected entities'),
        'skip_field' => $this->t('Skip field — preserve non-empty internal field values'),
      ],
      '#default_value' => $config->get('lock_strategy_default') ?: 'log_only',
      '#description' => $this->t('Applies when an entity has field_internal_lock enabled. Domain-specific rules are configured under @governance.', [
        '@governance' => $this->moduleHandler->moduleExists('ps_core')
          ? Link::fromTextAndUrl($this->t('Configuration → Governance'), Url::fromRoute('ps_core.governance'))->toString()
          : $this->t('Configuration → Governance'),
      ]),
    ];
    $form['governance']['lock_field_strategies'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Per-field lock strategy overrides'),
      '#default_value' => $this->formatFieldStrategies($config->get('lock_field_strategies')),
      '#rows' => 4,
      '#description' => $this->t('One field per line: field_name=strategy (log_only, skip_row, skip_field).'),
    ];
    $form['governance']['skip_unchanged_offers'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Skip unchanged offers (delta mode only)'),
      '#default_value' => $config->get('skip_unchanged_offers'),
      '#description' => $this->t('Compares source checksum with field_source_checksum and skips rows with no changes during delta imports.'),
    ];
    $form['governance']['conflict_window_seconds'] = [
      '#type' => 'number',
      '#title' => $this->t('Conflict detection window (seconds)'),
      '#description' => $this->t('When greater than 0, checksum mismatches are reported as conflicts only if the entity was modified locally after the last CRM import within this window. 0 = always report checksum mismatches.'),
      '#default_value' => (int) ($config->get('conflict_window_seconds') ?? 300),
      '#min' => 0,
      '#step' => 1,
      '#required' => TRUE,
    ];

    $form['performance'] = [
      '#type' => 'details',
      '#title' => $this->t('Performance'),
      '#open' => FALSE,
    ];
    $form['performance']['media_download_timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Media download timeout (seconds)'),
      '#default_value' => $config->get('media_download_timeout') ?? 30,
      '#min' => 5,
      '#max' => 300,
    ];
    $form['performance']['media_download_retry_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Media download retries'),
      '#default_value' => $config->get('media_download_retry_count') ?? 2,
      '#min' => 0,
      '#max' => 5,
      '#description' => $this->t('Number of retries after the first failed HTTP download attempt.'),
    ];
    $form['performance']['media_download_max_failures_percent'] = [
      '#type' => 'number',
      '#title' => $this->t('Media failure warning threshold (%)'),
      '#default_value' => $config->get('media_download_max_failures_percent') ?? 5,
      '#min' => 1,
      '#max' => 100,
      '#disabled' => TRUE,
      '#description' => $this->t('Not implemented yet. Media download failures are logged per item; no post-run KPI alert uses this threshold.'),
      '#attributes' => ['class' => ['ps-migrate-import-settings__not-implemented']],
    ];

    if ($this->moduleHandler->moduleExists('ps_core')) {
      $governanceUrl = Url::fromRoute('ps_core.governance');
      if ($governanceUrl->access($this->currentUser())) {
        $form['related'] = [
          '#type' => 'item',
          '#title' => $this->t('Related settings'),
          '#markup' => Link::fromTextAndUrl(
            $this->t('Import governance (domain lock rules and global defaults)'),
            $governanceUrl,
          )->toString(),
          '#wrapper_attributes' => ['class' => ['ps-migrate-import-settings__governance-link']],
        ];
      }
    }

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
      ->set('queue_enabled', (bool) $form_state->getValue('queue_enabled'))
      ->set('queue_process_on_cron', (bool) $form_state->getValue('queue_process_on_cron'))
      ->set('queue_items_per_cron', (int) $form_state->getValue('queue_items_per_cron'))
      ->set('alert_email_enabled', (bool) $form_state->getValue('alert_email_enabled'))
      ->set('alert_email_recipients', trim((string) $form_state->getValue('alert_email_recipients')))
      ->set('alert_email_on_warning', (bool) $form_state->getValue('alert_email_on_warning'))
      ->set('alert_skip_threshold_percent', (int) $form_state->getValue('alert_skip_threshold_percent'))
      ->set('post_run_index_solr', (bool) $form_state->getValue('post_run_index_solr'))
      ->set('post_run_search_api_index', trim((string) $form_state->getValue('post_run_search_api_index')))
      ->set('migration_order_full', $this->parseMigrationOrder((string) $form_state->getValue('migration_order_full')))
      ->set('migration_order_delta', $this->parseMigrationOrder((string) $form_state->getValue('migration_order_delta')))
      ->set('lock_strategy_default', (string) $form_state->getValue('lock_strategy_default'))
      ->set('lock_field_strategies', $this->parseFieldStrategies((string) $form_state->getValue('lock_field_strategies')))
      ->set('skip_unchanged_offers', (bool) $form_state->getValue('skip_unchanged_offers'))
      ->set('conflict_window_seconds', max(0, (int) $form_state->getValue('conflict_window_seconds')))
      ->set('media_download_timeout', (int) $form_state->getValue('media_download_timeout'))
      ->set('media_download_retry_count', (int) $form_state->getValue('media_download_retry_count'))
      ->save();

    $this->pathResolver->ensureConfiguredDirectories();

    parent::submitForm($form, $form_state);
  }

  /**
   * Formats configured migration order for textarea display.
   *
   * @param mixed $order
   *   Configured migration order sequence.
   */
  private function formatMigrationOrder(mixed $order): string {
    if (!is_array($order) || $order === []) {
      return '';
    }

    return implode("\n", array_map(static fn(mixed $id): string => trim((string) $id), $order));
  }

  /**
   * Parses a textarea migration order into a config sequence.
   *
   * @return list<string>
   *   Non-empty migration plugin IDs.
   */
  private function parseMigrationOrder(string $raw): array {
    $order = [];
    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
      $line = trim($line);
      if ($line !== '') {
        $order[] = $line;
      }
    }

    return $order;
  }

  /**
   * @param mixed $strategies
   */
  private function formatFieldStrategies(mixed $strategies): string {
    if (!is_array($strategies) || $strategies === []) {
      return '';
    }
    $lines = [];
    foreach ($strategies as $field => $strategy) {
      $lines[] = $field . '=' . $strategy;
    }
    return implode("\n", $lines);
  }

  /**
   * @return array<string, string>
   */
  private function parseFieldStrategies(string $raw): array {
    $mapping = [];
    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
      $line = trim($line);
      if ($line === '' || !str_contains($line, '=')) {
        continue;
      }
      [$field, $strategy] = array_map('trim', explode('=', $line, 2));
      if ($field === '' || $strategy === '') {
        continue;
      }
      $mapping[$field] = $strategy;
    }
    return $mapping;
  }

}
