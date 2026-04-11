<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Entity\FeatureInterface;
use Psr\Log\LoggerInterface;

/**
 * Feature Manager service.
 *
 * Provides centralized management of feature definitions with caching
 * and filtering capabilities. All feature access should go through this
 * service to ensure consistent behavior and optimal performance.
 *
 * @see \Drupal\ps_features\Service\FeatureManagerInterface
 * @see docs/specs/04-ps-features.md#feature-manager
 */
final class FeatureManager implements FeatureManagerInterface {

  use StringTranslationTrait;

  /**
   * Cache of loaded features.
   *
   * @var array<string, \Drupal\ps_features\Entity\FeatureInterface>|null
   */
  private ?array $features = NULL;

  /**
   * Constructs a FeatureManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface|null $dictionaryManager
   *   The dictionary manager service (optional: used only by dictionary-type features).
   * @param \Drupal\Core\StringTranslation\TranslationInterface|null $stringTranslation
   *   The string translation service.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CacheBackendInterface $cache,
    private readonly LoggerInterface $logger,
    private readonly ?DictionaryManagerInterface $dictionaryManager = NULL,
    ?TranslationInterface $stringTranslation = NULL,
  ) {
    if ($stringTranslation) {
      $this->setStringTranslation($stringTranslation);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFeature(string $feature_id): ?FeatureInterface {
    $features = $this->loadFeatures();
    return $features[$feature_id] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFeatures(array $filters = []): array {
    $features = $this->loadFeatures();

    if (empty($filters)) {
      return $features;
    }

    return array_filter($features, function (FeatureInterface $feature) use ($filters): bool {
      // Filter by value type.
      if (isset($filters['value_type']) && $feature->getValueType() !== $filters['value_type']) {
        return FALSE;
      }

      // Filter by group.
      if (isset($filters['group'])) {
        $featureGroup = $feature->getGroup();
        if ($featureGroup !== $filters['group']) {
          return FALSE;
        }
      }

      // Filter by required status.
      if (isset($filters['required']) && $feature->isRequired() !== $filters['required']) {
        return FALSE;
      }

      return TRUE;
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getFeatureOptions(array $filters = []): array {
    $features = $this->getFeatures($filters);
    $options = [];

    foreach ($features as $id => $feature) {
      $options[$id] = $feature->label();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function featureExists(string $feature_id): bool {
    return $this->getFeature($feature_id) !== NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFeature(string $feature_id): bool {
    return $this->featureExists($feature_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getFeaturesByGroup(): array {
    $features = $this->loadFeatures();
    $grouped = [];

    // Load group labels from feature_group dictionary.
    $groupLabels = [];
    if ($this->dictionaryManager) {
      try {
        $dictGroups = $this->dictionaryManager->getOptions('feature_group');
        if (!empty($dictGroups)) {
          // Dictionary returns UPPERCASE codes, but features store lowercase.
          foreach ($dictGroups as $code => $label) {
            $groupLabels[strtolower($code)] = $label;
          }
        }
      }
      catch (\Throwable $e) {
        // Dictionary not available, will use ungrouped fallback.
      }
    }

    foreach ($features as $id => $feature) {
      $group_id = $feature->getGroup() ?: 'ungrouped';

      if (!isset($grouped[$group_id])) {
        $label = $groupLabels[$group_id] ?? ($group_id === 'ungrouped' ? (string) $this->t('Ungrouped') : $group_id);
        $grouped[$group_id] = [
          'label' => (string) $label,
          'features' => [],
        ];
      }

      $grouped[$group_id]['features'][$id] = $feature;
    }

    // Sort each group by weight.
    foreach ($grouped as $group_id => $group_data) {
      uasort($grouped[$group_id]['features'], function (FeatureInterface $a, FeatureInterface $b): int {
        return $a->getWeight() <=> $b->getWeight();
      });
    }

    return $grouped;
  }

  /**
   * {@inheritdoc}
   */
  public function getValueTypes(): array {
    // Cast TranslatableMarkup to string for PHPStan compliance.
    return [
      'flag' => (string) $this->t('Flag'),
      'yesno' => (string) $this->t('Yes/No'),
      'dictionary' => (string) $this->t('Dictionary'),
      'string' => (string) $this->t('String'),
      'numeric' => (string) $this->t('Numeric'),
      'range' => (string) $this->t('Range'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function clearCache(): void {
    $this->features = NULL;
    $this->cache->delete('ps_features:all');
    $this->logger->info('Feature cache cleared.');
  }

  /**
   * Loads all features with caching.
   *
   * @return array<string, \Drupal\ps_features\Entity\FeatureInterface>
   *   Array of feature entities keyed by feature ID.
   */
  private function loadFeatures(): array {
    if ($this->features !== NULL) {
      return $this->features;
    }

    $cid = 'ps_features:all';
    $cached = $this->cache->get($cid);

    if ($cached !== FALSE) {
      $this->features = $cached->data;
      return $this->features;
    }

    try {
      $storage = $this->entityTypeManager->getStorage('ps_feature');
      /** @var array<string, \Drupal\ps_features\Entity\FeatureInterface> $features */
      $features = $storage->loadMultiple();

      $this->features = $features;
      $this->cache->set($cid, $features, CacheBackendInterface::CACHE_PERMANENT, ['ps_feature_list']);

      return $this->features;
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to load features: @message', ['@message' => $e->getMessage()]);
      return [];
    }
  }

}
