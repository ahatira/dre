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
   * @param array<string, mixed> $message
   * @param array<string, mixed> $params
   */
  #[Hook('mail')]
  public function mail(string $key, &$message, $params): void {
    if ($key !== 'import_pipeline_failed') {
      return;
    }

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

}
