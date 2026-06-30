<?php

declare(strict_types=1);

namespace Drupal\ps_email\Processor;

use Drupal\Core\Access\AccessResult;
use Drupal\ps_email\Service\EmailBrandingBuilder;
use Drupal\symfony_mailer\Attachment;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorTrait;

/**
 * Embeds the theme header logo as a CID attachment after the email wrapper.
 */
final class EmailLogoAttachmentProcessor implements EmailProcessorInterface {

  use EmailProcessorTrait;

  /**
   * Fixed Content-ID for the header logo (Outlook/Gmail friendly).
   */
  public const CONTENT_ID = 'ps-email-header-logo@ps-project';

  public function __construct(
    private readonly EmailBrandingBuilder $emailBrandingBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function postRender(EmailInterface $email): void {
    $html = $email->getHtmlBody();
    if ($html === NULL || $html === '') {
      return;
    }

    $logoUrl = $this->emailBrandingBuilder->getHeaderLogoUrl();
    if ($logoUrl === NULL || !str_contains($html, $logoUrl)) {
      return;
    }

    $path = $this->emailBrandingBuilder->getHeaderLogoPath();
    $mimeType = $this->emailBrandingBuilder->getHeaderLogoMimeType();
    $filename = $this->emailBrandingBuilder->getHeaderLogoFilename();
    if ($path === NULL || $mimeType === NULL || $filename === NULL) {
      return;
    }

    $contents = file_get_contents($path);
    if ($contents === FALSE || $contents === '') {
      return;
    }

    $attachment = Attachment::fromData($contents, $filename, $mimeType);
    $attachment->setContentId(self::CONTENT_ID);
    $attachment->setAccess(AccessResult::allowed());
    $email->attach($attachment);

    $email->setHtmlBody(str_replace($logoUrl, 'cid:' . self::CONTENT_ID, $html));
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(int $phase): int {
    return $phase === EmailInterface::PHASE_POST_RENDER ? 850 : EmailInterface::DEFAULT_WEIGHT;
  }

}
