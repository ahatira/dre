<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Utility\HomepageContent;

/**
 * Homepage editorial promo (copy + image + CTA).
 */
#[Block(
  id: 'ps_homepage_editorial_promo_block',
  admin_label: new TranslatableMarkup('Editorial promo (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class EditorialPromoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-editorial', 'container']],
      'inner' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'align-items-center', 'g-4']],
        'copy' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['col-lg-6']],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['ps-homepage-editorial__title', 'h3', 'mb-3']],
            '#value' => HomepageContent::editorialTitle(),
          ],
          'body' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#attributes' => ['class' => ['ps-homepage-editorial__body', 'mb-4']],
            '#value' => HomepageContent::editorialBody(),
          ],
          'cta' => [
            '#type' => 'link',
            '#title' => HomepageContent::editorialCtaLabel(),
            '#url' => Url::fromUri('internal:' . HomepageContent::editorialCtaPath()),
            '#attributes' => [
              'class' => ['btn', 'btn-primary'],
            ],
          ],
        ],
        'media' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['col-lg-6']],
          'image' => [
            '#type' => 'html_tag',
            '#tag' => 'img',
            '#attributes' => [
              'src' => HomepageContent::editorialImageUrl(),
              'alt' => HomepageContent::editorialTitle(),
              'class' => ['img-fluid', 'rounded', 'w-100', 'ps-homepage-editorial__image'],
              'loading' => 'lazy',
            ],
          ],
        ],
      ],
    ];
  }

}
