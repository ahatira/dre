<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Block form builder for the homepage search shortcuts grid.
 */
final class SearchShortcutsBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 8;

  public function __construct(
    private readonly SearchPresetOptionsProvider $presetOptionsProvider,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(),
      'section_header' => $this->buildSectionHeaderFields($config),
    ];

    $items = $this->sortItemsByWeight($config['items'] ?? []);

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Icon'),
        $this->t('Title'),
        $this->t('Link label'),
        $this->t('Link'),
        $this->t('Actions'),
      ],
      '#empty' => $this->t('No shortcuts yet.'),
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'shortcuts-weight',
        ],
      ],
      '#description' => $this->t('Up to @max shortcuts. Leave unused rows empty or check Remove.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $parents = ['items', (string) $delta];

      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['shortcuts-weight']],
      ];
      $form['items'][$delta]['icon'] = $this->buildIconPickerElement(
        $this->t('Icon'),
        (string) ($item['icon'] ?? 'bnp_custom:offices'),
      );
      $form['items'][$delta]['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#title_display' => 'invisible',
        '#default_value' => $item['title'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link label'),
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label'] ?? 'View listings',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'search_preset',
        $this->shortcutLinkTypeOptions(),
      ) + ['#title_display' => 'invisible'];
      $form['items'][$delta] += $this->buildPresetFields($parents, $item, $this->presetOptionsProvider);
      $form['items'][$delta]['url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#title_display' => 'invisible',
        '#default_value' => $item['url'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'custom_url'],
          ],
        ],
      ];
      $form['items'][$delta]['remove'] = [
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

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $items = [];
    foreach ($rows as $delta => $row) {
      if (!is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'icon' => IconIdUtility::extractFromSubmission($row['icon'] ?? NULL, 'bnp_custom:offices'),
        'title' => $title,
        'link_label' => trim((string) ($row['link_label'] ?? '')),
        'link_type' => (string) ($row['link_type'] ?? 'search_preset'),
        'preset_operation' => (string) ($row['preset_operation'] ?? ''),
        'preset_asset' => (string) ($row['preset_asset'] ?? ''),
        'preset_locality' => trim((string) ($row['preset_locality'] ?? '')),
        'url' => trim((string) ($row['url'] ?? '')),
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

}
