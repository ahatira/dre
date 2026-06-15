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

  private const MIN_VISIBLE_SLOTS = 4;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $offers = $this->sortItemsByWeight($config['offers'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $offers,
      static fn (array $item): bool => (int) ($item['nid'] ?? 0) > 0,
      self::MAX_OFFERS,
      self::MIN_VISIBLE_SLOTS,
    );

    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_intro' => $this->buildBodyBlockSectionHeaderNotice(),
      'footer_intro' => $this->buildBodyBlockSectionFooterNotice(),
    ];

    $form['options'] = [
      '#type' => 'details',
      '#title' => $this->t('Carousel options'),
      '#open' => TRUE,
      'max_visible' => [
        '#type' => 'select',
        '#title' => $this->t('Visible cards'),
        '#options' => [
          3 => '3',
          4 => '4',
          6 => '6',
        ],
        '#default_value' => (int) ($config['max_visible'] ?? 4),
        '#description' => $this->t('Maximum number of cards shown in the carousel at once.'),
      ],
      'show_favorite' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Show favorite action'),
        '#default_value' => $config['show_favorite'] ?? TRUE,
      ],
      'show_compare' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Show compare action'),
        '#default_value' => $config['show_compare'] ?? TRUE,
      ],
      'autoplay' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Autoplay'),
        '#default_value' => $config['autoplay'] ?? FALSE,
      ],
    ];

    $form['offers'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-offers-carousel-form__items']],
    ];

    $form['offers']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $offers,
      fn (array $item): string => $this->offerItemLabel($item),
      'ps-offers-carousel-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $offers[$delta] ?? ['weight' => $delta];
      $nid = (int) ($item['nid'] ?? 0);
      $node = $nid > 0 ? $this->entityTypeManager->getStorage('node')->load($nid) : NULL;
      $label = $node instanceof NodeInterface ? $node->label() : '';

      $form['offers'][$delta] = [
        '#type' => 'details',
        '#title' => $label !== ''
          ? $label
          : $this->t('Offer @number', ['@number' => $delta + 1]),
        '#open' => $label !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-offers-carousel-form__item']],
      ];

      $form['offers'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Offer'),
        '#target_type' => 'node',
        '#selection_settings' => ['target_bundles' => ['offer']],
        '#default_value' => $node instanceof NodeInterface ? $node : NULL,
        '#description' => $this->t('Published offer to feature. When no offers are selected, the carousel loads the latest published offers from the search index (Solr), falling back to the database if needed.'),
      ];
      $form['offers'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this offer'),
      );
    }

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_OFFERS);

    $form['#attributes']['class'][] = 'ps-offers-carousel-form';

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle'], $config['see_more_label']);

    $config['max_visible'] = (int) $form_state->getValue(['options', 'max_visible']);
    $config['show_favorite'] = (bool) $form_state->getValue(['options', 'show_favorite']);
    $config['show_compare'] = (bool) $form_state->getValue(['options', 'show_compare']);
    $config['autoplay'] = (bool) $form_state->getValue(['options', 'autoplay']);

    $rows = $form_state->getValue('offers');
    $offers = [];
    $weights = is_array($rows) ? $this->extractRepeaterOrderWeights($rows) : [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
          continue;
        }
        $nid = (int) ($row['nid'] ?? 0);
        if ($nid <= 0) {
          continue;
        }
        $offers[] = [
          'weight' => $weights[(int) $delta] ?? (int) $delta,
          'nid' => $nid,
        ];
      }
    }
    $config['offers'] = $this->sortItemsByWeight($offers);
  }

  /**
   * @param array<string, mixed> $item
   */
  private function offerItemLabel(array $item): string {
    $nid = (int) ($item['nid'] ?? 0);
    if ($nid <= 0) {
      return '';
    }
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    return $node instanceof NodeInterface ? $node->label() : '';
  }

}
