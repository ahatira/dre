<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_theme\Utility\TransactionToggleBuilder;

/**
 * Stellar homepage search hero (SDC + transaction toggle).
 */
#[Block(
  id: 'ps_homepage_search_hero_block',
  admin_label: new TranslatableMarkup('Search hero (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class SearchHeroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $searchSlot = [
      '#type' => 'container',
      '#attributes' => ['class' => ['d-flex', 'flex-column', 'gap-3']],
      'toggle' => [
        '#type' => 'component',
        '#component' => 'ps_theme:transaction-toggle',
        '#props' => TransactionToggleBuilder::build(),
      ],
      'location' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => ['bg-white', 'p-3', 'rounded', 'shadow-sm', 'text-dark', 'text-start'],
        ],
        '#value' => (string) $this->t('City, department, region… (location autocomplete coming soon)'),
      ],
    ];

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero',
      '#props' => [
        'title' => HomepageContent::heroTitle(),
        'subtitle' => HomepageContent::heroSubtitle(),
        'background_image' => HomepageContent::heroBackgroundUrl(),
        'background_alt' => '',
      ],
      '#slots' => [
        'search' => $searchSlot,
      ],
    ];
  }

}
