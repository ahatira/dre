<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the Search Filter Bar block.
 *
 * Renders a BNPPRE-style horizontal filter bar with:
 * - Operation type buttons (rent / sale)
 * - Asset type icon cards
 * Clicking any filter navigates to the appropriate SEO URL.
 */
#[Block(
  id: 'ps_search_filter_bar',
  admin_label: new TranslatableMarkup('Search Filter Bar'),
  category: new TranslatableMarkup('Property Search'),
)]
final class SearchFilterBarBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('language_manager'),
      $container->get('language.config_factory_override'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $request = $this->requestStack->getCurrentRequest();

    // Load SEO URL mappings (base + lang override).
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

    // Reverse maps: slug → code (for active detection).
    $opBySlug = array_flip($opSlugs);
    $assetBySlug = array_flip($assetSlugs);

    // Language URL prefix (empty for default language) — needed before path detection.
    $langPrefix = ($langcode !== $this->languageManager->getDefaultLanguage()->getId())
      ? '/' . $langcode
      : '';

    // Detect active filters from the current URL path.
    $pathInfo = $request->getPathInfo();
    // Strip the known language prefix (e.g. /fr) — only if non-empty.
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

    // Load dictionary entries for human-readable labels.
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface[] $opEntries */
    $opEntries = $storage->loadByProperties(['type' => 'operation_type']);
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface[] $assetEntries */
    $assetEntries = $storage->loadByProperties(['type' => 'asset_type']);

    // Build operation type options.
    $operationTypes = [];
    foreach ($opSlugs as $code => $slug) {
      $entryId = 'operation_type.' . strtolower($code);
      $label = isset($opEntries[$entryId]) ? $opEntries[$entryId]->label() : $code;
      $operationTypes[$code] = [
        'code' => $code,
        'slug' => $slug,
        'label' => $label,
        'url' => $langPrefix . '/' . $slug . '/',
        'active' => $code === $activeOp,
      ];
    }

    // Build asset type options.
    $assetTypes = [];
    foreach ($assetSlugs as $code => $slug) {
      $entryId = 'asset_type.' . strtolower($code);
      $entry = $assetEntries[$entryId] ?? NULL;
      $label = $entry ? $entry->label() : $code;
      $iconClass = ($entry && method_exists($entry, 'getIcon') && $entry->getIcon() !== '')
        ? $entry->getIcon()
        : 'ps-asset-icon--' . strtolower($code);
      // Asset card URL is not a direct href anymore — JS handles navigation.
      $assetTypes[$code] = [
        'code' => $code,
        'slug' => $slug,
        'label' => $label,
        'icon_class' => $iconClass,
        'active' => $code === $activeAsset,
      ];
    }

    // Active label for the main button.
    $activeOpLabel = $activeOp ? ($operationTypes[$activeOp]['label'] ?? $activeOp) : NULL;
    $activeAssetLabel = $activeAsset ? ($assetTypes[$activeAsset]['label'] ?? $activeAsset) : NULL;

    // Clear URL: base search page.
    $clearUrl = $langPrefix . '/recherche';

    return [
      '#theme' => 'ps_search_filter_bar',
      '#operation_types' => $operationTypes,
      '#asset_types' => $assetTypes,
      '#active_op' => $activeOp,
      '#active_asset' => $activeAsset,
      '#active_op_label' => $activeOpLabel,
      '#active_asset_label' => $activeAssetLabel,
      '#lang_prefix' => $langPrefix,
      '#clear_url' => $clearUrl,
      '#attached' => [
        'library' => ['ps_search/filter_bar'],
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
      ],
    ];
  }

}
