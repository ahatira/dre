<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_form\Service\ContactEmailPreviewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves isolated HTML documents for admin email preview iframes.
 */
final class ContactEmailPreviewFrameController extends ControllerBase {

  public function __construct(
    private readonly ContactEmailPreviewBuilder $previewBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_form.contact_email_preview_builder'),
    );
  }

  /**
   * Returns a standalone HTML email document for iframe embedding.
   */
  public function frame(string $webform): Response {
    $html = $this->previewBuilder->renderPreviewHtml($webform);
    if ($html === NULL) {
      return new Response($this->t('Preview unavailable for this webform.'), Response::HTTP_NOT_FOUND);
    }

    return new Response($html, Response::HTTP_OK, [
      'Content-Type' => 'text/html; charset=UTF-8',
      'Cache-Control' => 'no-store, no-cache, must-revalidate',
    ]);
  }

}
