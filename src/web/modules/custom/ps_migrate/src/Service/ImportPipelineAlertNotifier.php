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
 * Sends email alerts for CRM import pipeline failures and warnings.
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
    if (!$this->isEmailEnabled()) {
      return;
    }

    $recipients = $this->getRecipients();
    if ($recipients === []) {
      $this->logger->warning('Import failure alert skipped: no alert_email_recipients configured.');
      return;
    }

    $this->sendMail(
      'import_pipeline_failed',
      $recipients,
      [
        'run' => $run,
        'error' => $errorMessage,
        'subject' => (string) $this->t('CRM import failed: @file', ['@file' => $run->getFilename()]),
      ],
    );
  }

  /**
   * Sends a warning when aggregate skip rate exceeds the configured threshold.
   *
   * @param \Drupal\ps_migrate\Entity\ImportRunInterface $run
   *   Completed import run.
   * @param array<string, mixed> $migrateStats
   *   Stats returned by ImportPipelineMigrateRunner::run().
   */
  public function notifySkipWarningIfNeeded(ImportRunInterface $run, array $migrateStats): void {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    if (!(bool) $config->get('alert_email_on_warning')) {
      return;
    }

    if (!$this->isEmailEnabled()) {
      return;
    }

    $threshold = max(0, min(100, (int) $config->get('alert_skip_threshold_percent')));
    $skipRate = $this->calculateSkipRatePercent($migrateStats);
    if ($skipRate === NULL || $skipRate < $threshold) {
      return;
    }

    $recipients = $this->getRecipients();
    if ($recipients === []) {
      $this->logger->warning('Import skip warning alert skipped: no alert_email_recipients configured.');
      return;
    }

    $this->sendMail(
      'import_pipeline_skip_warning',
      $recipients,
      [
        'run' => $run,
        'skip_rate' => $skipRate,
        'threshold' => $threshold,
        'subject' => (string) $this->t('CRM import skip warning: @file (@rate%)', [
          '@file' => $run->getFilename(),
          '@rate' => number_format($skipRate, 1),
        ]),
      ],
    );
  }

  /**
   * Calculates aggregate skip rate across migration stats.
   *
   * @param array<string, mixed> $migrateStats
   *   Stats returned by ImportPipelineMigrateRunner::run().
   */
  public function calculateSkipRatePercent(array $migrateStats): ?float {
    $migrations = $migrateStats['migrations'] ?? [];
    if (!is_array($migrations) || $migrations === []) {
      return NULL;
    }

    $skipped = 0;
    $processed = 0;
    foreach ($migrations as $stats) {
      if (!is_array($stats)) {
        continue;
      }
      $skipped += (int) ($stats['skipped'] ?? 0);
      $processed += (int) ($stats['imported'] ?? 0);
      $processed += (int) ($stats['updated'] ?? 0);
      $processed += (int) ($stats['skipped'] ?? 0);
      $processed += (int) ($stats['failed'] ?? 0);
    }

    if ($processed === 0) {
      return NULL;
    }

    return round(($skipped / $processed) * 100, 2);
  }

  /**
   * Sends a pipeline alert email to all recipients.
   *
   * @param string $key
   *   Mail plugin key.
   * @param list<string> $recipients
   *   Recipient email addresses.
   * @param array<string, mixed> $params
   *   Mail parameters.
   */
  private function sendMail(string $key, array $recipients, array $params): void {
    $langcode = $this->languageManager->getDefaultLanguage()->getId();
    foreach ($recipients as $recipient) {
      $result = $this->mailManager->mail(
        'ps_migrate',
        $key,
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
   * Checks whether pipeline alert emails are enabled.
   */
  private function isEmailEnabled(): bool {
    return (bool) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('alert_email_enabled');
  }

  /**
   * Loads configured alert recipients from pipeline settings.
   *
   * @return list<string>
   *   Valid recipient email addresses.
   */
  private function getRecipients(): array {
    $raw = (string) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('alert_email_recipients');
    return $this->parseRecipients($raw);
  }

  /**
   * Parses a raw recipient list into valid email addresses.
   *
   * @return list<string>
   *   Valid recipient email addresses.
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
