<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\node\NodeInterface;
use Drupal\ps_theme\Utility\OfferCardPropsBuilder;

/**
 * Builds offer-teaser-card SDC render arrays for homepage blocks.
 */
final class HomepageOfferTeaserBuilder {

  /**
   * @param array{show_favorite?: bool, show_compare?: bool} $options
   *
   * @return array<string, mixed>
   */
  public function build(NodeInterface $node, array $options = []): array {
    $showFavorite = (bool) ($options['show_favorite'] ?? TRUE);
    $showCompare = (bool) ($options['show_compare'] ?? TRUE);

    $props = OfferCardPropsBuilder::buildTeaser($node);
    $props['show_favorite'] = $showFavorite;
    $props['show_compare'] = $showCompare;

    $component = [
      '#type' => 'component',
      '#component' => 'ps_theme:offer-teaser-card',
      '#props' => $props,
      '#cache' => [
        'tags' => $node->getCacheTags(),
        'contexts' => ['languages:language_interface'],
      ],
    ];

    if ($showFavorite && \Drupal::hasService('ps_favorite.lazy_builder')) {
      $component['#slots']['favorite'] = [
        '#lazy_builder' => [
          'ps_favorite.lazy_builder:buildButton',
          [$node->getEntityTypeId(), (int) $node->id(), 'teaser'],
        ],
        '#create_placeholder' => TRUE,
      ];
    }

    if ($showCompare && \Drupal::hasService('ps_compare.lazy_builder')) {
      $component['#slots']['compare'] = [
        '#lazy_builder' => [
          'ps_compare.lazy_builder:buildButton',
          [$node->getEntityTypeId(), (int) $node->id(), 'teaser'],
        ],
        '#create_placeholder' => TRUE,
      ];
    }

    return $component;
  }

}
