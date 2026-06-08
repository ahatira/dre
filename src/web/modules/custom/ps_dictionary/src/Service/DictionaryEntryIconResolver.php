<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;

/**
 * Resolves UI Icons for dictionary entries.
 */
final class DictionaryEntryIconResolver implements CacheableDependencyInterface {

  private const FALLBACK_ICON = 'bnp_custom:not-available';

  /**
   * Legacy CSS class values stored before UI Icons migration.
   *
   * @var array<string, string>
   */
  private const LEGACY_CSS_ICON_MAP = [
    'icon-bureau' => 'bnp_custom:offices',
    'icon-coworking' => 'bnp_custom:coworking',
    'icon-entrepot' => 'bnp_custom:logistic-warehouses',
    'icon-activite' => 'bnp_custom:business-premises',
    'icon-commerce' => 'bnp_custom:shops',
    'icon-terrain' => 'bnp_custom:terrain',
    'ps-asset-icon--bur' => 'bnp_custom:offices',
    'ps-asset-icon--cow' => 'bnp_custom:coworking',
    'ps-asset-icon--ent' => 'bnp_custom:logistic-warehouses',
    'ps-asset-icon--act' => 'bnp_custom:business-premises',
    'ps-asset-icon--com' => 'bnp_custom:shops',
    'ps-asset-icon--ter' => 'bnp_custom:terrain',
    'ps-asset-icon--log' => 'bnp_custom:logistic-warehouses',
  ];

  /**
   * Default icons per asset type business code.
   *
   * @var array<string, string>
   */
  private const DEFAULT_ASSET_TYPE_ICONS = [
    'BUR' => 'bnp_custom:offices',
    'COW' => 'bnp_custom:coworking',
    'ENT' => 'bnp_custom:logistic-warehouses',
    'ACT' => 'bnp_custom:business-premises',
    'COM' => 'bnp_custom:shops',
    'TER' => 'bnp_custom:terrain',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Builds a render array for a dictionary entry icon.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|string|null $entry
   *   Dictionary entry entity, config ID, or NULL when only type/code are known.
   * @param array<string, mixed> $settings
   *   Optional icon display settings (size, color, etc.).
   * @param array<string, mixed> $context
   *   Optional context when $entry is NULL: type, code.
   *
   * @return array<string, mixed>
   *   Render array for the icon.
   */
  public function buildRenderable(DictionaryEntryInterface|string|null $entry, array $settings = [], array $context = []): array {
    $parts = $this->resolveParts($entry, $context);
    $icon_settings = $settings + [
      'size' => '24px',
      'alt' => '',
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-dictionary-entry__icon']],
      'icon' => IconDefinition::getRenderable($parts['full_id'], $icon_settings),
      '#cache' => [
        'tags' => $this->getCacheTagsForEntry($this->resolveEntryId($entry, $context)),
      ],
    ];
  }

  /**
   * Resolves icon pack/id parts for a dictionary entry.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|string|null $entry
   *   Dictionary entry entity, config ID, or NULL.
   * @param array<string, mixed> $context
   *   Optional context when $entry is NULL: type, code.
   *
   * @return array{pack: string, id: string, full_id: string}
   *   Resolved icon identifiers.
   */
  public function resolveParts(DictionaryEntryInterface|string|null $entry, array $context = []): array {
    $entity = $this->loadEntry($entry);
    $stored_icon = $entity instanceof DictionaryEntryInterface ? $entity->getIcon() : '';

    if ($stored_icon !== '') {
      $parts = IconIdUtility::splitIconId($stored_icon);
      if ($parts !== NULL) {
        return [
          'pack' => $parts['pack'],
          'id' => $parts['id'],
          'full_id' => $parts['pack'] . ':' . $parts['id'],
        ];
      }

      $legacy = self::LEGACY_CSS_ICON_MAP[strtolower($stored_icon)] ?? '';
      if ($legacy !== '') {
        $legacyParts = IconIdUtility::splitIconId($legacy);
        if ($legacyParts !== NULL) {
          return [
            'pack' => $legacyParts['pack'],
            'id' => $legacyParts['id'],
            'full_id' => $legacyParts['pack'] . ':' . $legacyParts['id'],
          ];
        }
      }
    }

    $type = $entity instanceof DictionaryEntryInterface ? $entity->getType() : (string) ($context['type'] ?? '');
    $code = $entity instanceof DictionaryEntryInterface ? $entity->getCode() : (string) ($context['code'] ?? '');

    if ($type === 'asset_type') {
      $default = self::DEFAULT_ASSET_TYPE_ICONS[strtoupper($code)] ?? self::FALLBACK_ICON;
      $defaultParts = IconIdUtility::splitIconId($default);
      if ($defaultParts !== NULL) {
        return [
          'pack' => $defaultParts['pack'],
          'id' => $defaultParts['id'],
          'full_id' => $defaultParts['pack'] . ':' . $defaultParts['id'],
        ];
      }
    }

    return IconIdUtility::resolveParts('', 'bnp_custom', 'not-available');
  }

  /**
   * Returns the default UI icon id for a dictionary type/code pair.
   */
  public function getDefaultIconId(string $type, string $code): string {
    if ($type === 'asset_type') {
      return self::DEFAULT_ASSET_TYPE_ICONS[strtoupper($code)] ?? '';
    }

    return '';
  }

  /**
   * Normalizes a stored icon value to a pack:id string.
   */
  public function normalizeStoredIcon(mixed $value, string $fallback = ''): string {
    $stored = IconIdUtility::normalizeStoredIcon($value, '');
    if ($stored === '') {
      return $fallback;
    }

    if (IconIdUtility::splitIconId($stored) !== NULL) {
      return $stored;
    }

    return self::LEGACY_CSS_ICON_MAP[strtolower($stored)] ?: $fallback;
  }

  /**
   * Returns cache tags for a dictionary entry icon render.
   *
   * @return array<int, string>
   *   Cache tags.
   */
  public function getCacheTagsForEntry(string $entry_id): array {
    if ($entry_id === '') {
      return $this->getCacheTags();
    }

    return Cache::mergeTags(
      $this->getCacheTags(),
      ["config:ps_dictionary.entry.$entry_id"],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return ['config:ps_dictionary.entry.*'];
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
   * Loads a dictionary entry entity.
   */
  private function loadEntry(DictionaryEntryInterface|string|null $entry): ?DictionaryEntryInterface {
    if ($entry instanceof DictionaryEntryInterface) {
      return $entry;
    }

    if (!is_string($entry) || $entry === '') {
      return NULL;
    }

    $loaded = $this->entityTypeManager
      ->getStorage('ps_dictionary_entry')
      ->load($entry);

    return $loaded instanceof DictionaryEntryInterface ? $loaded : NULL;
  }

  /**
   * Resolves the dictionary entry config ID.
   */
  private function resolveEntryId(DictionaryEntryInterface|string|null $entry, array $context = []): string {
    if ($entry instanceof DictionaryEntryInterface) {
      return $entry->id();
    }

    if (is_string($entry) && $entry !== '') {
      return $entry;
    }

    $type = (string) ($context['type'] ?? '');
    $code = (string) ($context['code'] ?? '');
    if ($type !== '' && $code !== '') {
      return $type . '.' . strtolower($code);
    }

    return '';
  }

}
