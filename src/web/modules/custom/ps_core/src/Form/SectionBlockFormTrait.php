<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Shared LB section block form helpers (header, footer, table ordering).
 */
trait SectionBlockFormTrait {

  use StringTranslationTrait;

  /**
   * @return array<string, mixed>
   */
  protected function buildEditingLanguageNotice(array $libraries = []): array {
    $langcode = $this->editingLangcode();
    $language = \Drupal::languageManager()->getLanguage($langcode);
    $label = $language ? $language->getName() : strtoupper($langcode);

    $attached = [];
    if ($libraries !== []) {
      $attached['library'] = $libraries;
    }

    return [
      '#type' => 'item',
      '#markup' => (string) $this->t(
        'You are editing content in <strong>@language</strong>. Switch the page translation to edit another language.',
        ['@language' => $label],
      ),
      '#wrapper_attributes' => ['class' => ['messages', 'messages--status', 'ps-section-block-lang-notice']],
      '#attached' => $attached,
    ];
  }

  /**
   * Info notice: section title/subtitle live in the Section header LB block.
   *
   * @return array<string, mixed>
   */
  protected function buildBodyBlockSectionHeaderNotice(): array {
    return [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Section title and subtitle are configured in the <strong>Section header</strong> block above this body block in Layout Builder.') . '</p>',
      '#wrapper_attributes' => ['class' => ['messages', 'messages--info', 'ps-section-block-lang-notice']],
    ];
  }

