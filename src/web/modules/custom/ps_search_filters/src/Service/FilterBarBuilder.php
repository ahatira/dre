<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_context\Service\SearchBudgetFilterResolver;
use Drupal\ps_context\Service\SearchFilterVisibilityResolver;
use Drupal\ps_dictionary\Service\DictionaryEntryIconResolver;
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

    $queryAll = $request?->query->all() ?? [];

    $activeOp = NULL;
    $activeAsset = NULL;
    if (!empty($segments[0]) && isset($opBySlug[$segments[0]])) {
      $activeOp = $opBySlug[$segments[0]];
      if (!empty($segments[1]) && isset($assetBySlug[$segments[1]])) {
        $activeAsset = $assetBySlug[$segments[1]];
      }
    }

    // Path processor injects query params on SEO URLs (not visible in the address bar).
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
    $localityRaw = $queryAll['locality'] ?? NULL;
    if (is_array($localityRaw)) {
      $initialLocality = implode(', ', array_values(array_filter(array_map('strval', $localityRaw))));
    }
    elseif (is_string($localityRaw) && $localityRaw !== '') {
      $initialLocality = $localityRaw;
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
      '#budget_config' => $budgetConfig,
      '#lang_prefix' => $langPrefix,
      '#show_surface_filter' => $initialVisibility['show_surface'],
      '#show_capacity_filter' => $initialVisibility['show_capacity'],
      '#capacity_filter_label' => $capacityFilterLabel,
      '#attached' => [
        'library' => ['ps_search_filters/filter_bar'],
        'drupalSettings' => [
          'psSearch' => [
            'langPrefix' => $langPrefix,
            'searchPath' => $searchPath,
            'opSlugs' => $opSlugs,
            'assetSlugs' => $assetSlugs,
            'activeOp' => $activeOp,
            'activeFlexible' => $activeOp === NULL,
            'activeAsset' => $activeAsset,
            'initialLocality' => $initialLocality,
            'countUrl' => '/ps-search/count',
            'locationSuggestUrl' => '/ps-search/location-suggest',
            'locationDataUrl' => '/ps-search/location-data',
            'filterVisibilityByAsset' => $visibilityByAsset,
            'capacityFilterLabel' => $capacityFilterLabel,
            'capacityUnit' => $capacityUnit,
            'budgetFilterConfig' => $budgetConfig,
            'budgetFilterByAsset' => $budgetFilterByAsset,
          ],
        ],
      ],
      '#cache' => [
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
      ],
    ];
  }

}
