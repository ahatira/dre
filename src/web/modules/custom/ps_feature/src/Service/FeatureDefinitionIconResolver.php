<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Resolves UI Icons for individual feature definitions.
 */
final class FeatureDefinitionIconResolver implements CacheableDependencyInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Builds a render array for a feature icon when configured.
   *
   * @param \Drupal\ps_feature\Entity\FeatureDefinition|string $definition
   *   Feature definition entity or config ID.
   * @param array<string, mixed> $settings
   *   Optional icon display settings (size, color, etc.).
   *
   * @return array<string, mixed>
   *   Render array for the icon, or empty when no icon is set.
   */
  public function buildRenderable(FeatureDefinition|string $definition, array $settings = []): array {
    $parts = $this->resolveParts($definition);
    if ($parts === NULL) {
      return [];
    }

    $icon_settings = $settings + [
      'size' => '16px',
      'alt' => '',
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['feature-default__icon']],
      'icon' => IconDefinition::getRenderable($parts['full_id'], $icon_settings),
      '#cache' => [
        'tags' => $this->getCacheTagsForDefinition($this->resolveDefinitionId($definition)),
      ],
    ];
  }

  /**
   * Resolves icon pack/id parts for a feature definition.
   *
   * @return array{pack: string, id: string, full_id: string}|null
   *   Resolved icon identifiers, or NULL when no icon is configured.
   */
  public function resolveParts(FeatureDefinition|string $definition): ?array {
    $entity = $this->loadDefinition($definition);
    if (!$entity instanceof FeatureDefinition) {
      return NULL;
    }

    $icon = $entity->getIcon();
    if ($icon === '') {
      return NULL;
    }

    $parts = IconIdUtility::splitIconId($icon);
    if ($parts === NULL) {
      return NULL;
    }

    return [
      'pack' => $parts['pack'],
      'id' => $parts['id'],
      'full_id' => $parts['pack'] . ':' . $parts['id'],
    ];
  }

  /**
   * Returns cache tags for a feature definition icon render.
   *
   * @return array<int, string>
   *   Cache tags.
   */
  public function getCacheTagsForDefinition(string $definition_id): array {
    if ($definition_id === '') {
      return $this->getCacheTags();
    }

    return Cache::mergeTags(
      $this->getCacheTags(),
      ["config:ps_feature.feature_definition.$definition_id"],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return ['config:ps_feature.feature_definition.*'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

  /**
   * Loads a feature definition entity.
   */
  private function loadDefinition(FeatureDefinition|string $definition): ?FeatureDefinition {
    if ($definition instanceof FeatureDefinition) {
      return $definition;
    }

    $loaded = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->load($definition);

    return $loaded instanceof FeatureDefinition ? $loaded : NULL;
  }

  /**
   * Resolves the feature definition config ID.
   */
  private function resolveDefinitionId(FeatureDefinition|string $definition): string {
    if ($definition instanceof FeatureDefinition) {
      return $definition->id();
    }

    return $definition;
  }

}
