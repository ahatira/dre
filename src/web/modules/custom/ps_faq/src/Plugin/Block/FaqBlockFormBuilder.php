<?php

declare(strict_types=1);

namespace Drupal\ps_faq\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Form\SectionBlockFormTrait;

/**
 * Block form builder for the FAQ accordion block.
 */
final class FaqBlockFormBuilder {

  use SectionBlockFormTrait;

  private const MAX_ITEMS = 15;

  private const MIN_VISIBLE_SLOTS = 3;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $items = $this->sortItemsByWeight($config['faq_items'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $items,
      static fn (array $item): bool => (int) ($item['nid'] ?? 0) > 0,
      self::MAX_ITEMS,
      self::MIN_VISIBLE_SLOTS,
    );

    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_intro' => $this->buildBodyBlockSectionHeaderNotice(),
      'footer_intro' => $this->buildBodyBlockSectionFooterNotice(),
    ];

    $form['faq_items'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-faq-block-form__items']],
    ];

    $form['faq_items']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $items,
      fn (array $item): string => $this->faqItemLabel($item),
      'ps-faq-block-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $defaultNid = (int) ($item['nid'] ?? 0);
      $defaultNode = $defaultNid > 0 ? $this->entityTypeManager->getStorage('node')->load($defaultNid) : NULL;
      $label = $defaultNode instanceof NodeInterface ? $defaultNode->label() : '';

      $form['faq_items'][$delta] = [
        '#type' => 'details',
        '#title' => $label !== ''
          ? $label
          : $this->t('FAQ item @number', ['@number' => $delta + 1]),
        '#open' => $label !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-faq-block-form__item']],
      ];

      $form['faq_items'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('FAQ item'),
        '#target_type' => 'node',
        '#selection_settings' => [
          'target_bundles' => ['faq_item'],
        ],
        '#default_value' => $defaultNode instanceof NodeInterface ? $defaultNode : NULL,
        '#description' => $this->t('Published FAQ item to display in the accordion.'),
      ];
      $form['faq_items'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this item'),
      );
    }

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_ITEMS);

    $form['#attributes']['class'][] = 'ps-faq-block-form';

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['see_more_label'], $config['see_more_url']);

    $rows = $form_state->getValue('faq_items');
    if (!is_array($rows)) {
      $config['faq_items'] = [];
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

    $config['faq_items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @param array<string, mixed> $item
   */
  private function faqItemLabel(array $item): string {
    $nid = (int) ($item['nid'] ?? 0);
    if ($nid <= 0) {
      return '';
    }
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    return $node instanceof NodeInterface ? $node->label() : '';
  }

}
