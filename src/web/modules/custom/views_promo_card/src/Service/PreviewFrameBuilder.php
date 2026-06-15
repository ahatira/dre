<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Render\BareHtmlPageRendererInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds a full HTML document for admin preview iframes (ps_theme CSS).
 */
final class PreviewFrameBuilder {

  /**
   * Constructs a PreviewFrameBuilder.
   */
  public function __construct(
    private readonly BareHtmlPageRendererInterface $bareHtmlPageRenderer,
    private readonly PatternRegistry $patternRegistry,
  ) {}

  /**
   * Wraps a render array in a front-themed HTML document for iframe srcdoc.
   *
   * @param array<string, mixed> $content
   *   Inner preview render array.
   * @param string $pattern_id
   *   SDC pattern ID used to attach component libraries.
   */
  public function buildDocument(array $content, string $pattern_id = ''): Response {
    $libraries = [
      'ps_theme/framework',
      'views_promo_card/promo_card_preview_frame',
    ];

    $library_id = $this->patternRegistry->getComponentLibraryId($pattern_id);
    if ($library_id !== NULL) {
      $libraries[] = $library_id;
    }

    $response = $this->bareHtmlPageRenderer->renderBarePage(
      $content,
      '',
      'promo_card_preview_page',
      [
        '#show_messages' => FALSE,
        '#attached' => [
          'library' => $libraries,
        ],
      ],
    );

    $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
    return $response;
  }

}
