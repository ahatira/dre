<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\webform\Plugin\WebformElementAttachmentInterface;
use Drupal\webform\Plugin\WebformElementCompositeInterface;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Formats webform submission values for transactional email bodies.
 */
final class WebformSubmissionEmailValuesFormatter {

  private const ROW_STYLE = 'margin:0 0 12px;font-size:14px;line-height:1.6;color:#333333;';

  public function __construct(
    private readonly WebformElementManagerInterface $elementManager,
    private readonly WebformSubmissionConditionsValidatorInterface $conditionsValidator,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Builds HTML for the [webform_submission:values] token in emails.
   */
  public function formatHtml(WebformSubmissionInterface $webform_submission, array $options = []): string {
    $options += [
      'email' => TRUE,
      'exclude_empty' => TRUE,
      'exclude_empty_checkbox' => FALSE,
      'excluded_elements' => [],
      'exclude_attachments' => FALSE,
      'ignore_access' => FALSE,
    ];

    $webform = $webform_submission->getWebform();
    $elements = $webform->getElementsInitializedFlattenedAndHasValue();
    $rows = [];

    foreach ($elements as $key => $element) {
      if (!$this->isElementVisible($element, $webform_submission, $options)) {
        continue;
      }

      $plugin = $this->elementManager->getElementInstance($element, $webform_submission);
      $plugin->replaceTokens($element, $webform_submission);

      if ($plugin->isEmptyExcluded($element, $options) && !$plugin->getValue($element, $webform_submission, $options)) {
        continue;
      }

      $label = (string) ($element['#admin_title'] ?? $element['#title'] ?? $key);
      $value = $plugin->formatHtml($element, $webform_submission, $options);
      if (is_array($value)) {
        $value = (string) $this->renderer->renderInIsolation($value);
      }
      elseif ($value instanceof MarkupInterface) {
        $value = (string) $value;
      }
      else {
        $value = (string) $value;
      }

      $value = trim($value);
      if ($value === '' || $value === '&nbsp;') {
        continue;
      }

      $rows[] = $this->usesBlockLayout($element)
        ? $this->buildBlockRow($label, $value)
        : $this->buildInlineRow($label, $value);
    }

    return implode("\n", $rows);
  }

  /**
   * Whether the field label and value should be rendered on separate lines.
   *
   * @param array<string, mixed> $element
   *   Webform element definition.
   */
  private function usesBlockLayout(array $element): bool {
    $type = (string) ($element['#type'] ?? '');

    return match ($type) {
      'textarea', 'checkboxes' => TRUE,
      default => $type === 'select' && !empty($element['#multiple']),
    };
  }

  /**
   * Builds an inline "label: value" row.
   */
  private function buildInlineRow(string $label, string $value): string {
    return sprintf(
      '<p style="%s"><strong>%s:</strong> %s</p>',
      self::ROW_STYLE,
      htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
      $value,
    );
  }

  /**
   * Builds a block row (label on one line, value below).
   */
  private function buildBlockRow(string $label, string $value): string {
    return sprintf(
      '<p style="%s"><strong>%s:</strong></p><div style="%s">%s</div>',
      self::ROW_STYLE,
      htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
      self::ROW_STYLE,
      $value,
    );
  }

  /**
   * Determines whether an element is visible in the email context.
   *
   * @param array<string, mixed> $element
   *   Webform element definition.
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   Webform submission.
   * @param array<string, mixed> $options
   *   Formatting options.
   */
  private function isElementVisible(array $element, WebformSubmissionInterface $webform_submission, array $options): bool {
    if (isset($element['#webform_key'], $options['excluded_elements'][$element['#webform_key']])) {
      return FALSE;
    }

    if (!empty($options['exclude_attachments'])) {
      $plugin = $this->elementManager->getElementInstance($element, $webform_submission);
      if ($plugin instanceof WebformElementAttachmentInterface
        && !$plugin instanceof WebformElementCompositeInterface) {
        return FALSE;
      }
    }

    if (!$this->conditionsValidator->isElementVisible($element, $webform_submission)) {
      return FALSE;
    }

    if (!empty($options['ignore_access'])) {
      return TRUE;
    }

    if (isset($element['#access']) && (($element['#access'] instanceof AccessResultInterface && $element['#access']->isForbidden()) || $element['#access'] === FALSE)) {
      return FALSE;
    }

    $plugin = $this->elementManager->getElementInstance($element, $webform_submission);
    return $plugin->checkAccessRules('view', $element);
  }

}
