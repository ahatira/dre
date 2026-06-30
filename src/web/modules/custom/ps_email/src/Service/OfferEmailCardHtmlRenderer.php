<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Render\RendererInterface;

/**
 * Renders table-safe HTML offer cards for transactional emails.
 */
final class OfferEmailCardHtmlRenderer {

  public function __construct(
    private readonly RendererInterface $renderer,
    private readonly EmailDesignTokens $emailDesignTokens,
  ) {}

  /**
   * Renders a vertical offer card (image top).
   *
   * @param array<string, mixed> $props
   *   Card props from OfferEmailCardPropsBuilder.
   */
  public function renderVertical(array $props): string {
    return $this->render('offer_email_card_vertical', $props);
  }

  /**
   * Renders a compact offer card (no image).
   *
   * @param array<string, mixed> $props
   *   Card props from OfferEmailCardPropsBuilder.
   */
  public function renderCompact(array $props): string {
    return $this->render('offer_email_card_compact', $props);
  }

  /**
   * @param array<string, mixed> $props
   */
  private function render(string $theme, array $props): string {
    $build = ['#theme' => $theme];
    foreach ($props + $this->emailDesignTokens->getPreprocessVariables() as $key => $value) {
      $build['#' . $key] = $value;
    }

    return (string) $this->renderer->renderPlain($build);
  }

}
