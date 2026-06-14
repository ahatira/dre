<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Form\SectionBlockFormTrait;

/**
 * Block form builder for the market studies grid.
 */
final class MarketStudiesBlockFormBuilder {

  use SectionBlockFormTrait;

  private const MAX_ITEMS = 4;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $studies = $this->sortItemsByWeight($config['studies'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $studies,
      static fn (array $item): bool => (int) ($item['nid'] ?? 0) > 0,
      self::MAX_ITEMS,
      self::MAX_ITEMS,
    );

    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_intro' => $this->buildBodyBlockSectionHeaderNotice(),
      'footer_intro' => $this->buildBodyBlockSectionFooterNotice(),
    ];

    $form['studies'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-market-studies-form__items']],
    ];

    $form['studies']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $studies,
      fn (array $item): string => $this->marketStudyLabel($item),
      'ps-market-studies-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $studies[$delta] ?? ['weight' => $delta];
      $defaultNid = (int) ($item['nid'] ?? 0);
      $defaultNode = $defaultNid > 0 ? $this->entityTypeManager->getStorage('node')->load($defaultNid) : NULL;
      $label = $defaultNode instanceof NodeInterface ? $defaultNode->label() : '';

      $form['studies'][$delta] = [
        '#type' => 'details',
        '#title' => $label !== ''
          ? $label
          : $this->t('Market study @number', ['@number' => $delta + 1]),
        '#open' => $label !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-market-studies-form__item']],
      ];

      $form['studies'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Market study'),
        '#target_type' => 'node',
        '#selection_settings' => [
          'target_bundles' => ['market_study'],
        ],
        '#default_value' => $defaultNode instanceof NodeInterface ? $defaultNode : NULL,
        '#description' => $this->t('Published market study node to feature in the grid.'),
      ];
      $form['studies'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this study'),
      );
    }

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_ITEMS);

    $form['#attributes']['class'][] = 'ps-market-studies-form';

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle'], $config['see_more_label'], $config['see_more_url']);

    $rows = $form_state->getValue('studies');
    if (!is_array($rows)) {
      $config['studies'] = [];
      return;
    }

    $weights = $this->extractRepeaterOrderWeights($rows);
    $items = [];
    foreach ($rows as $delta => $row) {
      if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $nid = (int) ($row['nid'] ?? 0);
      if ($nid <= 0) {
        continue;
      }

      $items[] = [
        'weight' => $weights[(int) $delta] ?? (int) $delta,
        'nid' => $nid,
      ];
    }

    $config['studies'] = $this->sortItemsByWeight($items);
  }

  /**
   * @param array<string, mixed> $item
   */
  private function marketStudyLabel(array $item): string {
    $nid = (int) ($item['nid'] ?? 0);
    if ($nid <= 0) {
      return '';
    }
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    return $node instanceof NodeInterface ? $node->label() : '';
  }

}
