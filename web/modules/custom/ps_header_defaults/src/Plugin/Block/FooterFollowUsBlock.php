<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

#[Block(
    id: 'ps_header_defaults_footer_follow_us',
    admin_label: new TranslatableMarkup('Footer follow us'),
)]
final class FooterFollowUsBlock extends BlockBase
{
    public function build(): array
    {
        return [
            'wrapper' => [
                '#type' => 'container',
                '#attributes' => [
                    'class' => ['ps-footer-contact__social'],
                ],
                'linkedin' => [
                    '#type' => 'link',
                    '#title' => $this->t('in'),
                    '#url' => Url::fromUri('https://www.linkedin.com'),
                    '#options' => [
                        'attributes' => [
                            'class' => ['ps-footer-contact__social-link'],
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ],
                    ],
                ],
                'twitter' => [
                    '#type' => 'link',
                    '#title' => $this->t('X'),
                    '#url' => Url::fromUri('https://x.com'),
                    '#options' => [
                        'attributes' => [
                            'class' => ['ps-footer-contact__social-link'],
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ],
                    ],
                ],
            ],
        ];
    }
}
