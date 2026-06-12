<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\ps_search\Service\SearchAlertMailer;
use Drupal\ps_search\Service\SearchAlertMatcher;
use Psr\Log\LoggerInterface;

/**
 * Cron hooks for search alert digest notifications.
 */
final class SearchAlertCronHooks {

  private const SETTINGS_CONFIG = 'ps_search.alert_settings';

  private LoggerInterface $logger;

  public function __construct(
    private readonly SearchAlertMatcher $matcher,
    private readonly SearchAlertMailer $mailer,
    private readonly ConfigFactoryInterface $configFactory,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->logger = $loggerFactory->get('ps_search');
  }

  /**
   * Sends due search alert digests during Drupal cron.
   */
  public function processAlerts(): int {
    $sent = 0;
    foreach ($this->matcher->processBatch() as $payload) {
      $alert = $payload['alert'];
      $nids = $payload['nids'];
      if ($this->mailer->sendDigest($alert, $nids)) {
        $this->mailer->markSent($alert, $nids);
        $sent++;
      }
    }

    if ($sent > 0) {
      $this->logger->notice('Sent @count search alert digest(s).', ['@count' => $sent]);
    }

    return $sent;
  }

  /**
   * Purges expired anonymous alerts when cron notifications are enabled.
   */
  public function purgeExpiredAnonymous(): int {
    if (!(bool) $this->configFactory->get(self::SETTINGS_CONFIG)->get('enabled')) {
      return 0;
    }

    try {
      return $this->matcher->purgeExpiredAnonymous();
    }
    catch (\Throwable $exception) {
      $this->logger->error('Search alert purge failed: @message', ['@message' => $exception->getMessage()]);
      return 0;
    }
  }

}
