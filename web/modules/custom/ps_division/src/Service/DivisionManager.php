<?php

declare(strict_types=1);

namespace Drupal\ps_division\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_division\Entity\DivisionInterface;
use Drupal\ps_surface\Service\SurfaceValidatorInterface;

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
    private readonly SurfaceValidatorInterface $surfaceValidator,
    private readonly ConfigFactoryInterface $configFactory,
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

    // Validate division-level dictionary codes.
    if ($division->hasField('floor') && !$division->get('floor')->isEmpty()) {
      $floor = (string) $division->get('floor')->value;
      if (!$this->dictionaryManager->isValid('floor', $floor)) {
        $errors[] = "Invalid floor '{$floor}'.";
      }
    }

    $settings = $this->configFactory->get('ps_division.settings');
    $typeDictionary = (string) ($settings->get('dictionaries.division_type') ?? 'surface_type');
    if ($division->hasField('division_type') && !$division->get('division_type')->isEmpty()) {
      $type = (string) $division->get('division_type')->value;
      if (!$this->dictionaryManager->isValid($typeDictionary, $type)) {
        $errors[] = "Invalid division type '{$type}' for dictionary '{$typeDictionary}'.";
      }
    }

    $natureDictionary = (string) ($settings->get('dictionaries.division_nature') ?? 'surface_nature');
    if ($division->hasField('nature') && !$division->get('nature')->isEmpty()) {
      $nature = (string) $division->get('nature')->value;
      if (!$this->dictionaryManager->isValid($natureDictionary, $nature)) {
        $errors[] = "Invalid division nature '{$nature}' for dictionary '{$natureDictionary}'.";
      }
    }

    // Delegate surface validation to ps_surface.
    if ($division->hasField('surfaces')) {
      foreach ($division->get('surfaces') as $delta => $surface) {
        /** @var \Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem $surface */
        foreach ($this->surfaceValidator->validateItem($surface) as $surfaceError) {
          $errors[] = "Surface #{$delta}: {$surfaceError}";
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
