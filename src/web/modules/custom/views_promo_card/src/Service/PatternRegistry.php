<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ComponentPluginManager;

/**
 * Registry of SDC patterns allowed for promo cards.
 */
final class PatternRegistry {

  /**
   * Constructs a PatternRegistry.
   */
  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ComponentPluginManager $componentPluginManager,
  ) {}

  /**
   * Returns allowed pattern IDs.
   *
   * @return list<string>
   */
  public function getAllowedPatternIds(): array {
    $patterns = $this->configFactory->get('views_promo_card.settings')->get('allowed_patterns') ?? [];
    if (!is_array($patterns)) {
      return [];
    }
    return array_values(array_filter($patterns, fn(mixed $id): bool => is_string($id) && $id !== ''));
  }

  /**
   * Returns pattern options for select elements.
   *
   * @return array<string, string>
   */
  public function getPatternOptions(): array {
    $options = [];
    foreach ($this->getAllowedPatternIds() as $pattern_id) {
      $options[$pattern_id] = $this->getPatternLabel($pattern_id);
    }
    asort($options);
    return $options;
  }

  /**
   * Returns a human-readable label for a pattern.
   */
  public function getPatternLabel(string $pattern_id): string {
    try {
      $component = $this->componentPluginManager->find($pattern_id);
      return (string) ($component->getPluginDefinition()['name'] ?? $pattern_id);
    }
    catch (\Exception) {
      return $pattern_id;
    }
  }

  /**
   * Checks whether a pattern ID is allowed and discoverable.
   */
  public function isValidPattern(string $pattern_id): bool {
    if (!in_array($pattern_id, $this->getAllowedPatternIds(), TRUE)) {
      return FALSE;
    }
    return $this->isDiscoverablePattern($pattern_id);
  }

  /**
   * Checks whether an SDC pattern exists, regardless of allow-list settings.
   */
  public function isDiscoverablePattern(string $pattern_id): bool {
    if ($pattern_id === '') {
      return FALSE;
    }
    try {
      $this->componentPluginManager->find($pattern_id);
      return TRUE;
    }
    catch (\Exception) {
      return FALSE;
    }
  }

  /**
   * Returns the attachable library ID for an SDC pattern.
   */
  public function getComponentLibraryId(string $pattern_id): ?string {
    if ($pattern_id === '' || !str_contains($pattern_id, ':')) {
      return NULL;
    }
    if (!$this->isDiscoverablePattern($pattern_id)) {
      return NULL;
    }
    [$extension, $machine_name] = explode(':', $pattern_id, 2);
    return $extension . '/' . $machine_name;
  }

  /**
   * Returns library IDs for all allowed patterns (admin preview styling).
   *
   * @return list<string>
   */
  public function getAllowedPatternLibraries(): array {
    $libraries = [];
    foreach ($this->getAllowedPatternIds() as $pattern_id) {
      $library_id = $this->getComponentLibraryId($pattern_id);
      if ($library_id !== NULL) {
        $libraries[] = $library_id;
      }
    }
    return array_values(array_unique($libraries));
  }

}
