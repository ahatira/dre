<?php

declare(strict_types=1);

namespace Drupal\ps_price\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;

/**
 * Price normalizer service.
 *
 * Converts prices with different units to a common reference unit
 * (€/m²/year) for search comparison.
 *
 * Uses dictionary metadata for period multipliers to ensure consistency.
 */
final class PriceNormalizer {

  /**
   * Constructs a PriceNormalizer object.
   *
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(
    protected DictionaryManagerInterface $dictionaryManager,
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Normalizes a price to reference unit (€/m²/year).
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The price field item.
   * @param float $surfaceM2
   *   The surface area in m² (for conversion).
   *
   * @return float|null
   *   The normalized price or NULL if not applicable.
   */
  public function normalize(FieldItemInterface $item, float $surfaceM2 = 1.0): ?float {
    assert($item instanceof PriceItem);

    if ($item->amount === NULL) {
      return NULL;
    }

    $amount = (float) $item->amount;
    $unit = $item->unit_code ?? '';
    $period = $item->period_code ?? 'ANN';

    // Apply period conversion (dictionary codes ANN/MEN/TRI/SEM).
    $amount = $this->convertPeriod($amount, $period);

    // Apply unit conversion (dictionary codes SUR/GLO/OTH).
    $amount = $this->convertUnit($amount, $unit, $surfaceM2);

    return $amount;
  }

  /**
   * Converts amount based on period.
   *
   * Converts from the source period to the reference period configured
   * in settings. Reads multiplier from dictionary metadata to ensure
   * consistency.
   *
   * @param float $amount
   *   The amount.
   * @param string $period
   *   The source period code (ANN, MEN, TRI, SEM).
   *
   * @return float
   *   The amount converted to the reference period.
   */
  private function convertPeriod(float $amount, string $period): float {
    if (empty($period)) {
      return $amount;
    }

    // Get the reference period from settings (default: ANN = yearly).
    $referencePeriod = $this->configFactory->get('ps_price.settings')->get('reference_period') ?? 'ANN';

    // If source and reference are the same, no conversion needed.
    if ($period === $referencePeriod) {
      return $amount;
    }

    // Convert source period to yearly first (as common base).
    $metadata = $this->dictionaryManager->getMetadata('price_period', $period);
    $sourceMultiplier = $metadata['multiplier'] ?? $this->getFallbackMultiplier($period);
    $yearlyAmount = $amount * (float) $sourceMultiplier;

    // Convert from yearly to reference period.
    $refMetadata = $this->dictionaryManager->getMetadata('price_period', $referencePeriod);
    $refMultiplier = $refMetadata['multiplier'] ?? $this->getFallbackMultiplier($referencePeriod);

    // Divide by reference multiplier to get the amount in reference period.
    return $yearlyAmount / (float) $refMultiplier;
  }

  /**
   * Gets fallback multiplier for backward compatibility.
   *
   * @param string $period
   *   The period code.
   *
   * @return float
   *   The multiplier to convert to yearly.
   */
  private function getFallbackMultiplier(string $period): float {
    return match ($period) {
      'MEN' => 12.0,
      'TRI' => 4.0,
      'SEM' => 52.0,
      'ANN' => 1.0,
      default => 1.0,
    };
  }

  /**
   * Converts amount based on unit.
   *
   * @param float $amount
   *   The amount.
   * @param string $unit
   *   The unit (e.g., /m²/an, /an).
   * @param float $surfaceM2
   *   The surface area in m².
   *
   * @return float|null
   *   The amount converted to per m², or NULL if division by zero.
   */
  private function convertUnit(float $amount, string $unit, float $surfaceM2): float|null {
    if ($unit === 'GLO' && $surfaceM2 <= 0) {
      $behavior = $this->configFactory->get('ps_price.settings')->get('normalize_on_zero_surface') ?? 'null';
      return match ($behavior) {
        'null' => NULL,
        'zero' => 0.0,
        default => $amount,
      };
    }

    return match ($unit) {
      'SUR' => $amount,
      'GLO' => $amount / $surfaceM2,
      'OTH' => $amount,
      default => $amount,
    };
  }

}
