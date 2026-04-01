<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
    id: 'ps_header_defaults_footer_copyright',
    admin_label: new TranslatableMarkup('Footer copyright'),
)]
final class FooterCopyrightBlock extends BlockBase
{
    public function build(): array
    {
        return [
            '#type' => 'container',
            '#attributes' => [
                'class' => ['ps-footer-copyright'],
            ],
            'text' => [
                '#markup' => $this->t('© BNP Paribas Real Estate'),
            ],
        ];
    }
}
