<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;
use Drupal\node\NodeInterface;

/**
 * Block form builder for the homepage offers carousel.
 */
final class OffersCarouselBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_OFFERS = 12;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

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

    $form['options'] = [
      '#type' => 'details',
      '#title' => $this->t('Carousel options'),
      '#open' => TRUE,
    ];
    $form['options']['max_visible'] = [
      '#type' => 'select',
      '#title' => $this->t('Visible cards'),
      '#options' => [
        3 => '3',
        4 => '4',
        6 => '6',
      ],
      '#default_value' => (int) ($config['max_visible'] ?? 4),
    ];
    $form['options']['show_favorite'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show favorite action'),
      '#default_value' => $config['show_favorite'] ?? TRUE,
    ];
    $form['options']['show_compare'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show compare action'),
      '#default_value' => $config['show_compare'] ?? TRUE,
    ];
    $form['options']['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $config['autoplay'] ?? FALSE,
    ];

    $offers = $this->sortItemsByWeight($config['offers'] ?? []);

    $form['offers'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Offer'),
        $this->t('Remove'),
      ],
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'offers-weight',
        ],
      ],
      '#description' => $this->t('Select up to @max published offers. Drag to reorder.', ['@max' => self::MAX_OFFERS]),
    ];

    for ($delta = 0; $delta < self::MAX_OFFERS; $delta++) {
      $item = $offers[$delta] ?? ['weight' => $delta];
      $nid = (int) ($item['nid'] ?? 0);
      $node = $nid > 0 ? $this->entityTypeManager->getStorage('node')->load($nid) : NULL;

      $form['offers'][$delta]['#attributes']['class'][] = 'draggable';
      $form['offers'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['offers-weight']],
      ];
      $form['offers'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title_display' => 'invisible',
        '#target_type' => 'node',
        '#selection_settings' => ['target_bundles' => ['offer']],
        '#default_value' => $node instanceof NodeInterface ? $node : NULL,
      ];
      $form['offers'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title_display' => 'invisible',
        '#return_value' => 1,
      ];
    }

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

    $config['max_visible'] = (int) $form_state->getValue(['options', 'max_visible']);
    $config['show_favorite'] = (bool) $form_state->getValue(['options', 'show_favorite']);
    $config['show_compare'] = (bool) $form_state->getValue(['options', 'show_compare']);
    $config['autoplay'] = (bool) $form_state->getValue(['options', 'autoplay']);

    $rows = $form_state->getValue('offers');
    $offers = [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if (!is_array($row) || !empty($row['remove'])) {
          continue;
        }
        $nid = (int) ($row['nid'] ?? 0);
        if ($nid <= 0) {
          continue;
        }
        $offers[] = [
          'weight' => (int) ($row['weight'] ?? $delta),
          'nid' => $nid,
        ];
      }
    }
    $config['offers'] = $this->sortItemsByWeight($offers);
  }

}
