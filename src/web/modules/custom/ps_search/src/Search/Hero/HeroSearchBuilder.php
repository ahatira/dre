<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_search\Search\Header\HeaderSearchPanelBuilder;

/**
 * Builds the homepage search hero render array (SDC + search slot).
 */
final class HeroSearchBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly HeaderSearchPanelBuilder $headerSearchPanelBuilder,
  ) {}

  /**
   * @param array<string, mixed> $heroProps
   *   title, subtitle, background_image, background_alt.
   *
   * @return array<string, mixed>
   *   Full hero render array.
   */
  public function build(array $heroProps): array {
    $panel = $this->headerSearchPanelBuilder->buildPanelContent();
    unset($panel['#attached']['library']);

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero',
      '#props' => [
        'title' => (string) ($heroProps['title'] ?? ''),
        'subtitle' => (string) ($heroProps['subtitle'] ?? ''),
        'background_image' => (string) ($heroProps['background_image'] ?? ''),
        'background_alt' => (string) ($heroProps['background_alt'] ?? ''),
      ],
      '#slots' => [
        'search' => $panel,
      ],
      '#attached' => [
        'library' => ['ps_search/header.search'],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:ps_search.seo_url_mappings', 'config:ps_demo.homepage'],
      ],
    ];
  }

}
