<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;

/**
 * Provides operation and asset select options for homepage preset builders.
 */
final class SearchPresetOptionsProvider {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * @return array<string, string>
   *   Operation type options keyed by machine code.
   */
  public function getOperationOptions(?string $langcode = NULL): array {
    $options = ['' => (string) $this->t('- Any transaction -')];
    foreach ($this->loadFacetLabels('operation_type', $langcode) as $code => $label) {
      $options[$code] = $label;
    }
    return $options;
  }

  /**
   * @return array<string, string>
   *   Asset type options keyed by machine code.
   */
  public function getAssetOptions(?string $langcode = NULL): array {
    $options = ['' => (string) $this->t('- Any asset type -')];
    foreach ($this->loadFacetLabels('asset_type', $langcode) as $code => $label) {
      $options[$code] = $label;
    }
    return $options;
  }

  /**
   * @return array<string, string>
   *   Facet labels keyed by machine code.
   */
  private function loadFacetLabels(string $facetType, ?string $langcode): array {
    $langcode ??= $this->languageManager->getDefaultLanguage()->getId();

    $base = $this->configFactory->get('ps_search.seo_url_mappings');
    $langOverride = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');
    $slugs = array_merge(
      $base->get($facetType === 'operation_type' ? 'operation_types' : 'asset_types') ?? [],
      $langOverride->get($facetType === 'operation_type' ? 'operation_types' : 'asset_types') ?? [],
    );

    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entries = $storage->loadByProperties(['type' => $facetType]);

    $labels = [];
    foreach ($slugs as $code => $_slug) {
      $entryId = $facetType . '.' . strtolower((string) $code);
      $entry = $entries[$entryId] ?? NULL;
      $labels[(string) $code] = $entry ? $entry->label() : (string) $code;
    }

    return $labels;
  }

}
