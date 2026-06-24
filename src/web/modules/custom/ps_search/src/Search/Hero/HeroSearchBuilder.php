<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Builds the homepage search hero render array (SDC shell + slots).
 */
final class HeroSearchBuilder {

  use StringTranslationTrait;

  private const DEFAULT_BACKGROUND = 'assets/images/hero/hero-profile.png';

  public function __construct(
    private readonly HomepageSearchPanelBuilder $homepageSearchPanelBuilder,
    private readonly HeroDelegateBarBuilder $heroDelegateBarBuilder,
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  /**
   * Builds the homepage search hero render array.
   *
   * @param array<string, mixed> $heroProps
   *   Hero properties: title, background_image, background_alt, promo_*, labels.
   *
   * @return array<string, mixed>
   *   Full hero render array.
   */
  public function build(array $heroProps): array {
    $labels = is_array($heroProps['labels'] ?? NULL) ? $heroProps['labels'] : [];
    $panelOptions = [
      'hero_background_default' => (string) ($heroProps['hero_background_default'] ?? ''),
      'hero_background_by_asset' => is_array($heroProps['hero_background_by_asset'] ?? NULL)
        ? $heroProps['hero_background_by_asset']
        : [],
    ];
    $panel = $this->homepageSearchPanelBuilder->buildPanelContent($labels, $panelOptions);
    $delegate = $this->heroDelegateBarBuilder->build($labels);

    $promoCtaUrl = (string) ($heroProps['promo_cta_url'] ?? '/find-property');
    $backgroundImage = (string) ($heroProps['background_image'] ?? '');
    if ($backgroundImage === '') {
      $backgroundImage = $this->defaultHeroBackgroundUrl();
    }

    $promoBackground = (string) ($heroProps['promo_background_image'] ?? '');
    if ($promoBackground === '') {
      $promoBackground = $backgroundImage;
    }

    $promo = [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero-promo',
      '#props' => [
        'title' => (string) ($heroProps['promo_title'] ?? ''),
        'offers_line' => (string) ($heroProps['promo_offers_line'] ?? ''),
        'description' => (string) ($heroProps['promo_description'] ?? ''),
        'cta_label' => (string) ($heroProps['promo_cta_label'] ?? ''),
        'cta_url' => Url::fromUserInput($promoCtaUrl)->toString(),
        'background_image' => $promoBackground,
        'background_alt' => (string) ($heroProps['promo_background_alt'] ?? ''),
      ],
    ];

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero',
      '#props' => [
        'title' => (string) ($heroProps['title'] ?? ''),
        'background_image' => $backgroundImage,
        'background_alt' => (string) ($heroProps['background_alt'] ?? ''),
      ],
      '#slots' => [
        'search' => $panel,
        'delegate' => $delegate,
        'promo' => $promo,
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

  /**
   * Default blurred hero background from ps_theme assets.
   */
  public function defaultHeroBackgroundUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/' . self::DEFAULT_BACKGROUND)->toString();
  }

}
