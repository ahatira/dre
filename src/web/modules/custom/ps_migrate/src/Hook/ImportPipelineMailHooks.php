<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\Service\EmailDesignTokens;
use Drupal\ps_migrate\Entity\ImportRunInterface;

/**
 * Mail hooks for CRM import pipeline alerts.
 */
final class ImportPipelineMailHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly RendererInterface $renderer,
    private readonly EmailDesignTokens $emailDesignTokens,
  ) {}

  /**
   * Registers import alert body theme hook.
   *
   * @return array<string, mixed>
   *   Theme definitions.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_migrate_import_alert_body' => [
        'variables' => [
          'lines' => [],
          'admin_url' => NULL,
          'alert_variant' => 'warning',
        ],
        'template' => 'ps-migrate-import-alert-body',
      ],
    ];
  }

  /**
   * Injects design tokens into the import alert body fragment.
   */
  #[Hook('preprocess_ps_migrate_import_alert_body')]
  public function preprocessImportAlertBody(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
  }

  /**
   * Implements hook_mail().
   *
   * @param string $key
   *   Mail plugin key.
   * @param array<string, mixed> $message
   *   Message structure passed by reference.
   * @param array<string, mixed> $params
   *   Mail parameters.
   */
  #[Hook('mail')]
  public function mail(string $key, &$message, $params): void {
    if ($key === 'import_pipeline_failed') {
      $this->buildFailureMail($message, $params);
      return;
    }

    if ($key === 'import_pipeline_skip_warning') {
      $this->buildSkipWarningMail($message, $params);
    }
  }

  /**
   * Builds the failure alert mail body.
   *
   * @param array<string, mixed> $message
   *   Message structure passed by reference.
   * @param array<string, mixed> $params
   *   Mail parameters.
   */
  private function buildFailureMail(array &$message, array $params): void {
    /** @var \Drupal\ps_migrate\Entity\ImportRunInterface $run */
    $run = $params['run'] ?? NULL;
    if (!$run instanceof ImportRunInterface) {
      return;
    }

    $error = (string) ($params['error'] ?? '');
    $message['subject'] = (string) ($params['subject'] ?? $this->t('CRM import failed'));
    $build = [
      '#theme' => 'ps_migrate_import_alert_body',
      '#lines' => [
        (string) $this->t('Import run #@id failed for file @file.', [
          '@id' => $run->id(),
          '@file' => $run->getFilename(),
        ]),
        (string) $this->t('Mode: @mode', ['@mode' => $run->getImportMode()]),
        (string) $this->t('Error: @error', ['@error' => $error]),
      ],
      '#admin_url' => $this->buildRunAdminUrl($run),
      '#alert_variant' => 'warning',
    ];
    $message['body'][] = Markup::create((string) $this->renderer->renderPlain($build));
    $message['headers']['Content-Type'] = 'text/html; charset=UTF-8';
  }

  /**
   * Builds the skip warning alert mail body.
   *
   * @param array<string, mixed> $message
   *   Message structure passed by reference.
   * @param array<string, mixed> $params
   *   Mail parameters.
   */
  private function buildSkipWarningMail(array &$message, array $params): void {
    /** @var \Drupal\ps_migrate\Entity\ImportRunInterface $run */
    $run = $params['run'] ?? NULL;
    if (!$run instanceof ImportRunInterface) {
      return;
    }

    $skipRate = (float) ($params['skip_rate'] ?? 0);
    $threshold = (int) ($params['threshold'] ?? 0);
    $message['subject'] = (string) ($params['subject'] ?? $this->t('CRM import skip warning'));
    $build = [
      '#theme' => 'ps_migrate_import_alert_body',
      '#lines' => [
        (string) $this->t('Import run #@id completed with a high skip rate for file @file.', [
          '@id' => $run->id(),
          '@file' => $run->getFilename(),
        ]),
        (string) $this->t('Mode: @mode', ['@mode' => $run->getImportMode()]),
        (string) $this->t('Skip rate: @rate% (threshold: @threshold%).', [
          '@rate' => number_format($skipRate, 1),
          '@threshold' => $threshold,
        ]),
        (string) $this->t('Review skipped rows and import rejections in the back office.'),
      ],
      '#admin_url' => $this->buildRunAdminUrl($run),
      '#alert_variant' => 'warning',
    ];
    $message['body'][] = Markup::create((string) $this->renderer->renderPlain($build));
    $message['headers']['Content-Type'] = 'text/html; charset=UTF-8';
  }

  /**
   * Builds an absolute URL to the import run detail page in the BO.
   */
  private function buildRunAdminUrl(ImportRunInterface $run): string {
    return Url::fromRoute('entity.import_run.canonical', [
      'import_run' => $run->id(),
    ], ['absolute' => TRUE])->toString();
  }

}
