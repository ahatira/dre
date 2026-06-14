<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Form\SectionBlockFormTrait;

/**
 * Block form builder for the offers carousel.
 */
final class OffersCarouselBlockFormBuilder {

  use SectionBlockFormTrait;

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
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_header' => $this->buildSectionHeaderFields($config),
      'section_footer' => $this->buildSectionFooterFields($config, FALSE),
    ];

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
    $config['title'] = trim((string) $form_state->getValue(['section_header', 'title']));
    $config['subtitle'] = trim((string) $form_state->getValue(['section_header', 'subtitle']));
    $config['see_more_label'] = trim((string) $form_state->getValue(['section_footer', 'see_more_label']));

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
