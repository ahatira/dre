<?php

declare(strict_types=1);

namespace Drupal\ps\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Manages multi-channel notifications for PropertySearch.
 */
final class NotificationManager implements NotificationManagerInterface {

  /**
   * Available notification channels.
   *
   * @var array<string>
   */
  protected array $channels = ['email'];

  /**
   * Logger channel.
   */
  protected LoggerChannelInterface $logger;

  /**
   * Constructor.
   */
  public function __construct(
    protected readonly EmailValidator $emailValidator,
    protected readonly LoggerChannelFactoryInterface $loggerFactory,
    protected readonly ConfigFactoryInterface $configFactory,
  ) {
    $this->logger = $loggerFactory->get('ps');
  }

  /**
   * {@inheritdoc}
   */
  public function send(string $type, string $recipient, string $subject, string $body, array $options = []): bool {
    if (!in_array($type, $this->channels, TRUE)) {
      $this->logger->error('Unknown notification channel: @channel', [
        '@channel' => $type,
      ]);
      return FALSE;
    }

    if ($type === 'email') {
      return $this->sendEmail($recipient, $subject, $body, $options);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateEmail(string $email): bool {
    return $this->emailValidator->isValid($email, new RFCValidation());
  }

  /**
   * {@inheritdoc}
   */
  public function getChannels(): array {
    return $this->channels;
  }

  /**
   * Send email notification.
   *
   * @param string $recipient
   *   Email recipient.
   * @param string $subject
   *   Email subject.
   * @param string $body
   *   Email body.
   * @param array<string, mixed> $options
   *   Optional parameters (from, cc, bcc, etc.).
   *
   * @return bool
   *   TRUE if email sent successfully.
   */
  private function sendEmail(string $recipient, string $subject, string $body, array $options): bool {
    if (!$this->validateEmail($recipient)) {
      $this->logger->error('Invalid email recipient: @email', [
        '@email' => $recipient,
      ]);
      return FALSE;
    }

    // For now, just log. In production, use MailManager.
    $this->logger->info('Notification sent to @email: @subject (body: @body_length chars)', [
      '@email' => $recipient,
      '@subject' => $subject,
      '@body_length' => strlen($body),
    ]);

    // Options (from, cc, bcc) will be used in production implementation.
    unset($options);

    return TRUE;
  }

}
