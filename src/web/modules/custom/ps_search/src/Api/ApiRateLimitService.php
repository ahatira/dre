<?php

declare(strict_types=1);

namespace Drupal\ps_search\Api;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * IP-based rate limiting for ps_search public API routes.
 */
final class ApiRateLimitService {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FloodInterface $flood,
    private readonly AccountProxyInterface $currentUser,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Returns TRUE when the request is allowed under configured limits.
   */
  public function isAllowed(Request $request, string $bucket): bool {
    $config = $this->configFactory->get('ps_search.api_rate_limit_settings');
    if (!($config->get('enabled') ?? TRUE)) {
      return TRUE;
    }

    if ($this->currentUser->hasPermission('bypass ps search api rate limit')) {
      return TRUE;
    }

    $window = max(1, (int) ($config->get('window_seconds') ?? 60));
    $defaultLimit = max(1, (int) ($config->get('default_max_requests') ?? 120));
    $routeLimits = $config->get('routes') ?? [];
    $limit = max(1, (int) ($routeLimits[$bucket] ?? $defaultLimit));

    $identifier = $request->getClientIp() ?: 'unknown';
    $event = 'ps_search.api.' . $bucket;

    if ($this->flood->isAllowed($event, $limit, $window, $identifier)) {
      return TRUE;
    }

    $this->logger->warning('API rate limit exceeded for bucket @bucket from @ip.', [
      '@bucket' => $bucket,
      '@ip' => $identifier,
    ]);

    return FALSE;
  }

}
