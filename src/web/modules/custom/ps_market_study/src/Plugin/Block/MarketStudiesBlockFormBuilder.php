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
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'section_header' => $this->buildSectionHeaderFields($config),
      'section_footer' => $this->buildSectionFooterFields($config),
    ];

    $studies = $this->sortItemsByWeight($config['studies'] ?? []);

    $form['studies'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Market study'),
        $this->t('Remove'),
      ],
      '#empty' => $this->t('No market studies selected.'),
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'studies-weight',
        ],
      ],
      '#description' => $this->t('Select between 1 and @max published market studies. Drag to reorder.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $studies[$delta] ?? ['weight' => $delta];
      $defaultNid = (int) ($item['nid'] ?? 0);
      $defaultNode = $defaultNid > 0 ? $this->entityTypeManager->getStorage('node')->load($defaultNid) : NULL;

      $form['studies'][$delta]['#attributes']['class'][] = 'draggable';
      $form['studies'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['studies-weight']],
      ];
      $form['studies'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Market study'),
        '#title_display' => 'invisible',
        '#target_type' => 'node',
        '#selection_settings' => [
          'target_bundles' => ['market_study'],
        ],
        '#default_value' => $defaultNode instanceof NodeInterface ? $defaultNode : NULL,
      ];
      $form['studies'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove'),
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
    $config['see_more_url'] = trim((string) $form_state->getValue(['section_footer', 'see_more_url']));

    $rows = $form_state->getValue('studies');
    if (!is_array($rows)) {
      $config['studies'] = [];
      return;
    }

    $items = [];
    foreach ($rows as $delta => $row) {
      if (!is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $nid = (int) ($row['nid'] ?? 0);
      if ($nid <= 0) {
        continue;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'nid' => $nid,
      ];
    }

    $config['studies'] = $this->sortItemsByWeight($items);
  }

}
