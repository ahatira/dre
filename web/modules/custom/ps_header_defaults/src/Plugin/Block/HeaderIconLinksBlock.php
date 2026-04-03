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
        $favorites_title = Markup::create(
            '<span class="ps-header__icon-inner" aria-hidden="true">'
            . '<svg class="ps-header__icon-svg" viewBox="0 0 24 24" focusable="false">'
            . '<path d="M18.57 5.41C17.73 4.56 16.62 4.14 15.5 4.14C14.38 4.14 '
            . '13.27 4.56 12.43 5.41L12 5.84L11.57 5.41C10.73 4.56 9.62 4.14 '
            . '8.5 4.14C7.38 4.14 6.27 4.56 5.43 5.41C3.72 7.12 3.72 9.89 5.43 '
            . '11.6L12 18.17L18.57 11.6C20.28 9.89 20.28 7.12 18.57 5.41Z" />'
            . '</svg>'
            . '</span>'
            . '<span class="visually-hidden">' . $this->t('Favorites') . '</span>'
            . '<span class="ps-header__icon-badge" aria-hidden="true"></span>'
        );

        $search_title = Markup::create(
            '<span class="ps-header__icon-inner" aria-hidden="true">'
            . '<svg class="ps-header__icon-svg" viewBox="0 0 24 24" focusable="false">'
            . '<path d="M11 2a9 9 0 1 1 0 18 9 9 0 0 1 0-18Zm0 2a7 7 0 1 0 '
            . '0 14 7 7 0 0 0 0-14Zm9.71 15.29 2 2a1 1 0 0 1-1.42 '
            . '1.42l-2-2a1 1 0 0 1 1.42-1.42Z" />'
            . '</svg>'
            . '</span>'
            . '<span class="visually-hidden">' . $this->t('Search') . '</span>'
        );

        return [
        '#type' => 'container',
        '#attributes' => [
        'class' => ['ps-header-icon-links'],
        ],
        'favorites' => [
        '#type' => 'link',
        '#title' => $favorites_title,
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
        '#title' => $search_title,
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
