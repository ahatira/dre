<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Filter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_context\Service\SearchBudgetFilterResolver;
use Drupal\ps_context\Service\SearchFilterVisibilityResolver;
use Drupal\ps_dictionary\Service\DictionaryEntryIconResolver;
use Drupal\ps_search\Api\ApiRoutePaths;
use Drupal\ps_search\Service\SearchPathResolver;
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
    private readonly SearchBudgetFilterResolver $searchBudgetFilter,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly FilterBarHtmxSettings $htmxSettings,
  ) {}

  /**
   * Builds the filter bar render array.
   */
  public function build(): array {
    return $this->buildFilterBarRenderArray($this->resolveFilterData());
  }

  /**
   * Builds the compact homepage hero search panel (BNPPRE entry point).
   *
   * @param array<string, string> $labels
   *   Localized editorial labels from SearchHeroBlock.
   *
   * @return array<string, mixed>
   */
  public function buildHomepageEntryPanel(array $labels = []): array {
    $data = $this->resolveFilterData([
      'active_op' => NULL,
      'active_asset' => NULL,
      'initial_locality' => '',
      'active_flexible' => FALSE,
    ]);

    return [
      '#theme' => 'ps_search_homepage_entry',
      '#operation_types' => $data['operation_types'],
      '#asset_types' => $data['asset_types'],
      '#active_op' => $data['active_op'],
      '#active_flexible' => $data['active_flexible'],
      '#active_asset' => $data['active_asset'],
      '#active_op_label' => $data['active_op_label'],
      '#active_asset_label' => $data['active_asset_label'],
      '#search_path' => $data['search_path'],
      '#budget_config' => $data['budget_config'],
      '#show_surface_filter' => $data['show_surface_filter'],
      '#labels' => $labels,
      '#attached' => [
        'library' => [
          'ps_search/filter.bar',
          'ps_search/homepage.search',
        ],
        'drupalSettings' => [
          'psSearchFilterHtmx' => $this->htmxSettings->buildJsSettings(),
          'psSearch' => $this->buildPsSearchSettings($data),
        ],
      ],
      '#cache' => $this->buildFilterCacheTags(),
    ];
  }

  /**
   * @param array<string, mixed> $data
   *   Resolved filter data from resolveFilterData().
   *
   * @return array<string, mixed>
   */
  private function buildFilterBarRenderArray(array $data): array {
    return [
      '#theme' => 'ps_search_filter_bar',
      '#operation_types' => $data['operation_types'],
      '#asset_types' => $data['asset_types'],
      '#more_criteria_groups' => $data['more_criteria_groups'],
      '#core_criteria_items' => $data['core_criteria_items'],
      '#active_op' => $data['active_op'],
      '#active_flexible' => $data['active_flexible'],
      '#active_asset' => $data['active_asset'],
      '#active_op_label' => $data['active_op_label'],
      '#active_asset_label' => $data['active_asset_label'],
      '#budget_config' => $data['budget_config'],
      '#lang_prefix' => $data['lang_prefix'],
      '#show_surface_filter' => $data['show_surface_filter'],
      '#show_capacity_filter' => $data['show_capacity_filter'],
      '#capacity_filter_label' => $data['capacity_filter_label'],
      '#attached' => [
        'library' => ['ps_search/filter.bar'],
        'drupalSettings' => [
          'psSearchFilterHtmx' => $this->htmxSettings->buildJsSettings(),
          'psSearch' => $this->buildPsSearchSettings($data),
        ],
      ],
      '#cache' => $this->buildFilterCacheTags(),
    ];
  }

  /**
   * @param array<string, mixed> $overrides
   *   Optional keys: active_op, active_asset, initial_locality, active_flexible.
   *
   * @return array<string, mixed>
   */
  private function resolveFilterData(array $overrides = []): array {
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

    if ($overrides !== []) {
      $activeOp = $overrides['active_op'] ?? NULL;
      $activeAsset = $overrides['active_asset'] ?? NULL;
      $initialLocality = (string) ($overrides['initial_locality'] ?? '');
      $activeFlexible = (bool) ($overrides['active_flexible'] ?? ($activeOp === NULL));
    }
    else {
      $pathInfo = $request?->getPathInfo() ?? '';
      if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
        $stripped = substr($pathInfo, strlen($langPrefix));
      }
      else {
        $stripped = $pathInfo;
      }
      $segments = array_values(array_filter(explode('/', $stripped)));

      $queryAll = $request?->query->all() ?? [];

      $activeOp = NULL;
      $activeAsset = NULL;
      if ($segments !== []) {
        $facets = $this->searchPathResolver->resolveFacetsFromPathSegments($langcode, $segments);
        $activeOp = $facets['operation_type'];
        $activeAsset = $facets['asset_type'];
      }

      if (!$activeOp) {
        $rawOp = $queryAll['operation_type'] ?? NULL;
        $activeOp = is_array($rawOp) ? array_key_first($rawOp) : $rawOp;
        $activeOp = is_string($activeOp) && $activeOp !== '' ? strtoupper($activeOp) : NULL;
      }
      if (!$activeAsset) {
        $rawAsset = $queryAll['asset_type'] ?? NULL;
        $activeAsset = is_array($rawAsset) ? array_key_first($rawAsset) : $rawAsset;
        $activeAsset = is_string($activeAsset) && $activeAsset !== '' ? strtoupper($activeAsset) : NULL;
      }

      $initialLocality = '';
      $localityRaw = $queryAll['locations'] ?? $queryAll['locality'] ?? NULL;
      if (is_array($localityRaw)) {
        $initialLocality = implode(', ', array_values(array_filter(array_map('strval', $localityRaw))));
      }
      elseif (is_string($localityRaw) && $localityRaw !== '') {
        $initialLocality = $localityRaw;
      }

      $activeFlexible = $activeOp === NULL;
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

    $assetCodes = array_keys($assetSlugs);
    $visibilityByAsset = $this->searchFilterVisibility->buildVisibilityMap($assetCodes);
    $initialVisibility = $this->searchFilterVisibility->resolve($activeAsset);
    $budgetConfig = $this->searchBudgetFilter->resolve($activeAsset, $activeOp);
    $budgetFilterByAsset = $this->searchBudgetFilter->buildConfigMap($assetCodes);

    $offerSettings = $this->configFactory->get('ps_offer.settings');
    $capacityUnit = (string) ($offerSettings->get('surface_capacity_unit') ?: 'seats');
    $capacityFilterLabel = ucfirst($capacityUnit);

    $urlLangcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $searchPath = $this->searchPathResolver->getPublicPath($urlLangcode);

    $summaries = $this->moreCriteriaBuilder->buildSummaries($activeAsset);
    $coreItems = $summaries['core']['items'] ?? [];
    unset($summaries['core']);
    $moreFilterSchema = $this->moreCriteriaBuilder->buildFilterSchema($activeAsset);

    return [
      'operation_types' => $operationTypes,
      'asset_types' => $assetTypes,
      'more_criteria_groups' => $summaries,
      'core_criteria_items' => $coreItems,
      'active_op' => $activeOp,
      'active_flexible' => $activeFlexible,
      'active_asset' => $activeAsset,
      'active_op_label' => $activeOpLabel,
      'active_asset_label' => $activeAssetLabel,
      'budget_config' => $budgetConfig,
      'lang_prefix' => $langPrefix,
      'show_surface_filter' => $initialVisibility['show_surface'],
      'show_capacity_filter' => $initialVisibility['show_capacity'],
      'capacity_filter_label' => $capacityFilterLabel,
      'search_path' => $searchPath,
      'op_slugs' => $opSlugs,
      'asset_slugs' => $assetSlugs,
      'initial_locality' => $initialLocality,
      'visibility_by_asset' => $visibilityByAsset,
      'capacity_unit' => $capacityUnit,
      'budget_filter_by_asset' => $budgetFilterByAsset,
      'more_filter_schema' => $moreFilterSchema,
    ];
  }

  /**
   * @param array<string, mixed> $data
   *   Resolved filter data.
   *
   * @return array<string, mixed>
   */
  private function buildPsSearchSettings(array $data): array {
    return [
      'apiBase' => ApiRoutePaths::BASE,
      'langPrefix' => $data['lang_prefix'],
      'searchPath' => $data['search_path'],
      'opSlugs' => $data['op_slugs'],
      'assetSlugs' => $data['asset_slugs'],
      'activeOp' => $data['active_op'],
      'activeFlexible' => $data['active_flexible'],
      'activeAsset' => $data['active_asset'],
      'initialLocality' => $data['initial_locality'],
      'locationSuggestUrl' => ApiRoutePaths::LOCATION_SUGGEST,
      'locationDataUrl' => ApiRoutePaths::LOCATION_DATA,
      'filterVisibilityByAsset' => $data['visibility_by_asset'],
      'capacityFilterLabel' => $data['capacity_filter_label'],
      'capacityUnit' => $data['capacity_unit'],
      'budgetFilterConfig' => $data['budget_config'],
      'budgetFilterByAsset' => $data['budget_filter_by_asset'],
      'moreFilterSchema' => $data['more_filter_schema'],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function buildFilterCacheTags(): array {
    return [
      'contexts' => [
        'url.path',
        'url.query_args:locality',
        'url.query_args:operation_type',
        'url.query_args:asset_type',
        'languages:language_interface',
      ],
      'tags' => [
        'config:ps_search.seo_url_mappings',
        'config:ps_offer.settings',
        'ps_context_rule_list',
        'fb_feature_definition_list',
        'config:ps_dictionary.entry.*',
      ],
    ];
  }

  /**
   * Builds the mobile toolbar (Back, Show map, See all filters).
   */
  public function buildMobileActions(): array {
    return [
      '#theme' => 'ps_search_filter_bar_mobile_actions',
      '#cache' => [
        'contexts' => [
          'url.path',
          'languages:language_interface',
        ],
      ],
    ];
  }

}
