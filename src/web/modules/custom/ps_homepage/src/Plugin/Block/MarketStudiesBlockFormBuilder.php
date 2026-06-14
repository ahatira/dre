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
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(),
      'section_header' => $this->buildSectionHeaderFields($config),
      'section_footer' => $this->buildSectionFooterFields($config),
    ];

    $items = $this->sortItemsByWeight($config['items'] ?? []);

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Image'),
        $this->t('Category'),
        $this->t('Title'),
        $this->t('Date'),
        $this->t('URL'),
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
      $form['items'][$delta]['image'] = $this->buildMediaLibraryElement(
        $this->t('Image'),
        $item['image'] ?? NULL,
        FALSE,
      );
      $form['items'][$delta]['category'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Category'),
        '#title_display' => 'invisible',
        '#default_value' => $item['category'] ?? '',
        '#maxlength' => 128,
      ];
      $form['items'][$delta]['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#title_display' => 'invisible',
        '#default_value' => $item['title'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['date'] = [
        '#type' => 'date',
        '#title' => $this->t('Date'),
        '#title_display' => 'invisible',
        '#default_value' => $item['date'] ?? '',
      ];
      $form['items'][$delta]['url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#title_display' => 'invisible',
        '#default_value' => $item['url'] ?? '',
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
    $config['title'] = trim((string) $form_state->getValue(['section_header', 'title']));
    $config['subtitle'] = trim((string) $form_state->getValue(['section_header', 'subtitle']));
    $config['see_more_label'] = trim((string) $form_state->getValue(['section_footer', 'see_more_label']));
    $config['see_more_url'] = trim((string) $form_state->getValue(['section_footer', 'see_more_url']));

    $rows = $form_state->getValue('items');
    $items = [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if (!is_array($row) || !empty($row['remove'])) {
          continue;
        }

        $title = trim((string) ($row['title'] ?? ''));
        $mediaMid = $this->persistMediaReference($row['image'] ?? NULL);
        if ($mediaMid === NULL || $title === '') {
          continue;
        }

        $items[] = [
          'weight' => (int) ($row['weight'] ?? $delta),
          'image' => $mediaMid,
          'category' => trim((string) ($row['category'] ?? '')),
          'title' => $title,
          'date' => trim((string) ($row['date'] ?? '')),
          'url' => trim((string) ($row['url'] ?? '')),
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

}
