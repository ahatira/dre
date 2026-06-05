<?php

declare(strict_types=1);

namespace Drupal\ps_block\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Stellar footer copyright line.
 */
#[Block(
  id: 'ps_block_footer_copyright',
  admin_label: new TranslatableMarkup('Footer copyright (Stellar)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class FooterCopyrightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'text' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);

    $form['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Copyright text'),
      '#default_value' => $this->configuration['text'] ?? '',
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['text'] = trim((string) $form_state->getValue('text'));
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#type' => 'component',
      '#component' => 'ps_theme:footer-copyright',
      '#props' => [
        'text' => $this->configuration['text'] ?? '',
      ],
    ];
  }

}
