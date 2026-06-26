<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_migrate\Entity\ImportRunInterface;

/**
 * Mail hooks for CRM import pipeline alerts.
 */
final class ImportPipelineMailHooks {

  use StringTranslationTrait;

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
    $message['body'][] = (string) $this->t('Import run #@id failed for file @file.', [
      '@id' => $run->id(),
      '@file' => $run->getFilename(),
    ]);
    $message['body'][] = (string) $this->t('Mode: @mode', ['@mode' => $run->getImportMode()]);
    $message['body'][] = (string) $this->t('Error: @error', ['@error' => $error]);
    $message['body'][] = (string) $this->t('Review details in the import runs back office.');
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
    $message['body'][] = (string) $this->t('Import run #@id completed with a high skip rate for file @file.', [
      '@id' => $run->id(),
      '@file' => $run->getFilename(),
    ]);
    $message['body'][] = (string) $this->t('Mode: @mode', ['@mode' => $run->getImportMode()]);
    $message['body'][] = (string) $this->t('Skip rate: @rate% (threshold: @threshold%).', [
      '@rate' => number_format($skipRate, 1),
      '@threshold' => $threshold,
    ]);
    $message['body'][] = (string) $this->t('Review skipped rows and import rejections in the back office.');
  }

}
