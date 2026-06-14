<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage market studies section.
 */
final class MarketStudiesBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 4;

  public function __construct(
    private readonly DateFormatterInterface $dateFormatter,
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
        'footer_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section footer'),
          '#open' => FALSE,
        ] + $this->buildFooterCtaFields($langcode, $config),
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
        $this->t('Image'),
        $this->t('Alt'),
        $this->t('Category EN'),
        $this->t('Category FR'),
        $this->t('Title EN'),
        $this->t('Title FR'),
        $this->t('Date'),
        $this->t('URL EN'),
        $this->t('URL FR'),
        $this->t('Remove'),
      ],
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'studies-weight',
        ],
      ],
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['studies-weight']],
      ];
      $form['items'][$delta]['image'] = $this->buildManagedFileElement(
        $this->t('Image'),
        $item['image'] ?? NULL,
        'public://homepage/market-studies/',
        FALSE,
      );
      $form['items'][$delta]['image_alt'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['image_alt'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['category_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['category_en'] ?? '',
        '#maxlength' => 128,
      ];
      $form['items'][$delta]['category_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['category_fr'] ?? '',
        '#maxlength' => 128,
      ];
      $form['items'][$delta]['title_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['title_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['title_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['title_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['date'] = [
        '#type' => 'date',
        '#title_display' => 'invisible',
        '#default_value' => $item['date'] ?? '',
      ];
      $form['items'][$delta]['url_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['url_en'] ?? '',
        '#maxlength' => 512,
      ];
      $form['items'][$delta]['url_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['url_fr'] ?? '',
        '#maxlength' => 512,
      ];
      $form['items'][$delta]['remove'] = [
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

    $rows = $form_state->getValue('items');
    $items = [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if (!is_array($row) || !empty($row['remove'])) {
          continue;
        }

        $titleEn = trim((string) ($row['title_en'] ?? ''));
        $titleFr = trim((string) ($row['title_fr'] ?? ''));
        $imageFid = $this->persistManagedFile($row['image'] ?? NULL);
        if ($imageFid === NULL || ($titleEn === '' && $titleFr === '')) {
          continue;
        }

        $items[] = [
          'weight' => (int) ($row['weight'] ?? $delta),
          'image' => $imageFid,
          'image_alt' => trim((string) ($row['image_alt'] ?? '')),
          'category_en' => trim((string) ($row['category_en'] ?? '')),
          'category_fr' => trim((string) ($row['category_fr'] ?? '')),
          'title_en' => $titleEn,
          'title_fr' => $titleFr,
          'date' => trim((string) ($row['date'] ?? '')),
          'url_en' => trim((string) ($row['url_en'] ?? '')),
          'url_fr' => trim((string) ($row['url_fr'] ?? '')),
        ];
      }
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * Formats a stored date for display.
   */
  public function formatDate(string $date, string $langcode): string {
    if ($date === '') {
      return '';
    }
    $timestamp = strtotime($date);
    if ($timestamp === FALSE) {
      return $date;
    }
    return $this->dateFormatter->format($timestamp, 'medium', [], $langcode);
  }

  /**
   * @return list<array<string, mixed>>
   */
  public static function defaultItems(): array {
    return [
      [
        'weight' => 0,
        'image' => NULL,
        'image_alt' => '',
        'category_en' => 'Market study',
        'category_fr' => 'Étude de marché',
        'title_en' => 'Office market trends 2026',
        'title_fr' => 'Tendances du marché des bureaux 2026',
        'date' => date('Y-m-d'),
        'url_en' => '/research',
        'url_fr' => '/recherche',
      ],
      [
        'weight' => 1,
        'image' => NULL,
        'image_alt' => '',
        'category_en' => 'Insight',
        'category_fr' => 'Analyse',
        'title_en' => 'Logistics outlook in France',
        'title_fr' => 'Perspectives logistique en France',
        'date' => date('Y-m-d', strtotime('-1 month')),
        'url_en' => '/research',
        'url_fr' => '/recherche',
      ],
    ];
  }

}
