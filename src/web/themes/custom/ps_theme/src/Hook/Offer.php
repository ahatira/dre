<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\ps_theme\Utility\OfferCardPropsBuilder;

/**
 * Offer node preprocess hooks.
 */
final class Offer {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_node')]
  public function preprocessNode(array &$variables): void {
    $node = $variables['node'] ?? NULL;
    if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
      return;
    }

    $view_mode = (string) ($variables['view_mode'] ?? '');

    if ($view_mode === 'teaser') {
      $variables['offer_card'] = OfferCardPropsBuilder::build($node);
      return;
    }

    if ($view_mode === 'full') {
      $variables['#attached']['library'][] = 'ps_theme/offer-detail';
    }
  }

  /**
   * Adds layout section modifiers on offer full Layout Builder sections.
   */
  #[Hook('preprocess_layout')]
  public function preprocessLayout(array &$variables): void {
    $route_node = \Drupal::routeMatch()->getParameter('node');
    if (!$route_node instanceof NodeInterface || $route_node->bundle() !== 'offer') {
      return;
    }

    $label = (string) ($variables['settings']['label'] ?? '');
    if ($label === '') {
      return;
    }

    $slug = Html::cleanCssIdentifier(strtolower($label));
    $variables['attributes']['class'][] = 'ps-offer-layout';
    $variables['attributes']['class'][] = 'ps-offer-layout--' . $slug;

    if ($slug === 'main') {
      $variables['attributes']['class'][] = 'container';
      if (isset($variables['region_attributes']['first'])) {
        $variables['region_attributes']['first']->addClass('ps-offer-layout__main-content');
      }
    }

    if ($slug === 'map') {
      $variables['attributes']['class'][] = 'container-fluid';
      $variables['attributes']['class'][] = 'px-0';
    }
  }

}
