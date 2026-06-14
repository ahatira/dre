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
    $form = [];

    $form += $this->buildLanguageTabs($config, function (string $langcode, array $config): array {
      return [
        'header_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section header'),
          '#open' => TRUE,
        ] + $this->buildHeadingFields($langcode, $config),
      ];
    });

    $items = $this->sortItemsByWeight($config['items'] ?? []);
    if ($items === []) {
      $items = self::defaultItems();
    }

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Icon'),
        $this->t('Title (EN)'),
        $this->t('Title (FR)'),
        $this->t('Link label (EN)'),
        $this->t('Link label (FR)'),
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
      $form['items'][$delta]['title_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['title_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['title_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['title_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_label_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link label (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label_en'] ?? 'View listings',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_label_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link label (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label_fr'] ?? 'Voir les annonces',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'search_preset',
        $this->shortcutLinkTypeOptions(),
      ) + ['#title_display' => 'invisible'];
      $form['items'][$delta] += $this->buildPresetFields($parents, $item, $this->presetOptionsProvider);
      $form['items'][$delta]['url_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['url_en'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'custom_url'],
          ],
        ],
      ];
      $form['items'][$delta]['url_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['url_fr'] ?? '',
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
    }

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

      $titleEn = trim((string) ($row['title_en'] ?? ''));
      $titleFr = trim((string) ($row['title_fr'] ?? ''));
      if ($titleEn === '' && $titleFr === '') {
        continue;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'icon' => IconIdUtility::extractFromSubmission($row['icon'] ?? NULL, 'bnp_custom:offices'),
        'title_en' => $titleEn,
        'title_fr' => $titleFr,
        'link_label_en' => trim((string) ($row['link_label_en'] ?? '')),
        'link_label_fr' => trim((string) ($row['link_label_fr'] ?? '')),
        'link_type' => (string) ($row['link_type'] ?? 'search_preset'),
        'preset_operation' => (string) ($row['preset_operation'] ?? ''),
        'preset_asset' => (string) ($row['preset_asset'] ?? ''),
        'preset_locality' => trim((string) ($row['preset_locality'] ?? '')),
        'url_en' => trim((string) ($row['url_en'] ?? '')),
        'url_fr' => trim((string) ($row['url_fr'] ?? '')),
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @return list<array<string, mixed>>
   */
  public static function defaultItems(): array {
    $cards = [
      ['icon' => 'bnp_custom:offices', 'en' => 'Offices for rent', 'fr' => 'Bureaux à louer', 'asset' => 'BUR'],
      ['icon' => 'bnp_custom:logistic-warehouses', 'en' => 'Logistics', 'fr' => 'Logistique', 'asset' => 'LOG'],
      ['icon' => 'bnp_custom:shops', 'en' => 'Retail', 'fr' => 'Commerce', 'asset' => 'COM'],
      ['icon' => 'bnp_custom:coworking', 'en' => 'Coworking', 'fr' => 'Coworking', 'asset' => 'COW'],
    ];

    $items = [];
    foreach ($cards as $index => $card) {
      $items[] = [
        'weight' => $index,
        'icon' => $card['icon'],
        'title_en' => $card['en'],
        'title_fr' => $card['fr'],
        'link_label_en' => 'View listings',
        'link_label_fr' => 'Voir les annonces',
        'link_type' => 'search_preset',
        'preset_operation' => 'LOC',
        'preset_asset' => $card['asset'],
        'preset_locality' => '',
        'url_en' => '',
        'url_fr' => '',
      ];
    }

    return $items;
  }

}