  /**
   * Info notice: footer CTA lives in the Section footer LB block.
   *
   * @return array<string, mixed>
   */
  protected function buildBodyBlockSectionFooterNotice(): array {
    return [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('The footer CTA is configured in the <strong>Section footer</strong> block below this body block in Layout Builder.') . '</p>',
      '#wrapper_attributes' => ['class' => ['messages', 'messages--info', 'ps-section-block-lang-notice']],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildButtonStyleElement(string $default = 'outline'): array {
    return [
      '#type' => 'select',
      '#title' => $this->t('Button style'),
      '#options' => [
        'outline' => (string) $this->t('Outline (primary)'),
        'primary' => (string) $this->t('Primary (filled)'),
      ],
      '#default_value' => $default,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildRemoveItemCheckbox(string $label = ''): array {
    return [
      '#type' => 'checkbox',
      '#title' => $label !== '' ? $label : (string) $this->t('Remove this item'),
      '#return_value' => 1,
    ];
  }

  /**
   * Computes how many repeater slots to render (filled rows + one empty).
   *
   * @param array<int, array<string, mixed>> $items
   * @param callable(array<string, mixed>): bool $isFilled
   */
  protected function computeRepeaterSlotCount(array $items, callable $isFilled, int $max, int $minVisible): int {
    $filled = 0;
    foreach ($items as $item) {
      if (is_array($item) && $isFilled($item)) {
        $filled++;
      }
    }

    return min($max, max($minVisible, $filled + 1));
  }

  protected function textFormatDefault(mixed $value): string {
    if (is_array($value)) {
      return (string) ($value['value'] ?? '');
    }
    return (string) $value;
  }

  protected function textFormatValue(mixed $value): string {
    if (!is_array($value)) {
      return trim((string) $value);
    }
    return trim((string) ($value['value'] ?? ''));
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionHeaderFields(array $config, bool $includeSubtitle = TRUE): array {
    $fields = [
      '#type' => 'details',
      '#title' => $this->t('Section header'),
      '#open' => TRUE,
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Section title'),
        '#default_value' => $config['title'] ?? '',
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
    ];

    if ($includeSubtitle) {
      $fields['subtitle'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Section subtitle'),
        '#default_value' => $config['subtitle'] ?? '',
        '#rows' => 3,
        '#maxlength' => 512,
      ];
    }

    return $fields;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionFooterFields(array $config, bool $includeUrl = TRUE): array {
    $fields = [
      '#type' => 'details',
      '#title' => $this->t('Section footer'),
      '#open' => FALSE,
      'see_more_label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA label'),
        '#default_value' => $config['see_more_label'] ?? '',
        '#maxlength' => 255,
      ],
    ];

    if ($includeUrl) {
      $fields['see_more_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA URL'),
        '#default_value' => $config['see_more_url'] ?? '',
        '#maxlength' => 512,
      ];
    }

    return $fields;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildLinkTypeElement(string $fieldName, mixed $defaultValue, array $options): array {
    return [
      '#type' => 'select',
      '#title' => $this->t('Link type'),
      '#options' => $options,
      '#default_value' => $defaultValue ?: 'url',
    ];
  }

  /**
   * @param list<string> $parents
   */
  protected function buildStateSelector(array $parents, string $field): string {
    $name = 'settings';
    foreach ($parents as $parent) {
      $name .= '[' . $parent . ']';
    }
    return ':input[name="' . $name . '[' . $field . ']"]';
  }

  /**
   * @param array<int, array<string, mixed>> $items
   *
   * @return array<int, array<string, mixed>>
   */
  protected function sortItemsByWeight(array $items): array {
    usort($items, static function (array $a, array $b): int {
      return ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0));
    });
    return array_values($items);
  }

  /**
   * Drag-and-drop order table for homepage repeater block forms.
   *
   * Place as $form[$containerKey]['order'] inside a #tree container.
   *
   * @param array<int, array<string, mixed>> $items
   */
  protected function buildRepeaterOrderTable(
    int $slotCount,
    array $items,
    callable $labelResolver,
    string $weightGroup,
  ): array {
    $table = [
      '#type' => 'table',
      '#header' => [
        $this->t('Order'),
        $this->t('Item'),
      ],
      '#empty' => $this->t('No items yet.'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => $weightGroup,
        ],
      ],
      '#attributes' => ['class' => ['ps-homepage-repeater-order']],
      '#attached' => ['library' => ['ui_suite_bnp/drupal.tabledrag']],
    ];

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      if (!is_array($item)) {
        $item = ['weight' => $delta];
      }
      $label = trim((string) $labelResolver($item, $delta));

      $table[$delta] = [
        '#attributes' => ['class' => ['draggable']],
        '#weight' => (int) ($item['weight'] ?? $delta),
      ];
      $table[$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for row @number', ['@number' => $delta + 1]),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#delta' => max(50, $slotCount + 10),
        '#attributes' => ['class' => [$weightGroup]],
      ];
      $table[$delta]['label'] = [
        '#wrapper_attributes' => ['class' => ['ps-homepage-repeater-order__label']],
        '#markup' => $label !== ''
          ? $label
          : (string) $this->t('Item @number', ['@number' => $delta + 1]),
      ];
    }

    return $table;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildRepeaterOrderHelp(int $max): array {
    return [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Drag rows to reorder items (up to @max). Expand each item below to edit its content.', ['@max' => $max]) . '</p>',
    ];
  }

  /**
   * @param mixed $containerValue
   *
   * @return array<int, int>
   */
  protected function extractRepeaterOrderWeights(mixed $containerValue, string $orderKey = 'order'): array {
    if (!is_array($containerValue) || !isset($containerValue[$orderKey]) || !is_array($containerValue[$orderKey])) {
      return [];
    }

    $weights = [];
    foreach ($containerValue[$orderKey] as $delta => $row) {
      if (!is_array($row)) {
        continue;
      }
      $weights[(int) $delta] = (int) ($row['weight'] ?? $delta);
    }

    return $weights;
  }

  protected function editingLangcode(): string {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      return $node->language()->getId();
    }

    return \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  /**
   * Extracts icon value from form state for a table row.
   */
  protected function extractIconValue(FormStateInterface $form_state, array $parents, string $field, string $fallback = ''): string {
    $value = $form_state->getValue($parents);
    if (!is_array($value)) {
      return $fallback;
    }
    return \Drupal\ps_core\Utility\IconIdUtility::extractFromSubmission($value[$field] ?? NULL, $fallback);
  }

}
