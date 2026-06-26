<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipelineAlertNotifier;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * Tests skip-rate warning alerts for the CRM import pipeline.
 */
#[Group('ps_migrate')]
final class ImportPipelineAlertNotifierTest extends UnitTestCase {

  /**
   * Ensures skip rate is computed from aggregate migration counters.
   */
  public function testCalculateSkipRatePercent(): void {
    $notifier = $this->createNotifier(
      alertOnWarning: FALSE,
      threshold: 10,
      recipients: '',
    );

    $rate = $notifier->calculateSkipRatePercent([
      'migrations' => [
        'ps_offer_from_xml' => [
          'imported' => 80,
          'updated' => 10,
          'skipped' => 10,
          'failed' => 0,
        ],
        'ps_agent_from_xml' => [
          'imported' => 18,
          'updated' => 0,
          'skipped' => 2,
          'failed' => 0,
        ],
      ],
    ]);

    self::assertSame(10.0, $rate);
  }

  /**
   * Ensures warning emails are sent when skip rate exceeds the threshold.
   */
  public function testNotifySkipWarningWhenThresholdExceeded(): void {
    $run = $this->createMock(ImportRunInterface::class);
    $run->method('id')->willReturn('42');
    $run->method('getFilename')->willReturn('offers.xml');
    $run->method('getImportMode')->willReturn('full');

    $mailManager = $this->createMock(MailManagerInterface::class);
    $mailManager->expects(self::once())
      ->method('mail')
      ->with(
        'ps_migrate',
        'import_pipeline_skip_warning',
        'ops@example.com',
        self::anything(),
        self::callback(static function (array $params): bool {
          return ($params['skip_rate'] ?? NULL) === 20.0 && ($params['threshold'] ?? NULL) === 10;
        }),
      )
      ->willReturn(['result' => TRUE]);

    $notifier = $this->createNotifier(
      alertOnWarning: TRUE,
      threshold: 10,
      recipients: 'ops@example.com',
      mailManager: $mailManager,
    );
    $notifier->setStringTranslation($this->getStringTranslationStub());

    $notifier->notifySkipWarningIfNeeded($run, [
      'migrations' => [
        'ps_offer_from_xml' => [
          'imported' => 40,
          'updated' => 0,
          'skipped' => 10,
          'failed' => 0,
        ],
      ],
    ]);
  }

  /**
   * Ensures warning emails are not sent below the configured threshold.
   */
  public function testNotifySkipWarningSkippedBelowThreshold(): void {
    $run = $this->createMock(ImportRunInterface::class);

    $mailManager = $this->createMock(MailManagerInterface::class);
    $mailManager->expects(self::never())->method('mail');

    $notifier = $this->createNotifier(
      alertOnWarning: TRUE,
      threshold: 10,
      recipients: 'ops@example.com',
      mailManager: $mailManager,
    );

    $notifier->notifySkipWarningIfNeeded($run, [
      'migrations' => [
        'ps_offer_from_xml' => [
          'imported' => 95,
          'updated' => 0,
          'skipped' => 5,
          'failed' => 0,
        ],
      ],
    ]);
  }

  /**
   * Builds a notifier instance with mocked dependencies.
   */
  private function createNotifier(
    bool $alertOnWarning,
    int $threshold,
    string $recipients,
    ?MailManagerInterface $mailManager = NULL,
  ): ImportPipelineAlertNotifier {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['alert_email_enabled', TRUE],
      ['alert_email_on_warning', $alertOnWarning],
      ['alert_skip_threshold_percent', $threshold],
      ['alert_email_recipients', $recipients],
    ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    $language = $this->createMock(Language::class);
    $language->method('getId')->willReturn('en');

    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getDefaultLanguage')->willReturn($language);

    return new ImportPipelineAlertNotifier(
      $configFactory,
      $mailManager ?? $this->createMock(MailManagerInterface::class),
      $languageManager,
      $this->createMock(LoggerInterface::class),
    );
  }

}
