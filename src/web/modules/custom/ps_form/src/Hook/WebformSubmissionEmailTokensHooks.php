<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_form\Service\WebformSubmissionEmailValuesFormatter;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Overrides webform submission value tokens for PS email formatting.
 */
final class WebformSubmissionEmailTokensHooks {

  public function __construct(
    private readonly WebformSubmissionEmailValuesFormatter $valuesFormatter,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Replaces [webform_submission:values] output for email-friendly layout.
   */
  #[Hook('tokens_alter')]
  public function tokensAlter(array &$replacements, array $context, BubbleableMetadata $bubbleable_metadata): void {
    if (($context['type'] ?? '') !== 'webform_submission') {
      return;
    }

    $submission = $context['data']['webform_submission'] ?? NULL;
    if (!$submission instanceof WebformSubmissionInterface) {
      return;
    }

    $bubbleable_metadata->addCacheableDependency($submission);
    $bubbleable_metadata->addCacheableDependency($submission->getWebform());

    foreach ($context['tokens'] as $name => $original) {
      if (!in_array($name, ['values', 'values:html'], TRUE)) {
        continue;
      }

      $options = $context['options'] ?? [];
      $options['html'] = ($name === 'values:html') || !empty($options['html']);
      $replacements[$original] = Markup::create(
        $this->valuesFormatter->formatHtml($submission, $options),
      );
    }
  }

  /**
   * Applies PS email H1 color to inline confirmation headings.
   */
  #[Hook('mail_alter')]
  public function mailAlter(array &$message): void {
    if (($message['module'] ?? '') !== 'webform') {
      return;
    }

    $secondary = (string) ($this->configFactory->get('ps_email.email_tokens')->get('secondary_color') ?: '#ba3075');
    if ($secondary === '') {
      return;
    }

    foreach ($message['body'] as $index => $body) {
      $html = $body instanceof Markup ? (string) $body : (string) $body;
      $updated = preg_replace(
        '/(<h1\\b[^>]*\\bcolor:\\s*)#333333/i',
        '$1' . $secondary,
        $html,
      );
      if ($updated !== NULL && $updated !== $html) {
        $message['body'][$index] = Markup::create($updated);
      }
    }
  }

}
