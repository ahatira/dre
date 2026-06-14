<?php

declare(strict_types=1);

namespace Drupal\ps_news\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\SectionBlockFormTrait;

/**
 * Block form builder for the news grid block.
 */
final class NewsBlockFormBuilder {

  use SectionBlockFormTrait;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    return [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_intro' => $this->buildBodyBlockSectionHeaderNotice(),
      'footer_intro' => $this->buildBodyBlockSectionFooterNotice(),
      'display' => [
        '#type' => 'details',
        '#title' => $this->t('Display options'),
        '#open' => TRUE,
        'items_count' => [
          '#type' => 'select',
          '#title' => $this->t('Number of articles'),
          '#options' => [
            3 => '3',
            4 => '4',
            6 => '6',
          ],
          '#default_value' => (int) ($config['items_count'] ?? 3),
          '#description' => $this->t('The view grid layout adapts to this count on desktop.'),
        ],
      ],
    ];
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle'], $config['see_more_label']);
    $config['items_count'] = (int) $form_state->getValue(['display', 'items_count']);
  }

}
