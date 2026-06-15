<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\views\ViewExecutable;
use Drupal\views_promo_card\Service\InsertionManager;

/**
 * Views preprocess hooks for promo card injection.
 */
final class ViewsPromoCardHooks {

  /**
   * Constructs ViewsPromoCardHooks.
   */
  public function __construct(
    private readonly InsertionManager $insertionManager,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for unformatted views.
   */
  #[Hook('preprocess_views_view_unformatted')]
  public function preprocessUnformatted(array &$variables): void {
    $this->injectSlots($variables);
  }

  /**
   * Implements hook_preprocess_HOOK() for grid views.
   */
  #[Hook('preprocess_views_view_grid')]
  public function preprocessGrid(array &$variables): void {
    $this->injectSlots($variables);
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for unformatted views.
   */
  #[Hook('theme_suggestions_views_view_unformatted_alter')]
  public function suggestionsUnformatted(array &$suggestions, array $variables): void {
    $this->appendSuggestion($suggestions, $variables);
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for grid views.
   */
  #[Hook('theme_suggestions_views_view_grid_alter')]
  public function suggestionsGrid(array &$suggestions, array $variables): void {
    $this->appendSuggestion($suggestions, $variables);
  }

  /**
   * Injects promo card slots into view variables.
   */
  private function injectSlots(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable) {
      return;
    }
    $pageRowCount = count($variables['rows'] ?? []);
    $slots = $this->insertionManager->buildSlots($view, $pageRowCount);
    if ($slots !== []) {
      $variables['promo_card_slots'] = $slots;
      $variables['#attached']['library'][] = 'views_promo_card/promo_card_slot';
    }
  }

  /**
   * Appends promo_card theme suggestion when slots exist.
   */
  private function appendSuggestion(array &$suggestions, array $variables): void {
    if (empty($variables['promo_card_slots'])) {
      return;
    }
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable) {
      $suggestions[] = 'views_view_unformatted__promo_card';
      return;
    }
    $base = str_contains((string) ($variables['theme_hook_original'] ?? ''), 'grid')
      ? 'views_view_grid'
      : 'views_view_unformatted';
    $suggestions[] = $base . '__promo_card';
    $suggestions[] = $base . '__' . $view->id() . '__' . $view->current_display . '__promo_card';
  }

}
