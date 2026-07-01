<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Component\Datetime\DateTimePlus;

/**
 * Parses and validates schedule_visit availability dates.
 */
final class ScheduleVisitAvailabilities {

  public const ELEMENT_KEY = 'availabilities';

  public const MAX_DATES = 5;

  public const MIN_DATES = 1;

  /**
   * Earliest selectable day offset from today (tomorrow).
   */
  public const MIN_DATE_OFFSET_DAYS = 1;

  /**
   * Latest selectable day offset from today (15-day window from tomorrow).
   */
  public const MAX_DATE_OFFSET_DAYS = 15;

  /**
   * Splits a flatpickr multiple-mode value into ISO date strings.
   *
   * @return list<string>
   *   Parsed ISO date strings.
   */
  public static function parseDates(string $value): array {
    $value = trim($value);
    if ($value === '') {
      return [];
    }

    $dates = [];
    foreach (preg_split('/\s*,\s*/', $value) ?: [] as $part) {
      $part = trim($part);
      if ($part !== '') {
        $dates[] = $part;
      }
    }

    return $dates;
  }

  /**
   * Validates a flatpickr multiple-mode availability value.
   *
   * @return list<string>
   *   ISO date strings when valid.
   *
   * @throws \InvalidArgumentException
   *   When the value is invalid.
   */
  public static function assertValid(string $value): array {
    $dates = self::parseDates($value);
    $count = count($dates);

    if ($count < self::MIN_DATES) {
      throw new \InvalidArgumentException('At least one availability date is required.');
    }

    if ($count > self::MAX_DATES) {
      throw new \InvalidArgumentException(sprintf('No more than %d availability dates are allowed.', self::MAX_DATES));
    }

    $unique = [];
    $today = new \DateTimeImmutable('today');
    $minDate = $today->modify('+' . self::MIN_DATE_OFFSET_DAYS . ' days');
    $maxDate = $today->modify('+' . self::MAX_DATE_OFFSET_DAYS . ' days');

    foreach ($dates as $date) {
      try {
        $dateTime = new DateTimePlus($date, NULL, 'Y-m-d', ['validate_format' => TRUE]);
      }
      catch (\Exception) {
        throw new \InvalidArgumentException(sprintf('"%s" is not a valid date.', $date));
      }

      if ($dateTime->format('Y-m-d') !== $date) {
        throw new \InvalidArgumentException(sprintf('"%s" is not a valid date.', $date));
      }

      $dateImmutable = new \DateTimeImmutable($dateTime->format('Y-m-d'));
      if ($dateImmutable < $minDate || $dateImmutable > $maxDate) {
        throw new \InvalidArgumentException('Availability dates must be within the next 15 days, starting tomorrow.');
      }

      if (isset($unique[$date])) {
        throw new \InvalidArgumentException('Duplicate availability dates are not allowed.');
      }

      $unique[$date] = TRUE;
    }

    return $dates;
  }

}
