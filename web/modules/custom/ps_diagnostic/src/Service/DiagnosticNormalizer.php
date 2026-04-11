<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Diagnostic normalizer service.
 *
 * Validates and normalizes diagnostic data with auto-calculation.
 * Uses DiagnosticClassCalculator for automatic class determination.
 */
final class DiagnosticNormalizer implements DiagnosticNormalizerInterface {

  /**
   * Constructs a DiagnosticNormalizer.
   *
   * @param \Drupal\ps_diagnostic\Service\DiagnosticClassCalculatorInterface $classCalculator
   *   The class calculator service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   */
  public function __construct(
    private readonly DiagnosticClassCalculatorInterface $classCalculator,
    private readonly LoggerChannelFactoryInterface $loggerFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function normalize(array $data): array {
    $normalized = $data;

    // Normalize type_id.
    if (isset($normalized['type_id']) && !is_scalar($normalized['type_id'])) {
      $normalized['type_id'] = NULL;
    }

    // Normalize value (negatives → NULL).
    if (isset($normalized['value'])) {
      if (!is_numeric($normalized['value'])) {
        $normalized['value'] = NULL;
      }
      elseif ((float) $normalized['value'] < 0) {
        $normalized['value'] = NULL;
      }
      else {
        $normalized['value'] = (float) $normalized['value'];
      }
    }

    // Auto-calculate class if value provided and no manual override.
    if (empty($normalized['class']) &&
        !empty($normalized['type_id']) &&
        isset($normalized['value']) &&
        is_numeric($normalized['value'])) {
      $calculated = $this->classCalculator->calculateClass(
        (string) $normalized['type_id'],
        (float) $normalized['value']
      );
      if ($calculated !== NULL) {
        $normalized['class'] = $calculated;
      }
    }

    // Normalize dates.
    if (isset($normalized['valid_from']) && !is_scalar($normalized['valid_from'])) {
      $normalized['valid_from'] = NULL;
    }
    if (isset($normalized['valid_to']) && !is_scalar($normalized['valid_to'])) {
      $normalized['valid_to'] = NULL;
    }

    // Validate date coherence.
    if (!empty($normalized['valid_from']) && !empty($normalized['valid_to'])) {
      $from = @strtotime((string) $normalized['valid_from']);
      $to = @strtotime((string) $normalized['valid_to']);
      if ($from !== FALSE && $to !== FALSE && $from > $to) {
        $this->loggerFactory->get('ps_diagnostic')
          ->warning('Invalid date range: valid_from > valid_to');
        $normalized['valid_to'] = NULL;
      }
    }

    // Normalize flags.
    $normalized['no_classification'] = !empty($normalized['no_classification']);
    $normalized['non_applicable'] = !empty($normalized['non_applicable']);

    return $normalized;
  }

}
