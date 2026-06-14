<?php

declare(strict_types=1);

namespace Drupal\ps_content\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_content\Form\ContentBlockFormTrait;

/**
 * Block form builder for the tools & resources accordion block.
 */
final class OutilsAccordionBlockFormBuilder {

  use ContentBlockFormTrait;

  private const MAX_ITEMS = 8;

  private const MIN_VISIBLE_SLOTS = 3;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $items = $this->sortItemsByWeight($config['items'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $items,
      static fn (array $item): bool => trim((string) ($item['question'] ?? '')) !== '',
      self::MAX_ITEMS,
      self::MIN_VISIBLE_SLOTS,
    );
    $defaultOpenDelta = $this->defaultOpenDelta($items, $slotCount);

    $form = [
      'editing_language' => $this->buildContentEditingLanguageNotice(),
      'items_intro' => $this->buildBodyBlockSectionHeaderNotice(),
      'items' => [
        '#type' => 'container',
        '#tree' => TRUE,
        '#attributes' => ['class' => ['ps-outils-accordion-form__items']],
      ],
    ];

    $form['items']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $items,
      static fn (array $item): string => trim((string) ($item['question'] ?? '')),
      'ps-outils-accordion-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $question = trim((string) ($item['question'] ?? ''));

      $form['items'][$delta] = [
        '#type' => 'details',
        '#title' => $question !== ''
          ? $question
          : $this->t('Accordion item @number', ['@number' => $delta + 1]),
        '#open' => $question !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-outils-accordion-form__item']],
      ];

      $form['items'][$delta]['content'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Accordion content'),
        '#description' => $this->t('Question shown in the accordion header and answer revealed when opened.'),
      ];
      $form['items'][$delta]['content']['question'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Question'),
        '#default_value' => $item['question'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['content']['answer'] = [
        '#type' => 'text_format',
        '#title' => $this->t('Answer'),
        '#format' => 'basic_html',
        '#default_value' => $this->textFormatDefault($item['answer'] ?? ''),
        '#rows' => 4,
        '#description' => $this->t('Rich text shown inside the open panel.'),
      ];

      $form['items'][$delta]['illustration'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Illustration'),
        '#description' => $this->t('Optional image or SVG displayed on desktop (side panel) and inside the open panel on mobile.'),
      ];
      $form['items'][$delta]['illustration']['media'] = $this->buildMediaLibraryElement(
        $this->t('Illustration'),
        $item['illustration'] ?? ($delta === 0 ? ($config['illustration'] ?? NULL) : NULL),
      );

      $form['items'][$delta]['cta'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Call to action'),
        '#description' => $this->t('Optional button at the bottom of the open panel.'),
      ];
      $form['items'][$delta]['cta']['link_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button label'),
        '#default_value' => $item['link_label'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['cta']['link_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#default_value' => $item['link_url'] ?? '',
        '#maxlength' => 512,
        '#description' => $this->t('Internal path (e.g. /outils/simulateur) or absolute URL.'),
        '#states' => [
          'visible' => [
            ':input[name="settings[items][' . $delta . '][cta][link_label]"]' => ['!empty' => TRUE],
          ],
        ],
      ];
      $form['items'][$delta]['cta']['button_style'] = $this->buildButtonStyleElement(
        (string) ($item['button_style'] ?? 'outline'),
      ) + [
        '#states' => [
          'visible' => [
            ':input[name="settings[items][' . $delta . '][cta][link_label]"]' => ['!empty' => TRUE],
          ],
        ],
      ];

      $form['items'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this item'),
      );
    }

    $form['default_open'] = [
      '#type' => 'select',
      '#title' => $this->t('Item opened by default'),
      '#options' => $this->defaultOpenOptions($items, $slotCount),
      '#default_value' => (string) $defaultOpenDelta,
      '#description' => $this->t('Controls which panel is expanded initially and which illustration shows on desktop.'),
    ];

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_ITEMS);

    $form['#attributes']['class'][] = 'ps-outils-accordion-form';

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle'], $config['illustration'], $config['illustration_alt'], $config['ui_mode']);

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $defaultOpenDelta = (int) $form_state->getValue('default_open');
    $weights = $this->extractRepeaterOrderWeights($rows);
    $items = [];

    foreach ($rows as $delta => $row) {
      if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $content = is_array($row['content'] ?? NULL) ? $row['content'] : [];
      $cta = is_array($row['cta'] ?? NULL) ? $row['cta'] : [];
      $illustration = is_array($row['illustration'] ?? NULL) ? $row['illustration'] : [];

      $question = trim((string) ($content['question'] ?? ''));
      if ($question === '') {
        continue;
      }

      $buttonStyle = (string) ($cta['button_style'] ?? 'outline');
      if (!in_array($buttonStyle, ['outline', 'primary'], TRUE)) {
        $buttonStyle = 'outline';
      }

      $items[] = [
        'weight' => $weights[(int) $delta] ?? (int) $delta,
        'illustration' => $this->persistMediaReference($illustration['media'] ?? NULL),
        'question' => $question,
        'answer' => $this->textFormatValue($content['answer'] ?? ''),
        'link_label' => trim((string) ($cta['link_label'] ?? '')),
        'link_url' => trim((string) ($cta['link_url'] ?? '')),
        'button_style' => $buttonStyle,
        'opened_by_default' => ((int) $delta === $defaultOpenDelta),
      ];
    }

    if ($items !== [] && !array_filter($items, static fn (array $item): bool => !empty($item['opened_by_default']))) {
      $items[0]['opened_by_default'] = TRUE;
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @param array<int, array<string, mixed>> $items
   *
   * @return array<string, string>
   */
  private function defaultOpenOptions(array $items, int $slotCount): array {
    $options = [];
    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? [];
      $question = trim((string) ($item['question'] ?? ''));
      $options[(string) $delta] = $question !== ''
        ? $question
        : (string) $this->t('Accordion item @number', ['@number' => $delta + 1]);
    }

    return $options;
  }

  /**
   * @param array<int, array<string, mixed>> $items
   */
  private function defaultOpenDelta(array $items, int $slotCount): int {
    for ($delta = 0; $delta < $slotCount; $delta++) {
      if (!empty($items[$delta]['opened_by_default'])) {
        return $delta;
      }
    }

    return 0;
  }

}
