<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;

/**
 * Mail hooks for search alert digest notifications.
 */
final class SearchAlertMailHooks {

  /**
   * Implements hook_mail().
   */
  #[Hook('mail')]
  public function mail(string $key, array &$message, array $params): void {
    if ($key !== 'search_alert_digest') {
      return;
    }

    $message['subject'] = (string) ($params['subject'] ?? '');
    $message['body'][] = Markup::create((string) ($params['body'] ?? ''));
    $message['headers']['Content-Type'] = 'text/html; charset=UTF-8';

    if (!empty($params['from_mail'])) {
      $message['from'] = (string) $params['from_mail'];
      if (!empty($params['from_name'])) {
        $message['headers']['From'] = sprintf('%s <%s>', $params['from_name'], $params['from_mail']);
      }
    }
    if (!empty($params['bcc'])) {
      $message['headers']['Bcc'] = (string) $params['bcc'];
    }
  }

}
