<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

#[Block(
    id: 'ps_header_defaults_header_utility_link',
    admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Header utility link'),
)]
final class HeaderUtilityLinkBlock extends BlockBase
{
    public function build(): array
    {
        return [
        'link' => [
        '#type' => 'link',
        '#title' => $this->t('Find a property'),
        '#url' => Url::fromUri('internal:/properties'),
        '#options' => [
          'attributes' => [
            'class' => ['ps-header-utility-link'],
          ],
        ],
        ],
        ];
    }
}
