<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Uses the default (front) theme for promo card preview routes.
 *
 * Ensures SDC components render with front-theme Twig templates during
 * admin live preview requests, similar to block.admin_demo.
 */
final class PreviewThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Routes that should render with the front theme.
   *
   * @var list<string>
   */
  private const PREVIEW_ROUTES = [
    'views_promo_card.card_preview',
    'views_promo_card.placement_preview',
  ];

  /**
   * Constructs a PreviewThemeNegotiator.
   */
  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return in_array($route_match->getRouteName(), self::PREVIEW_ROUTES, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match): ?string {
    $default = (string) $this->configFactory->get('system.theme')->get('default');
    return $default !== '' ? $default : NULL;
  }

}
