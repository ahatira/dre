<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds offer-teaser-card SDC render arrays for offer carousels.
 */
final class OfferTeaserBuilder {

  public function __construct(
    private readonly ?object $favoriteLazyBuilder,
    private readonly ?object $compareLazyBuilder,
  ) {}

  public static function create(ContainerInterface $container): self {
    $favorite = $container->has('ps_favorite.lazy_builder')
      ? $container->get('ps_favorite.lazy_builder')
      : NULL;
    $compare = $container->has('ps_compare.lazy_builder')
      ? $container->get('ps_compare.lazy_builder')
      : NULL;

    return new self($favorite, $compare);
  }

  /**
   * @param array{show_favorite?: bool, show_compare?: bool, cta_label?: string} $options
   *
   * @return array<string, mixed>
   */
  public function build(NodeInterface $node, array $options = []): array {
    $showFavorite = (bool) ($options['show_favorite'] ?? TRUE);
    $showCompare = (bool) ($options['show_compare'] ?? TRUE);

    $props = \Drupal\ps_theme\Utility\OfferCardPropsBuilder::buildTeaser($node);
    $props['show_favorite'] = $showFavorite;
    $props['show_compare'] = $showCompare;
    if (!empty($options['cta_label'])) {
      $props['cta_label'] = (string) $options['cta_label'];
    }

    $component = [
      '#type' => 'component',
      '#component' => 'ps_theme:offer-teaser-card',
      '#props' => $props,
      '#cache' => [
        'tags' => $node->getCacheTags(),
        'contexts' => ['languages:language_interface'],
      ],
    ];

    if ($showFavorite && $this->favoriteLazyBuilder !== NULL) {
      $component['#slots']['favorite'] = [
        '#lazy_builder' => [
          'ps_favorite.lazy_builder:buildButton',
          [$node->getEntityTypeId(), (int) $node->id(), 'teaser'],
        ],
        '#create_placeholder' => TRUE,
      ];
    }

    if ($showCompare && $this->compareLazyBuilder !== NULL) {
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
