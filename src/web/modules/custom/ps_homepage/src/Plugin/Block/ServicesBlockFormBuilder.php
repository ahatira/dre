<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Block form builder for the homepage services grid.
 */
final class ServicesBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 6;

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
      $items = ServicesBlockFormBuilder::defaultItems();
    }

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Icon'),
        $this->t('Title (EN)'),
        $this->t('Title (FR)'),
        $this->t('Body (EN)'),
        $this->t('Body (FR)'),
        $this->t('Button (EN)'),
        $this->t('Button (FR)'),
        $this->t('Link'),
        $this->t('Style'),
        $this->t('Actions'),
      ],
      '#empty' => $this->t('No service cards yet.'),
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'services-weight',
        ],
      ],
    ];

    $itemCount = count($items);
    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $parents = ['items', (string) $delta];

      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['services-weight']],
      ];
      $form['items'][$delta]['icon'] = $this->buildIconPickerElement(
        $this->t('Icon'),
        (string) ($item['icon'] ?? 'bnp_custom:offices'),
      );
      $form['items'][$delta]['card_title_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['card_title_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['card_title_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['card_title_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['body_en'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Body (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['body_en'] ?? '',
        '#rows' => 2,
      ];
      $form['items'][$delta]['body_fr'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Body (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['body_fr'] ?? '',
        '#rows' => 2,
      ];
      $form['items'][$delta]['button_label_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_label_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['button_label_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_label_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'search_preset',
        $this->serviceLinkTypeOptions(),
      ) + ['#title_display' => 'invisible'];
      $form['items'][$delta]['button_style'] = [
        '#type' => 'select',
        '#title' => $this->t('Style'),
        '#title_display' => 'invisible',
        '#options' => [
          'outline' => (string) $this->t('Outline'),
          'primary' => (string) $this->t('Primary'),
        ],
        '#default_value' => $item['button_style'] ?? 'outline',
      ];
      $form['items'][$delta] += $this->buildPresetFields($parents, $item, $this->presetOptionsProvider);
      $form['items'][$delta]['button_url_en'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL (EN)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_url_en'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'url'],
          ],
        ],
      ];
      $form['items'][$delta]['button_url_fr'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL (FR)'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_url_fr'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'url'],
          ],
        ],
      ];
      $form['items'][$delta]['modal_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Modal ID'),
        '#title_display' => 'invisible',
        '#default_value' => $item['modal_id'] ?? '',
        '#maxlength' => 128,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'modal'],
          ],
        ],
      ];
      $form['items'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove'),
        '#title_display' => 'invisible',
        '#return_value' => 1,
        '#default_value' => empty($item['card_title_en']) && empty($item['card_title_fr']) ? 0 : 0,
      ];
    }

    $form['items']['#description'] = $this->t('Up to @max cards. Leave unused rows empty or check Remove. Drag to reorder.', ['@max' => self::MAX_ITEMS]);

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

      $titleEn = trim((string) ($row['card_title_en'] ?? ''));
      $titleFr = trim((string) ($row['card_title_fr'] ?? ''));
      if ($titleEn === '' && $titleFr === '') {
        continue;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'icon' => IconIdUtility::extractFromSubmission($row['icon'] ?? NULL, 'bnp_custom:offices'),
        'card_title_en' => $titleEn,
        'card_title_fr' => $titleFr,
        'body_en' => trim((string) ($row['body_en'] ?? '')),
        'body_fr' => trim((string) ($row['body_fr'] ?? '')),
        'button_label_en' => trim((string) ($row['button_label_en'] ?? '')),
        'button_label_fr' => trim((string) ($row['button_label_fr'] ?? '')),
        'link_type' => (string) ($row['link_type'] ?? 'url'),
        'button_style' => (string) ($row['button_style'] ?? 'outline'),
        'preset_operation' => (string) ($row['preset_operation'] ?? ''),
        'preset_asset' => (string) ($row['preset_asset'] ?? ''),
        'preset_locality' => trim((string) ($row['preset_locality'] ?? '')),
        'button_url_en' => trim((string) ($row['button_url_en'] ?? '')),
        'button_url_fr' => trim((string) ($row['button_url_fr'] ?? '')),
        'modal_id' => trim((string) ($row['modal_id'] ?? '')),
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @return list<array<string, mixed>>
   */
  public static function defaultItems(): array {
    $cards = [
      ['icon' => 'bnp_custom:offices', 'en' => 'Offices', 'fr' => 'Bureaux', 'asset' => 'BUR'],
      ['icon' => 'bnp_custom:logistic-warehouses', 'en' => 'Logistics', 'fr' => 'Logistique', 'asset' => 'LOG'],
      ['icon' => 'bnp_custom:shops', 'en' => 'Retail', 'fr' => 'Commerce', 'asset' => 'COM'],
      ['icon' => 'bnp_custom:coworking', 'en' => 'Coworking', 'fr' => 'Coworking', 'asset' => 'COW'],
    ];

    $items = [];
    foreach ($cards as $index => $card) {
      $items[] = [
        'weight' => $index,
        'icon' => $card['icon'],
        'card_title_en' => $card['en'],
        'card_title_fr' => $card['fr'],
        'body_en' => '',
        'body_fr' => '',
        'button_label_en' => 'Discover',
        'button_label_fr' => 'Découvrir',
        'link_type' => 'search_preset',
        'button_style' => 'outline',
        'preset_operation' => 'LOC',
        'preset_asset' => $card['asset'],
        'preset_locality' => '',
        'button_url_en' => '',
        'button_url_fr' => '',
        'modal_id' => '',
      ];
    }

    return $items;
  }

}
