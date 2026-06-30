<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

/**
 * Maps business-facing CSV values to feature catalogue technical fields.
 */
final class FeatureCatalogueCsvMapper {

  /**
   * Business category labels keyed by normalized label.
   *
   * @var array<string, string>
   */
  private const CATEGORIES = [
    'equipements' => 'equipment',
    'équipements' => 'equipment',
    'equipment' => 'equipment',
    'services' => 'services',
    'etat du batiment' => 'building',
    'état du bâtiment' => 'building',
    'building' => 'building',
    'informations complementaires' => 'additional',
    'informations complémentaires' => 'additional',
    'other' => 'additional',
    'transport' => 'transport',
    'transports' => 'transport',
  ];

  /**
   * Business value types keyed by normalized label.
   *
   * @var array<string, string>
   */
  private const VALUE_TYPES = [
    'indicateur' => 'flag',
    'oui/non' => 'yes_no',
    'oui / non' => 'yes_no',
    'nombre' => 'numeric',
    'texte' => 'text',
    'date' => 'date',
  ];

  /**
   * Resolves a business category label to a feature group ID.
   */
  public function resolveCategory(string $categorie): ?string {
    $key = $this->normalizeBusinessLabel($categorie);
    if ($key === '') {
      return NULL;
    }

    return self::CATEGORIES[$key] ?? NULL;
  }

  /**
   * Resolves a business value type label to a type driver plugin ID.
   */
  public function resolveTypeDriver(string $typeValeur): ?string {
    $key = $this->normalizeBusinessLabel($typeValeur);
    if ($key === '') {
      return NULL;
    }

    return self::VALUE_TYPES[$key] ?? NULL;
  }

  /**
   * Parses a yes/no business value for expose_as_filter.
   */
  public function resolveExposeAsFilter(string $value): bool {
    $normalized = $this->normalizeBusinessLabel($value);
    return in_array($normalized, ['oui', 'yes', '1', 'true', 'vrai'], TRUE);
  }

  /**
   * Builds a Drupal-safe definition ID from a CRM feature code.
   */
  public function normalizeDefinitionId(string $code): string {
    $code = trim($code);
    if ($code === '') {
      return '';
    }

    $code = strtolower($code);
    $code = preg_replace('/[^a-z0-9]+/u', '_', $code) ?? $code;

    return trim($code, '_');
  }

  /**
   * Normalizes a CRM feature code for storage.
   */
  public function normalizeFeatureCode(string $code): string {
    return strtoupper(trim($code));
  }

  /**
   * Returns allowed business category labels for documentation and validation.
   *
   * @return string[]
   *   Human-readable category labels.
   */
  public function getAllowedCategoryLabels(): array {
    return [
      'Équipements',
      'Services',
      'État du bâtiment',
      'Informations complémentaires',
      'Transport',
    ];
  }

  /**
   * Returns allowed business value type labels.
   *
   * @return string[]
   *   Human-readable value type labels.
   */
  public function getAllowedValueTypeLabels(): array {
    return [
      'Indicateur',
      'Oui/Non',
      'Nombre',
      'Texte',
      'Date',
    ];
  }

  /**
   * Normalizes a business label for dictionary lookup.
   */
  private function normalizeBusinessLabel(string $value): string {
    $value = trim($value);
    if ($value === '') {
      return '';
    }

    $value = mb_strtolower($value);
    $value = str_replace(['’', "'"], ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
  }

}
