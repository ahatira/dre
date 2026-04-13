<?php

declare(strict_types=1);

namespace Drupal\ps_block\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Footer copyright block.
 */
#[Block(
    id: 'ps_block_footer_copyright_block',
    admin_label: new TranslatableMarkup('PS Footer Copyright'),
    category: new TranslatableMarkup('Property Search'),
)]
final class FooterCopyrightBlock extends BlockBase
{
    public function defaultConfiguration(): array
    {
        return [
            'copyright' => '© BNP Paribas Real Estate',
        ] + parent::defaultConfiguration();
    }

    public function blockForm($form, FormStateInterface $form_state): array
    {
        $form = parent::blockForm($form, $form_state);

        $form['copyright'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Copyright text'),
            '#default_value' => (string) ($this->configuration['copyright'] ?? ''),
            '#required' => true,
        ];

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state): void
    {
        $this->configuration['copyright'] = trim((string) $form_state->getValue('copyright'));
    }

    public function build(): array
    {
        return [
            '#theme' => 'ps_block_footer_copyright_block',
            '#copyright' => (string) ($this->configuration['copyright'] ?? ''),
            '#cache' => [
                'max-age' => Cache::PERMANENT,
            ],
        ];
    }
}
