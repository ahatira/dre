<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldType\TextItemBase;

/**
 * Renders offer description with a read-more toggle.
 *
 * @FieldFormatter(
 *   id = "ps_offer_description",
 *   label = @Translation("Offer description (read more)"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
final class OfferDescriptionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'visible_lines' => 6,
      'read_more_label' => 'Read more',
      'read_less_label' => 'Read less',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['visible_lines'] = [
      '#type' => 'number',
      '#title' => $this->t('Visible lines when collapsed'),
      '#description' => $this->t('Number of text lines shown before the read-more control.'),
      '#default_value' => $this->getSetting('visible_lines'),
      '#min' => 1,
      '#max' => 20,
      '#required' => TRUE,
    ];
    $elements['read_more_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Read more label'),
      '#default_value' => $this->getSetting('read_more_label'),
      '#required' => TRUE,
    ];
    $elements['read_less_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Read less label'),
      '#default_value' => $this->getSetting('read_less_label'),
      '#required' => TRUE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Visible lines: @lines', [
      '@lines' => $this->getSetting('visible_lines'),
    ]);
    $summary[] = $this->t('Read more: @label', [
      '@label' => $this->getSetting('read_more_label'),
    ]);
    $summary[] = $this->t('Read less: @label', [
      '@label' => $this->getSetting('read_less_label'),
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $visible_lines = max(1, (int) $this->getSetting('visible_lines'));
    $read_more_setting = (string) $this->getSetting('read_more_label');
    $read_less_setting = (string) $this->getSetting('read_less_label');
    $read_more = $read_more_setting === 'Read more'
      ? (string) $this->t('Read more')
      : $read_more_setting;
    $read_less = $read_less_setting === 'Read less'
      ? (string) $this->t('Read less')
      : $read_less_setting;

    foreach ($items as $delta => $item) {
      if (!$item instanceof TextItemBase) {
        continue;
      }
      $text = (string) ($item->value ?? '');
      if ($text === '') {
        continue;
      }

      $content_id = 'ps-offer-description-content-' . $items->getEntity()->id() . '-' . $delta;

      $elements[$delta] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-offer-description', 'ps-offer-description--collapsible'],
          'style' => '--ps-offer-description-lines: ' . $visible_lines . ';',
        ],
        'intro' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-offer-description__intro']],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#value' => $this->t('Description'),
            '#attributes' => ['class' => ['ps-offer-description__title']],
          ],
          'body' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-offer-description__body']],
            'clip' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['ps-offer-description__clip']],
              'content' => [
                '#type' => 'container',
                '#attributes' => [
                  'id' => $content_id,
                  'class' => ['ps-offer-description__content'],
                ],
                'text' => [
                  '#type' => 'processed_text',
                  '#text' => $text,
                  '#format' => $item->format ?? 'basic_html',
                  '#langcode' => $item->getLangcode(),
                ],
              ],
            ],
            'fade' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#attributes' => [
                'class' => ['ps-offer-description__fade'],
                'aria-hidden' => 'true',
              ],
            ],
          ],
        ],
        'toggle' => [
          '#type' => 'html_tag',
          '#tag' => 'button',
          '#attributes' => [
            'type' => 'button',
            'class' => ['ps-offer-description__toggle'],
            'data-ps-read-more' => '',
            'data-read-more-label' => $read_more,
            'data-read-less-label' => $read_less,
            'aria-expanded' => 'false',
            'aria-controls' => $content_id,
          ],
          'label' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => $read_more,
            '#attributes' => ['class' => ['ps-offer-description__toggle-label']],
          ],
          'icon' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => [
              'class' => ['ps-offer-description__toggle-icon'],
              'aria-hidden' => 'true',
            ],
          ],
        ],
        '#attached' => [
          'library' => ['ps_theme/offer-detail'],
        ],
      ];
    }

    return $elements;
  }

}
