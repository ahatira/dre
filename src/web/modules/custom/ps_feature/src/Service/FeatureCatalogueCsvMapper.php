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
