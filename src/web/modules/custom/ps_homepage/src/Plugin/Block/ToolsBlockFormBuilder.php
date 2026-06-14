<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage tools & resources section.
 */
final class ToolsBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 8;

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

    $form['media'] = [
      '#type' => 'details',
      '#title' => $this->t('Illustration'),
      '#open' => FALSE,
    ];
    $form['media']['illustration'] = $this->buildMediaLibraryElement(
      $this->t('Illustration image'),
      $config['illustration'] ?? NULL,
    );

    $items = $this->sortItemsByWeight($config['items'] ?? []);

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Question'),
        $this->t('Answer'),
        $this->t('Link label'),
        $this->t('URL'),
        $this->t('Open'),
        $this->t('Remove'),
      ],
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'tools-weight',
        ],
      ],
      '#description' => $this->t('Up to @max accordion items. Only one may be opened by default.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['tools-weight']],
      ];
      $form['items'][$delta]['question'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Question'),
        '#title_display' => 'invisible',
        '#default_value' => $item['question'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['answer'] = [
        '#type' => 'text_format',
        '#title' => $this->t('Answer'),
        '#title_display' => 'invisible',
        '#format' => 'basic_html',
        '#default_value' => $this->textFormatDefault($item['answer'] ?? ''),
      ];
      $form['items'][$delta]['link_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link label'),
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#title_display' => 'invisible',
        '#default_value' => $item['link_url'] ?? '',
        '#maxlength' => 512,
      ];
      $form['items'][$delta]['opened_by_default'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Open by default'),
        '#title_display' => 'invisible',
        '#return_value' => 1,
        '#default_value' => !empty($item['opened_by_default']),
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

    $config['illustration'] = $this->persistMediaReference($form_state->getValue(['media', 'illustration']));

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $items = [];
    $openedDelta = NULL;
    foreach ($rows as $delta => $row) {
      if (!is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $question = trim((string) ($row['question'] ?? ''));
      if ($question === '') {
        continue;
      }

      $opened = !empty($row['opened_by_default']);
      if ($opened && $openedDelta === NULL) {
        $openedDelta = $delta;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'question' => $question,
        'answer' => $this->textFormatValue($row['answer'] ?? ''),
        'link_label' => trim((string) ($row['link_label'] ?? '')),
        'link_url' => trim((string) ($row['link_url'] ?? '')),
        'opened_by_default' => $opened && $openedDelta === $delta,
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  private function textFormatDefault(mixed $value): string {
    if (is_array($value)) {
      return (string) ($value['value'] ?? '');
    }
    return (string) $value;
  }

  private function textFormatValue(mixed $value): string {
    if (!is_array($value)) {
      return trim((string) $value);
    }
    return trim((string) ($value['value'] ?? ''));
  }

}
