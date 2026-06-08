<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
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
      $iconClass = 'ps-asset-icon--' . strtolower($code);
      $assetTypes[$code] = [
        'code' => $code,
        'slug' => $slug,
        'label' => $label,
        'icon_class' => $iconClass,
        'active' => $code === $activeAsset,
      ];
    }

    $activeOpLabel = $activeOp ? ($operationTypes[$activeOp]['label'] ?? $activeOp) : NULL;
    $activeAssetLabel = $activeAsset ? ($assetTypes[$activeAsset]['label'] ?? $activeAsset) : NULL;

    $budgetHeading = $activeOp === 'VEN'
      ? (string) $this->t('Price (€)')
      : (string) $this->t('Rent (€/m²/year)');

    return [
      '#theme' => 'ps_search_filter_bar',
      '#operation_types' => $operationTypes,
      '#asset_types' => $assetTypes,
      '#more_criteria' => $this->moreCriteriaBuilder->build(),
      '#active_op' => $activeOp,
      '#active_asset' => $activeAsset,
      '#active_op_label' => $activeOpLabel,
      '#active_asset_label' => $activeAssetLabel,
      '#budget_heading' => $budgetHeading,
      '#lang_prefix' => $langPrefix,
      '#attached' => [
        'library' => ['ps_search_filters/filter_bar'],
        'drupalSettings' => [
          'psSearch' => [
            'langPrefix' => $langPrefix,
            'opSlugs' => $opSlugs,
            'assetSlugs' => $assetSlugs,
            'activeOp' => $activeOp,
            'activeAsset' => $activeAsset,
            'countUrl' => '/ps-search/count',
            'locationSuggestUrl' => '/ps-search/location-suggest',
            'locationDataUrl' => '/ps-search/location-data',
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['url.path', 'languages:language_interface'],
        'tags' => ['config:ps_search.seo_url_mappings', 'fb_feature_definition_list'],
      ],
    ];
  }

}
