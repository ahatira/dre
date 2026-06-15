<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_search\Service\SearchPathResolver;

/**
 * Builds the offers carousel body render array (§4 Annonces).
 */
final class OfferCarouselBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly OfferTeaserBuilder $offerTeaserBuilder,
    private readonly OfferFeaturedOffersResolver $featuredOffersResolver,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array{
   *   body: array<string, mixed>,
   *   section: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  public function build(array $configuration): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $footerUrl = $this->searchPathResolver->getPublicPath($langcode);

    $nids = [];
    foreach ($configuration['offers'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $nid = (int) ($item['nid'] ?? 0);
      if ($nid > 0) {
        $nids[] = $nid;
      }
    }

    if ($nids === []) {
      $nids = $this->featuredOffersResolver->resolveDynamicNids(6);
    }

    if ($nids === []) {
      return [
        'body' => ['#markup' => ''],
        'section' => [],
        'cache' => [],
        'attached' => [],
      ];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $maxVisible = max(3, min(6, (int) ($configuration['max_visible'] ?? 4)));
    $autoplay = !empty($configuration['autoplay']);
    $cardOptions = [
      'show_favorite' => !empty($configuration['show_favorite']),
      'show_compare' => !empty($configuration['show_compare']),
    ];

    $items = [];
    $cacheTags = ['config:block.block'];
    foreach ($nids as $nid) {
      $node = $nodes[$nid] ?? NULL;
      if (!$node instanceof NodeInterface || !$node->isPublished()) {
        continue;
      }
      if ($node->hasTranslation($langcode)) {
        $node = $node->getTranslation($langcode);
      }

      $items[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-offers-carousel__item']],
        'card' => $this->offerTeaserBuilder->build($node, $cardOptions),
      ];
      $cacheTags = array_merge($cacheTags, $node->getCacheTags());
    }

    if ($items === []) {
      return [
        'body' => ['#markup' => ''],
        'section' => [],
        'cache' => [],
        'attached' => [],
      ];
    }

    $body = [
      'carousel' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-offers-carousel__carousel']],
        'viewport' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['ps-homepage-offers-carousel__viewport'],
            'data-autoplay' => $autoplay ? 'true' : 'false',
            'data-autoplay-interval' => '5000',
          ],
          'fade_prev' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-homepage-offers-carousel__fade', 'ps-homepage-offers-carousel__fade--prev']],
          ],
          'control_prev' => [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#value' => '',
            '#attributes' => [
              'type' => 'button',
              'class' => ['ps-homepage-offers-carousel__control', 'ps-homepage-offers-carousel__control--prev'],
              'data-carousel-prev' => 'true',
              'aria-label' => (string) $this->t('Previous offers'),
            ],
          ],
          'track' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-homepage-offers-carousel__track']],
          ] + $items,
          'fade_next' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-homepage-offers-carousel__fade', 'ps-homepage-offers-carousel__fade--next']],
          ],
          'control_next' => [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#value' => '',
            '#attributes' => [
              'type' => 'button',
              'class' => ['ps-homepage-offers-carousel__control', 'ps-homepage-offers-carousel__control--next'],
              'data-carousel-next' => 'true',
              'aria-label' => (string) $this->t('Next offers'),
            ],
          ],
        ],
        'dots' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['ps-homepage-offers-carousel__dots'],
            'role' => 'tablist',
            'aria-label' => (string) $this->t('Offers carousel pagination'),
          ],
        ],
      ],
    ];

    return [
      'body' => $body,
      'section' => [
        'modifier' => 'offers-carousel',
        'section_class' => 'ps-homepage-offers-carousel ps-homepage-offers-carousel--visible-' . $maxVisible,
        'footer_url' => $footerUrl,
      ],
      'attached' => [
        'library' => ['ps_offer/offers_carousel'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique(array_merge($cacheTags, ['search_api_list:offers']))),
      ],
    ];
  }

}
