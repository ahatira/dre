<?php

declare(strict_types=1);

namespace Drupal\ps_email\Processor;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\File\FileSystemInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\symfony_mailer\Attachment;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorTrait;

/**
 * Embeds offer card images as CID attachments in transactional emails.
 */
final class OfferEmailCardImageAttachmentProcessor implements EmailProcessorInterface {

  use EmailProcessorTrait;

  public const CONTENT_ID = 'ps-offer-card-image@ps-project';

  private const IMAGE_STYLE = 'bnp_media_admin_card';

  public function __construct(
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function postRender(EmailInterface $email): void {
    $html = $email->getHtmlBody();
    if ($html === NULL || $html === '' || !str_contains($html, 'data-ps-offer-image-uri=')) {
      return;
    }

    if (preg_match('/<img[^>]+data-ps-offer-image-uri="([^"]+)"[^>]*>/i', $html, $match) !== 1) {
      return;
    }

    $payload = $this->readStyledImagePayload($match[1]);
    if ($payload === NULL) {
      return;
    }

    [$contents, $mimeType, $filename] = $payload;
    $attachment = Attachment::fromData($contents, $filename, $mimeType);
    $attachment->setContentId(self::CONTENT_ID);
    $attachment->setAccess(AccessResult::allowed());
    $email->attach($attachment);

    $replacement = preg_replace('/src="[^"]*"/', 'src="cid:' . self::CONTENT_ID . '"', $match[0], 1);
    if (!is_string($replacement)) {
      return;
    }

    $replacement = preg_replace('/\s*data-ps-offer-image-uri="[^"]*"/', '', $replacement, 1);
    if (!is_string($replacement)) {
      return;
    }

    $email->setHtmlBody(str_replace($match[0], $replacement, $html));
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(int $phase): int {
    return $phase === EmailInterface::PHASE_POST_RENDER ? 860 : EmailInterface::DEFAULT_WEIGHT;
  }

  /**
   * @return array{0: string, 1: string, 2: string}|null
   *   File contents, MIME type and attachment filename.
   */
  private function readStyledImagePayload(string $uri): ?array {
    $style = ImageStyle::load(self::IMAGE_STYLE);
    $resolvedUri = $uri;
    if ($style !== NULL) {
      $derivativeUri = $style->buildUri($uri);
      if (!file_exists($derivativeUri)) {
        $style->createDerivative($uri, $derivativeUri);
      }
      if (is_readable($derivativeUri)) {
        $resolvedUri = $derivativeUri;
      }
    }

    $path = $this->fileSystem->realpath($resolvedUri);
    if ($path === FALSE || !is_readable($path)) {
      return NULL;
    }

    $contents = file_get_contents($path);
    if ($contents === FALSE || $contents === '') {
      return NULL;
    }

    return [$contents, $this->guessMimeType($path), basename($path)];
  }

  private function guessMimeType(string $path): string {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return match ($extension) {
      'jpg', 'jpeg' => 'image/jpeg',
      'png' => 'image/png',
      'gif' => 'image/gif',
      'webp' => 'image/webp',
      'svg' => 'image/svg+xml',
      default => 'application/octet-stream',
    };
  }

}
