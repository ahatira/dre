<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_search\Form\SearchPresetBlockFormTrait;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Block form builder for the search shortcuts grid.
 */
final class SearchShortcutsBlockFormBuilder {

  use SearchPresetBlockFormTrait;

  private const MAX_ITEMS = 8;

  private const MIN_VISIBLE_SLOTS = 4;

  public function __construct(
    private readonly SearchPresetOptionsProvider $presetOptionsProvider,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $items = $this->sortItemsByWeight($config['items'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $items,
      static fn (array $item): bool => trim((string) ($item['title'] ?? '')) !== '',
      self::MAX_ITEMS,
      self::MIN_VISIBLE_SLOTS,
    );

    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(['ps_homepage/homepage_block_form']),
      'shortcuts_intro' => $this->buildBodyBlockSectionHeaderNotice(),
    ];

    $form['items'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-search-shortcuts-form__items']],
    ];

    $form['items']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $items,
      static fn (array $item): string => trim((string) ($item['title'] ?? '')),
      'ps-search-shortcuts-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $title = trim((string) ($item['title'] ?? ''));
      $parents = ['items', (string) $delta, 'link'];

      $form['items'][$delta] = [
        '#type' => 'details',
        '#title' => $title !== ''
          ? $title
          : $this->t('Shortcut @number', ['@number' => $delta + 1]),
        '#open' => $title !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-search-shortcuts-form__item']],
      ];

      $form['items'][$delta]['content'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Shortcut content'),
      ];
      $form['items'][$delta]['content']['icon'] = $this->buildIconPickerElement(
        $this->t('Icon'),
        (string) ($item['icon'] ?? 'bnp_custom:offices'),
      );
      $form['items'][$delta]['content']['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $item['title'] ?? '',
        '#maxlength' => 255,
      ];

      $form['items'][$delta]['link'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Link'),
        '#description' => $this->t('Search preset builds a filtered search URL. Custom URL accepts an internal path or absolute URL.'),
      ];
      $form['items'][$delta]['link']['link_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link label'),
        '#default_value' => $item['link_label'] ?? 'View listings',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link']['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'search_preset',
        $this->shortcutLinkTypeOptions(),
      );
      $form['items'][$delta]['link'] += $this->buildPresetFields($parents, $item, $this->presetOptionsProvider);
      $form['items'][$delta]['link']['url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#default_value' => $item['url'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'custom_url'],
          ],
        ],
      ];

      $form['items'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this shortcut'),
      );
    }

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_ITEMS);

    $form['#attributes']['class'][] = 'ps-search-shortcuts-form';

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle']);

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $weights = $this->extractRepeaterOrderWeights($rows);
    $items = [];
    foreach ($rows as $delta => $row) {
      if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $content = is_array($row['content'] ?? NULL) ? $row['content'] : [];
      $link = is_array($row['link'] ?? NULL) ? $row['link'] : [];

      $title = trim((string) ($content['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $items[] = [
        'weight' => $weights[(int) $delta] ?? (int) $delta,
        'icon' => $this->extractShortcutIconValue($content),
        'title' => $title,
        'link_label' => trim((string) ($link['link_label'] ?? '')),
        'link_type' => (string) ($link['link_type'] ?? 'search_preset'),
        'preset_operation' => (string) ($link['preset_operation'] ?? ''),
        'preset_asset' => (string) ($link['preset_asset'] ?? ''),
        'preset_locality' => trim((string) ($link['preset_locality'] ?? '')),
        'url' => trim((string) ($link['url'] ?? '')),
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

}
