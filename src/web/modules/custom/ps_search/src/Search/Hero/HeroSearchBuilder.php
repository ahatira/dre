<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Builds the homepage search hero render array (SDC + search slot).
 */
final class HeroSearchBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly HomepageSearchPanelBuilder $homepageSearchPanelBuilder,
  ) {}

  /**
   * @param array<string, mixed> $heroProps
   *   title, background_image, background_alt, promo_*, labels, delegate_url.
   *
   * @return array<string, mixed>
   *   Full hero render array.
   */
  public function build(array $heroProps): array {
    $labels = is_array($heroProps['labels'] ?? NULL) ? $heroProps['labels'] : [];
    $panel = $this->homepageSearchPanelBuilder->buildPanelContent($labels);

    $delegateUrl = (string) ($heroProps['delegate_url'] ?? '/contact');
    $promoCtaUrl = (string) ($heroProps['promo_cta_url'] ?? '/find-property');

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero',
      '#props' => [
        'title' => (string) ($heroProps['title'] ?? ''),
        'background_image' => (string) ($heroProps['background_image'] ?? ''),
        'background_alt' => (string) ($heroProps['background_alt'] ?? ''),
        'promo_title' => (string) ($heroProps['promo_title'] ?? ''),
        'promo_offers_line' => (string) ($heroProps['promo_offers_line'] ?? ''),
        'promo_description' => (string) ($heroProps['promo_description'] ?? ''),
        'promo_cta_label' => (string) ($heroProps['promo_cta_label'] ?? ''),
        'promo_cta_url' => Url::fromUserInput($promoCtaUrl)->toString(),
        'promo_background_image' => (string) ($heroProps['promo_background_image'] ?? ''),
        'promo_background_alt' => (string) ($heroProps['promo_background_alt'] ?? ''),
        'delegate_url' => Url::fromUserInput($delegateUrl)->toString(),
      ],
      '#slots' => [
        'search' => $panel,
      ],
      '#attached' => $panel['#attached'] ?? [],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_merge(
          ['block_view'],
          $panel['#cache']['tags'] ?? ['config:ps_search.seo_url_mappings'],
        ),
        'max-age' => 0,
      ],
    ];
  }

}
