<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

#[Block(
    id: 'ps_header_defaults_footer_contact',
    admin_label: new TranslatableMarkup('Footer contact'),
)]
final class FooterContactBlock extends BlockBase
{
    public function build(): array
    {
        $phoneOne = [
            '#type' => 'link',
            '#title' => $this->t('+32 2 646 49 49'),
            '#url' => Url::fromUri('tel:+3226464949'),
            '#options' => [
                'attributes' => [
                    'class' => ['ps-footer-contact__phone'],
                ],
            ],
        ];

        $phoneTwo = [
            '#type' => 'link',
            '#title' => $this->t('+32 2 646 49 49'),
            '#url' => Url::fromUri('tel:+3226464949'),
            '#options' => [
                'attributes' => [
                    'class' => ['ps-footer-contact__phone'],
                ],
            ],
        ];

        $email = [
            '#type' => 'link',
            '#title' => $this->t('bnppre.belgium@bnpparibas.com'),
            '#url' => Url::fromUri('mailto:bnppre.belgium@bnpparibas.com'),
            '#options' => [
                'attributes' => [
                    'class' => ['ps-footer-contact__email'],
                ],
            ],
        ];

        return [
            'wrapper' => [
                '#type' => 'container',
                '#attributes' => [
                    'class' => ['ps-footer-contact'],
                ],
                'city_one' => [
                    '#type' => 'container',
                    '#attributes' => [
                        'class' => ['ps-footer-contact__line'],
                    ],
                    'label' => [
                        '#type' => 'html_tag',
                        '#tag' => 'span',
                        '#value' => $this->t('City 1 :'),
                        '#attributes' => [
                            'class' => ['ps-footer-contact__line-label'],
                        ],
                    ],
                    'value' => $phoneOne,
                ],
                'city_two' => [
                    '#type' => 'container',
                    '#attributes' => [
                        'class' => ['ps-footer-contact__line'],
                    ],
                    'label' => [
                        '#type' => 'html_tag',
                        '#tag' => 'span',
                        '#value' => $this->t('City 2 :'),
                        '#attributes' => [
                            'class' => ['ps-footer-contact__line-label'],
                        ],
                    ],
                    'value' => $phoneTwo,
                ],
                'email' => [
                    '#type' => 'container',
                    '#attributes' => [
                        'class' => ['ps-footer-contact__line', 'ps-footer-contact__line--email'],
                    ],
                    'value' => $email,
                ],
            ],
        ];
    }
}
