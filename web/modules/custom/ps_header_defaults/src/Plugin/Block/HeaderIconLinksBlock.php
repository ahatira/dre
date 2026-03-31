<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

#[Block(
    id: 'ps_header_defaults_header_icon_links',
    admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Header icon links'),
)]
final class HeaderIconLinksBlock extends BlockBase
{
    public function build(): array
    {
        return [
        '#type' => 'container',
        '#attributes' => [
        'class' => ['ps-header-icon-links'],
        ],
        'favorites' => [
        '#type' => 'link',
        '#title' => Markup::create('<span class="visually-hidden">' . $this->t('Favorites') . '</span>'),
        '#url' => Url::fromUri('internal:/favorites'),
        '#options' => [
          'attributes' => [
            'class' => ['ps-header__icon-link', 'ps-header__icon-link--favorites'],
            'aria-label' => $this->t('Favorites'),
          ],
        ],
        ],
        'search' => [
        '#type' => 'link',
        '#title' => Markup::create('<span class="visually-hidden">' . $this->t('Search') . '</span>'),
        '#url' => Url::fromUri('internal:/search/node'),
        '#options' => [
          'attributes' => [
            'class' => ['ps-header__icon-link', 'ps-header__icon-link--search'],
            'aria-label' => $this->t('Search'),
          ],
        ],
        ],
        ];
    }
}
