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
