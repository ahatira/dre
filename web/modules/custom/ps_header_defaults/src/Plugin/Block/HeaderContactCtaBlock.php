<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

#[Block(
    id: 'ps_header_defaults_header_contact_cta',
    admin_label: new TranslatableMarkup('Header contact CTA'),
)]
final class HeaderContactCtaBlock extends BlockBase
{
    public function build(): array
    {
        return [
            'link' => [
                '#type' => 'link',
                '#title' => $this->t('Contact us'),
                '#url' => Url::fromUri('internal:/contact'),
                '#options' => [
                    'attributes' => [
                        'class' => ['btn', 'btn-secondary', 'ps-header-contact-cta'],
                    ],
                ],
            ],
        ];
    }
}
