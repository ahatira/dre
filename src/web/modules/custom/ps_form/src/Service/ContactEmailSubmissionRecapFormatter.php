<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\RendererInterface;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Builds a filtered label/value recap for contact confirmation emails.
 */
final class ContactEmailSubmissionRecapFormatter {

  /**
   * Element keys never shown in visitor confirmation recap.
   *
   * @var list<string>
   */
  private const EXCLUDED_KEYS = [
    'ps_from_hub',
    'from_hub',
    'optout_intro',
    'required_fields_note',
    'contact_details_intro',
    'message_intro',
    'legal_notice',
    'captcha',
    'altcha',
  ];

  /**
   * Element types skipped in recap output.
   *
   * @var list<string>
   */
  private const EXCLUDED_TYPES = [
    'webform_actions',
    'webform_wizard_page',
    'webform_markup',
    'processed_text',
    'item',
    'hidden',
    'fieldset',
  ];

  public function __construct(
    private readonly WebformElementManagerInterface $elementManager,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Returns recap rows as label/value pairs for Twig rendering.
   *
   * @return list<array{label: string, value: \Drupal\Component\Render\MarkupInterface|string}>
   *   Ordered recap rows.
   */
  public function buildRows(WebformSubmissionInterface $submission): array {
    $webform = $submission->getWebform();
    $elements = $webform->getElementsInitializedAndFlattened();
    $options = [
      'email' => TRUE,
      'exclude_empty' => TRUE,
      'exclude_empty_checkbox' => TRUE,
    ];

    $rows = [];
    foreach ($elements as $key => $element) {
      if (!is_string($key) || $this->shouldSkipKey($key, $element)) {
        continue;
      }

      $plugin = $this->elementManager->getElementInstance($element, $submission);
      if (!$plugin->isInput($element) || !$plugin->hasValue($element, $submission, $options)) {
        continue;
      }

      $label = (string) ($element['#title'] ?? $key);
      $formatted = $plugin->formatText($element, $submission, $options);
      if (is_array($formatted)) {
        $value = trim(strip_tags((string) $this->renderer->renderPlain($formatted)));
      }
      else {
        $value = trim(strip_tags((string) $formatted));
      }
      if ($value === '' || $value === '{Empty}') {
        continue;
      }

      $rows[] = [
        'label' => $label,
        'value' => Html::escape($value),
      ];
    }

    return $rows;
  }

  /**
   * Determines whether an element should be omitted from the recap.
   *
   * @param string $key
   *   Element machine name.
   * @param array<string, mixed> $element
   *   Flattened webform element.
   */
  private function shouldSkipKey(string $key, array $element): bool {
    if (in_array($key, self::EXCLUDED_KEYS, TRUE)) {
      return TRUE;
    }
    if (str_starts_with($key, 'optout_')) {
      return TRUE;
    }

    $type = (string) ($element['#type'] ?? '');
    return in_array($type, self::EXCLUDED_TYPES, TRUE);
  }

}
