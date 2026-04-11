<?php

declare(strict_types=1);

namespace Drupal\ps_division\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_division\Entity\DivisionInterface;

/**
 * Division manager service implementation.
 *
 * Provides centralized business logic for division entities including
 * retrieval, validation, and surface aggregation with caching.
 *
 * @see \Drupal\ps_division\Service\DivisionManagerInterface
 */
final class DivisionManager implements DivisionManagerInterface {

  /**
   * Logger channel.
   */
  private readonly LoggerChannelInterface $logger;

  /**
   * Constructs a DivisionManager.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly CacheBackendInterface $cache,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->logger = $loggerFactory->get('ps_division');
  }

  /**
   * {@inheritdoc}
   */
  public function validate(DivisionInterface $division): array {
    $errors = [];

    // Validate surfaces if ps_surface field exists.
    if ($division->hasField('surfaces')) {
      foreach ($division->get('surfaces') as $delta => $surface) {
        /** @var \Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem $surface */
        $value = $surface->getValue();
        if ($value !== NULL && $value < 0) {
          $errors[] = "Surface #{$delta}: value cannot be negative.";
        }

        $unit = $surface->getUnit();
        if ($unit && !$this->dictionaryManager->isValid('surface_unit', $unit)) {
          $errors[] = "Surface #{$delta}: invalid unit '{$unit}'.";
        }

        $type = $surface->getType();
        if ($type && !$this->dictionaryManager->isValid('surface_type', $type)) {
          $errors[] = "Surface #{$delta}: invalid type '{$type}'.";
        }

        $nature = $surface->getNature();
        if ($nature && !$this->dictionaryManager->isValid('surface_nature', $nature)) {
          $errors[] = "Surface #{$delta}: invalid nature '{$nature}'.";
        }

        $qual = $surface->getQualification();
        if ($qual && !$this->dictionaryManager->isValid('surface_qualification', $qual)) {
          $errors[] = "Surface #{$delta}: invalid qualification '{$qual}'.";
        }
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(DivisionInterface $division): array {
    $type = NULL;
    if ($division->hasField('division_type') && !$division->get('division_type')->isEmpty()) {
      $type = $division->get('division_type')->value;
    }

    $nature = NULL;
    if ($division->hasField('nature') && !$division->get('nature')->isEmpty()) {
      $nature = $division->get('nature')->value;
    }

    return [
      'id' => $division->id(),
      'building_name' => $division->getBuildingName(),
      'type' => $type,
      'nature' => $nature,
      'lot' => $division->getLot(),
      'total_surface' => $division->getTotalSurface(),
    ];
  }

}
