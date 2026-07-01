<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Builds the similar offers carousel on offer detail pages.
 */
final class OfferSimilarCarouselBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly SimilarOffersResolver $similarOffersResolver,
    private readonly OfferCarouselBuilder $carouselBuilder,
    private readonly OfferSearchUrlBuilder $searchUrlBuilder,
  ) {}

  /**
   * Builds the similar offers section render array.
   */
  public function build(NodeInterface $offer): array {
    if ($offer->bundle() !== 'offer') {
      return [];
    }

    $nids = $this->similarOffersResolver->resolveNids();
    if ($nids === []) {
      return [];
    }

    $carousel = $this->carouselBuilder->buildFromNids($nids, [
      'max_visible' => 4,
      'show_favorite' => TRUE,
      'show_compare' => FALSE,
      'autoplay' => FALSE,
      'cta_label' => (string) $this->t('View the property'),
    ]);
    if ($carousel === []) {
      return [];
    }

    $seeMoreUrl = $this->searchUrlBuilder->buildResultsPageUrl($offer)->toString();

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'ps-offer-similar-carousel',
          'ps-homepage-offers-carousel',
          'ps-homepage-offers-carousel--visible-4',
        ],
      ],
      'inner' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-section__inner']],
        'header' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-section__header']],
          'title' => [
            '#type' => 'component',
            '#component' => 'ps_theme:homepage-section-header',
            '#props' => [
              'title' => (string) $this->t('You might also like these other properties'),
              'subtitle' => '',
              'align' => 'center',
              'accent' => 'none',
            ],
          ],
        ],
        'body' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-section__body']],
        ] + $carousel['body'],
        'footer' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-section__footer']],
          'cta' => [
            '#type' => 'component',
            '#component' => 'ps_theme:homepage-section-footer',
            '#props' => [
              'cta_label' => (string) $this->t('See more'),
              'cta_url' => $seeMoreUrl,
              'cta_style' => 'outline',
            ],
          ],
        ],
      ],
      '#attached' => $carousel['attached'],
      '#cache' => [
        'tags' => array_values(array_unique(array_merge(
          $offer->getCacheTags(),
          $carousel['cache']['tags'] ?? [],
        ))),
        'contexts' => array_values(array_unique(array_merge(
          ['route', 'languages:language_interface'],
          $carousel['cache']['contexts'] ?? [],
        ))),
      ],
    ];
  }

}
