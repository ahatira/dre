<?php

declare(strict_types=1);

namespace Drupal\ps_core\Hook;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\ps_core\Processor\EmailLogoAttachmentProcessor;
use Drupal\ps_core\Service\EmailBrandingBuilder;
use Drupal\symfony_mailer\EmailInterface;

/**
 * Central mail hooks for Property Search.
 */
final class MailHooks {

  /**
   * BNP Paribas Real Estate primary green.
   */
  private const PRIMARY_COLOR = '#00915a';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailBrandingBuilder $emailBrandingBuilder,
    private readonly EmailLogoAttachmentProcessor $emailLogoAttachmentProcessor,
  ) {}

  /**
   * Ensures legacy HTML mail bodies are not escaped by Symfony Mailer.
   */
  #[Hook('mail_alter')]
  public function mailAlter(array &$message): void {
    $contentType = $message['headers']['Content-Type'] ?? '';
    if (!is_string($contentType) || stripos($contentType, 'text/html') === FALSE) {
      return;
    }

    if (!isset($message['body']) || !is_array($message['body'])) {
      return;
    }

    foreach ($message['body'] as $index => $part) {
      if ($part instanceof MarkupInterface || !is_string($part)) {
        continue;
      }
      $message['body'][$index] = Markup::create($part);
    }
  }

  /**
   * Registers the header logo CID processor on every Symfony Mailer email.
   */
  #[Hook('mailer_init')]
  public function mailerInit(EmailInterface $email): void {
    $email->addProcessor($this->emailLogoAttachmentProcessor);
  }

  /**
   * Adds site-wide variables to the generic email wrapper.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    $siteConfig = $this->configFactory->get('system.site');
    $siteName = (string) ($siteConfig->get('name') ?? 'Property Search');

    $variables['site_name'] = $siteName;
    $variables['site_slogan'] = $this->emailBrandingBuilder->getSiteSlogan();
    $variables['site_url'] = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $variables['email_primary_color'] = self::PRIMARY_COLOR;
    $variables['email_team_name'] = (string) t('The @team team', ['@team' => $siteName]);
    $variables['email_logo_url'] = $this->emailBrandingBuilder->getHeaderLogoUrl();
  }

}
