<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_email\Service\EmailDesignTokens;

/**
 * Builds a highlighted availability dates block for schedule_visit emails.
 */
final class ScheduleVisitAvailabilitiesEmailBlockBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly RendererInterface $renderer,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly EmailDesignTokens $emailDesignTokens,
  ) {}

  /**
   * Renders preferred visit dates from submission values.
   *
   * @param array<string, mixed> $data
   *   Webform submission values.
   */
  public function buildHtmlFromSubmissionData(array $data, ?string $langcode = NULL): string {
    $raw = trim((string) ($data[ScheduleVisitAvailabilities::ELEMENT_KEY] ?? ''));
    if ($raw === '') {
      return '';
    }

    $dates = ScheduleVisitAvailabilities::parseDates($raw);
    if ($dates === []) {
      return '';
    }

    sort($dates);

    $options = $langcode !== NULL && $langcode !== '' ? ['langcode' => $langcode] : [];
    $formattedDates = [];
    foreach ($dates as $date) {
      $timestamp = strtotime($date . ' 12:00:00 UTC');
      if ($timestamp === FALSE) {
        continue;
      }

      $formattedDates[] = $this->dateFormatter->format(
        $timestamp,
        'custom',
        'l j F Y',
        'UTC',
        $langcode ?? '',
      );
    }

    if ($formattedDates === []) {
      return '';
    }

    $build = [
      '#theme' => 'ps_schedule_visit_availabilities_email_block',
      '#title' => (string) $this->t('Preferred visit dates', [], $options + ['context' => 'Schedule visit email']),
      '#dates' => $formattedDates,
    ];

    foreach ($this->emailDesignTokens->getPreprocessVariables() as $key => $value) {
      $build['#' . $key] = $value;
    }

    return (string) $this->renderer->renderPlain($build);
  }

}
