<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Date feature type plugin.
 *
 * @FeatureType(
 *   id = "date",
 *   label = @Translation("Date"),
 *   description = @Translation("Date with precision: {value: date, precision: string}")
 * )
 */
class DateFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['value'])) {
      $errors[] = "Date payload must contain 'value' key.";
    }
    elseif (!is_string($payload['value'])) {
      $errors[] = "Date 'value' must be a string.";
    }
    elseif (!$this->isValidDate($payload['value'])) {
      $errors[] = "Date 'value' must be a valid date format (YYYY-MM-DD).";
    }

    if (!isset($payload['precision'])) {
      $errors[] = "Date payload must contain 'precision' key.";
    }
    elseif (!in_array($payload['precision'], ['day', 'month', 'year', 'quarter'], TRUE)) {
      $errors[] = "Date 'precision' must be one of: day, month, year, quarter.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'value' => trim($payload['value'] ?? ''),
      'precision' => strtolower(trim($payload['precision'] ?? 'day')),
    ];
  }

  /**
   * Validates if a string is a valid date.
   *
   * @param string $date
   *   The date string to validate.
   *
   * @return bool
   *   TRUE if valid, FALSE otherwise.
   */
  protected function isValidDate(string $date): bool {
    $d = \DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'value' => [
        '#type' => 'date',
        '#title' => t('Date'),
        '#default_value' => $current_payload['value'] ?? '',
        '#required' => TRUE,
      ],
      'precision' => [
        '#type' => 'select',
        '#title' => t('Precision'),
        '#options' => [
          'day' => t('Day'),
          'month' => t('Month'),
          'quarter' => t('Quarter'),
          'year' => t('Year'),
        ],
        '#default_value' => $current_payload['precision'] ?? 'day',
        '#required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $value = $payload['value'] ?? '';
    $precision = $payload['precision'] ?? 'day';
    
    if (empty($value)) {
      return '';
    }
    
    $date = \DateTime::createFromFormat('Y-m-d', $value);
    if (!$date) {
      return $value;
    }
    
    switch ($precision) {
      case 'year':
        return $date->format('Y');
      
      case 'quarter':
        $quarter = ceil($date->format('n') / 3);
        return sprintf('Q%d %s', $quarter, $date->format('Y'));
      
      case 'month':
        return $date->format('F Y');
      
      case 'day':
      default:
        return $date->format('F j, Y');
    }
  }

}
