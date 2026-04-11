<?php

declare(strict_types=1);

namespace Drupal\ps\Service;

/**
 * Interface for notification management.
 */
interface NotificationManagerInterface {

  /**
   * Send a notification.
   *
   * @param string $type
   *   The notification type (email, sms, etc.).
   * @param string $recipient
   *   The recipient identifier.
   * @param string $subject
   *   The notification subject.
   * @param string $body
   *   The notification body.
   * @param array<string, mixed> $options
   *   Optional parameters.
   *
   * @return bool
   *   TRUE if notification sent successfully.
   */
  public function send(string $type, string $recipient, string $subject, string $body, array $options = []): bool;

  /**
   * Validate recipient email.
   *
   * @param string $email
   *   The email address.
   *
   * @return bool
   *   TRUE if email is valid.
   */
  public function validateEmail(string $email): bool;

  /**
   * Get notification channels.
   *
   * @return array<string>
   *   Array of available notification channels.
   */
  public function getChannels(): array;

}
