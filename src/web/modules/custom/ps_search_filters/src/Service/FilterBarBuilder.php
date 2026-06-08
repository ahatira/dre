<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_context\Service\SearchFilterVisibilityResolver;
use Drupal\ps_dictionary\Service\DictionaryEntryIconResolver;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds render array data for the search filter bar block.
 */
final class FilterBarBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RequestStack $requestStack,
    private readonly MoreCriteriaBuilder $moreCriteriaBuilder,
    private readonly DictionaryEntryIconResolver $dictionaryEntryIconResolver,
    private readonly SearchFilterVisibilityResolver $searchFilterVisibility,
  ) {}

  /**
   * Builds the filter bar render array.
   */
  public function build(): array {
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $request = $this->requestStack->getCurrentRequest();
    $base = $this->configFactory->get('ps_search.seo_url_mappings');
    $langOverride = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    $opSlugs = array_merge(
      $base->get('operation_types') ?? [],
      $langOverride->get('operation_types') ?? [],
    );
    $assetSlugs = array_merge(
      $base->get('asset_types') ?? [],
      $langOverride->get('asset_types') ?? [],
    );

    $opBySlug = array_flip($opSlugs);
    $assetBySlug = array_flip($assetSlugs);

    $langPrefix = ($langcode !== $this->languageManager->getDefaultLanguage()->getId())
      ? '/' . $langcode
      : '';

    $pathInfo = $request?->getPathInfo() ?? '';
    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $stripped = substr($pathInfo, strlen($langPrefix));
    }
    else {
      $stripped = $pathInfo;
    }
    $segments = array_values(array_filter(explode('/', $stripped)));

    $activeOp = NULL;
    $activeAsset = NULL;
    if (!empty($segments[0]) && isset($opBySlug[$segments[0]])) {
      $activeOp = $opBySlug[$segments[0]];
      if (!empty($segments[1]) && isset($assetBySlug[$segments[1]])) {
        $activeAsset = $assetBySlug[$segments[1]];
      }
    }

    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $opEntries = $storage->loadByProperties(['type' => 'operation_type']);
    $assetEntries = $storage->loadByProperties(['type' => 'asset_type']);

    $operationTypes = [];
    foreach ($opSlugs as $code => $slug) {
      $entryId = 'operation_type.' . strtolower($code);
      $label = isset($opEntries[$entryId]) ? $opEntries[$entryId]->label() : $code;
      $operationTypes[$code] = [
        'code' => $code,
        'slug' => $slug,
        'label' => $label,
        'active' => $code === $activeOp,
      ];
    }

    $assetTypes = [];
    foreach ($assetSlugs as $code => $slug) {
      $entryId = 'asset_type.' . strtolower($code);
      $entry = $assetEntries[$entryId] ?? NULL;
      $label = $entry ? $entry->label() : $code;
      $assetTypes[$code] = [
        'code' => $code,
        'slug' => $slug,
        'label' => $label,
        'weight' => $entry ? $entry->getWeight() : 999,
        'icon' => $this->dictionaryEntryIconResolver->buildRenderable(
          $entry,
          ['size' => '24px'],
          ['type' => 'asset_type', 'code' => $code],
        ),
        'active' => $code === $activeAsset,
      ];
    }

    uasort($assetTypes, static function (array $a, array $b): int {
      return ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0);
    });

    $activeOpLabel = $activeOp ? ($operationTypes[$activeOp]['label'] ?? $activeOp) : NULL;
    $activeAssetLabel = $activeAsset ? ($assetTypes[$activeAsset]['label'] ?? $activeAsset) : NULL;

    $budgetHeading = $activeOp === 'VEN'
      ? (string) $this->t('Price (€)')
      : (string) $this->t('Rent (€/m²/year)');

    $assetCodes = array_keys($assetSlugs);
    $visibilityByAsset = $this->searchFilterVisibility->buildVisibilityMap($assetCodes);
    $initialVisibility = $this->searchFilterVisibility->resolve($activeAsset);

    $offerSettings = $this->configFactory->get('ps_offer.settings');
    $capacityUnit = (string) ($offerSettings->get('surface_capacity_unit') ?: 'seats');
    $capacityFilterLabel = ucfirst($capacityUnit);

    return [
      '#theme' => 'ps_search_filter_bar',
      '#operation_types' => $operationTypes,
      '#asset_types' => $assetTypes,
      '#more_criteria' => $this->moreCriteriaBuilder->build(),
      '#active_op' => $activeOp,
      '#active_flexible' => $activeOp === NULL,
      '#active_asset' => $activeAsset,
      '#active_op_label' => $activeOpLabel,
      '#active_asset_label' => $activeAssetLabel,
      '#budget_heading' => $budgetHeading,
      '#lang_prefix' => $langPrefix,
      '#show_surface_filter' => $initialVisibility['show_surface'],
      '#show_capacity_filter' => $initialVisibility['show_capacity'],
      '#capacity_filter_label' => $capacityFilterLabel,
      '#attached' => [
        'library' => ['ps_search_filters/filter_bar'],
        'drupalSettings' => [
          'psSearch' => [
            'langPrefix' => $langPrefix,
            'opSlugs' => $opSlugs,
            'assetSlugs' => $assetSlugs,
            'activeOp' => $activeOp,
            'activeFlexible' => $activeOp === NULL,
            'activeAsset' => $activeAsset,
            'countUrl' => '/ps-search/count',
            'locationSuggestUrl' => '/ps-search/location-suggest',
            'locationDataUrl' => '/ps-search/location-data',
            'filterVisibilityByAsset' => $visibilityByAsset,
            'capacityFilterLabel' => $capacityFilterLabel,
            'capacityUnit' => $capacityUnit,
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['url.path', 'languages:language_interface'],
        'tags' => [
          'config:ps_search.seo_url_mappings',
          'config:ps_offer.settings',
          'ps_context_rule_list',
          'fb_feature_definition_list',
          'config:ps_dictionary.entry.*',
        ],
      ],
    ];
  }

}
