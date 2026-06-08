<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Utility\HomepageContent;

/**
 * Homepage asset-type shortcuts (offices, logistics, retail, coworking).
 */
#[Block(
  id: 'ps_homepage_business_univers_block',
  admin_label: new TranslatableMarkup('Business univers (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class BusinessUniversBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $items = HomepageContent::universItems();
    /** @var \Drupal\ps_search\Service\SearchPathResolver $searchPathResolver */
    $searchPathResolver = \Drupal::service('ps_search.search_path_resolver');
    $searchPath = $searchPathResolver->getPublicPath();
    $tiles = [];
    foreach ($items as $index => $item) {
      parse_str($item['query'], $query);
      $tiles[$index] = [
        '#type' => 'link',
        '#title' => $item['label'],
        '#url' => Url::fromUserInput($searchPath, ['query' => $query]),
        '#attributes' => [
          'class' => ['ps-homepage-univers__tile', 'btn', 'btn-outline-primary'],
        ],
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-univers', 'container']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['ps-homepage-univers__title', 'h3', 'mb-4']],
        '#value' => HomepageContent::universTitle(),
      ],
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['d-flex', 'flex-wrap', 'gap-3']],
      ] + $tiles,
    ];
  }

}
