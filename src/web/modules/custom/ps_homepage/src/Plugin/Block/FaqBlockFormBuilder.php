<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;
use Drupal\node\NodeInterface;

/**
 * Block form builder for the homepage FAQ section.
 */
final class FaqBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 15;

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
      $fields = $this->buildHeadingFields($langcode, $config, FALSE);
      unset($fields['subtitle_' . $langcode]);
      return [
        'header_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section header'),
          '#open' => TRUE,
        ] + $fields,
        'footer_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section footer'),
          '#open' => FALSE,
        ] + $this->buildFooterCtaFields($langcode, $config),
      ];
    });

    $items = $this->sortItemsByWeight($config['faq_items'] ?? []);

    $form['faq_items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('FAQ item'),
        $this->t('Remove'),
      ],
      '#empty' => $this->t('No FAQ items selected.'),
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'faq-weight',
        ],
      ],
      '#description' => $this->t('Select between 1 and @max published FAQ items. Drag to reorder.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $defaultNid = (int) ($item['nid'] ?? 0);
      $defaultNode = $defaultNid > 0 ? $this->entityTypeManager->getStorage('node')->load($defaultNid) : NULL;

      $form['faq_items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['faq_items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['faq-weight']],
      ];
      $form['faq_items'][$delta]['nid'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('FAQ item'),
        '#title_display' => 'invisible',
        '#target_type' => 'node',
        '#selection_settings' => [
          'target_bundles' => ['faq_item'],
        ],
        '#default_value' => $defaultNode instanceof NodeInterface ? $defaultNode : NULL,
      ];
      $form['faq_items'][$delta]['remove'] = [
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
    foreach (['en', 'fr'] as $langcode) {
      $config['title_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'header_' . $langcode,
        'title_' . $langcode,
      ]));
      $config['see_more_label_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'footer_' . $langcode,
        'see_more_label_' . $langcode,
      ]));
      $config['see_more_url_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'footer_' . $langcode,
        'see_more_url_' . $langcode,
      ]));
    }

    $rows = $form_state->getValue('faq_items');
    if (!is_array($rows)) {
      $config['faq_items'] = [];
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

    $config['faq_items'] = $this->sortItemsByWeight($items);
  }

}
