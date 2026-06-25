<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Psr\Log\LoggerInterface;

/**
 * Sends email alerts for CRM import pipeline failures.
 */
final class ImportPipelineAlertNotifier {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly MailManagerInterface $mailManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Sends a failure notification when enabled in pipeline settings.
   */
  public function notifyFailure(ImportRunInterface $run, string $errorMessage): void {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    if (!(bool) $config->get('alert_email_enabled')) {
      return;
    }

    $recipients = $this->parseRecipients((string) $config->get('alert_email_recipients'));
    if ($recipients === []) {
      $this->logger->warning('Import failure alert skipped: no alert_email_recipients configured.');
      return;
    }

    $langcode = $this->languageManager->getDefaultLanguage()->getId();
    $params = [
      'run' => $run,
      'error' => $errorMessage,
      'subject' => (string) $this->t('CRM import failed: @file', ['@file' => $run->getFilename()]),
    ];

    foreach ($recipients as $recipient) {
      $result = $this->mailManager->mail(
        'ps_migrate',
        'import_pipeline_failed',
        $recipient,
        $langcode,
        $params,
      );
      if (empty($result['result'])) {
        $this->logger->error('Failed to send import alert email to @recipient.', [
          '@recipient' => $recipient,
        ]);
      }
    }
  }

  /**
   * @return list<string>
   */
  private function parseRecipients(string $raw): array {
    if ($raw === '') {
      return [];
    }

    $parts = preg_split('/[\s,;]+/', $raw) ?: [];
    $recipients = [];
    foreach ($parts as $part) {
      $part = trim($part);
      if ($part !== '' && filter_var($part, FILTER_VALIDATE_EMAIL)) {
        $recipients[] = $part;
      }
    }

    return array_values(array_unique($recipients));
  }

}
