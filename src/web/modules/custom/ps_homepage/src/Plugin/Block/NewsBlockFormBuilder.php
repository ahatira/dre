<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage news section.
 */
final class NewsBlockFormBuilder {

  use HomepageBlockFormTrait;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [];

    $form += $this->buildLanguageTabs($config, function (string $langcode, array $config): array {
      return [
        'header_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section header'),
          '#open' => TRUE,
        ] + $this->buildHeadingFields($langcode, $config),
        'footer_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section footer'),
          '#open' => FALSE,
        ] + [
          'see_more_label_' . $langcode => [
            '#type' => 'textfield',
            '#title' => $this->t('Footer CTA label'),
            '#default_value' => $config['see_more_label_' . $langcode] ?? '',
            '#maxlength' => 255,
          ],
        ],
      ];
    });

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Display options'),
      '#open' => TRUE,
    ];
    $form['display']['items_count'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of articles'),
      '#options' => [
        3 => '3',
        4 => '4',
        6 => '6',
      ],
      '#default_value' => (int) ($config['items_count'] ?? 3),
      '#description' => $this->t('The view grid layout adapts to this count on desktop.'),
    ];

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    foreach (['en', 'fr'] as $langcode) {
      $config['title_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'header_' . $langcode,
        'title_' . $langcode,
      ]));
      $config['subtitle_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'header_' . $langcode,
        'subtitle_' . $langcode,
      ]));
      $config['see_more_label_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'footer_' . $langcode,
        'see_more_label_' . $langcode,
      ]));
    }

    $config['items_count'] = (int) $form_state->getValue(['display', 'items_count']);
  }

}
