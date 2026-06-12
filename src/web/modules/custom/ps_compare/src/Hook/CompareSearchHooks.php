<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_compare\Service\ComparePanelBuilder;
use Drupal\views\ViewExecutable;

/**
 * Injects compare UI on the property search results view.
 */
final class CompareSearchHooks {

  public function __construct(
    private readonly ComparePanelBuilder $panelBuilder,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable || $view->id() !== 'ps_search_offers') {
      return;
    }

    if (($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    $variables['compare_widget'] = $this->panelBuilder->buildWidget();
    $variables['search_mobile_bottom_bar'] = [
      '#theme' => 'ps_search_mobile_bottom_bar',
    ];
    $variables['compare_modal'] = [
      '#theme' => 'ps_compare_modal',
    ];
    $variables['compare_share_modal'] = [
      '#theme' => 'ps_compare_share_modal',
    ];
  }

  /**
   * Ensures compare assets load on the property search page.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if (\Drupal::routeMatch()->getRouteName() !== 'view.ps_search_offers.page_list') {
      return;
    }

    $widget = $this->panelBuilder->buildWidget();
    $attachments['#attached'] = BubbleableMetadata::mergeAttachments(
      $attachments['#attached'] ?? [],
      $widget['#attached'] ?? [],
    );
  }

}
