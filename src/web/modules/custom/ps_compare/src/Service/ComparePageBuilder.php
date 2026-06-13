<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_compare\CompareRenderContext;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Drupal\ps_favorite\Service\FavoriteLazyBuilder;

/**
 * Builds the property comparison table page.
 */
final class ComparePageBuilder {

  use StringTranslationTrait;

  private const VIEW_MODE = 'compare';

  /**
   * Rows always rendered in the comparison table even when all cells are empty.
   */
  private const ALWAYS_VISIBLE_ROW_IDS = [
    'price',
  ];

  /**
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface|null
   */
  private ?EntityViewBuilderInterface $nodeViewBuilder = NULL;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface|null
   */
  private ?EntityViewDisplayInterface $compareDisplay = NULL;

  private string $renderContext = CompareRenderContext::PAGE;

  private bool $sharedView = FALSE;

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly CompareLazyBuilder $lazyBuilder,
    private readonly CompareOfferSummaryBuilder $offerSummaryBuilder,
    private readonly CompareShareResolver $shareResolver,
    private readonly CompareEmailImageEncoder $emailImageEncoder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityDisplayRepositoryInterface $entityDisplayRepository,
    private readonly RendererInterface $renderer,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FeatureTypeManager $featureTypeManager,
    private readonly CompareDisplaySettings $displaySettings,
    private readonly CompareTableEnhancer $tableEnhancer,
    private readonly FavoriteLazyBuilder $favoriteLazyBuilder,
  ) {}

  /**
   * Builds the compare page render array.
   *
   * @param \Drupal\node\NodeInterface[]|null $offersOverride
   *   Explicit offer list (shared link or email). When NULL, uses compare manager.
   * @param array{shared_view?: bool, share_url?: string|null} $options
   *
   * @return array<string, mixed>
   */
  public function buildPage(
    string $context = CompareRenderContext::PAGE,
    ?array $offersOverride = NULL,
    array $options = [],
  ): array {
    $this->renderContext = $context;
    $this->sharedView = (bool) ($options['shared_view'] ?? FALSE);

    $offers = $offersOverride ?? array_values(array_filter(
      $this->compareManager->getCompareList('node'),
      static fn (EntityInterface $entity): bool => $entity instanceof NodeInterface && $entity->bundle() === 'offer',
    ));

    $display = $this->displaySettings->forContext($context);
    $shareUrl = $options['share_url'] ?? $this->shareResolver->buildUrlFromOffers($offers, $context === CompareRenderContext::EMAIL);
    $columns = [];
    foreach ($offers as $offer) {
      assert($offer instanceof NodeInterface);
      $summary = $this->offerSummaryBuilder->build($offer);
      $columns[] = [
        'entity_id' => (int) $offer->id(),
        'title' => $summary['title'] ?? $offer->label(),
        'url' => $offer->toUrl()->toString(),
        'thumbnail' => $summary['thumbnail'] ?? NULL,
        'header' => $this->buildColumnHeader($offer, $summary),
      ];
    }

    $sections = array_merge($this->buildStaticSections($offers), $this->buildFeatureSections($offers));
    $sections = array_values(array_filter($sections, static fn (array $section): bool => ($section['rows'] ?? []) !== []));

    $enhanced = $this->tableEnhancer->enhance($sections);
    $sections = $enhanced['sections'];

    $build = [
      '#theme' => $context === CompareRenderContext::EMAIL ? 'ps_compare_table_email' : 'ps_compare_table',
      '#title' => $this->t('Compare properties'),
      '#context' => $context,
      '#columns' => $columns,
      '#sections' => $sections,
      '#photos_label' => $this->t('Photos'),
      '#display' => $display,
      '#toolbar' => $this->buildToolbar($display),
      '#compare_url' => $shareUrl,
      '#cache' => [
        'tags' => ['config:ps_compare.settings'],
        'max-age' => 0,
      ],
    ];

    if ($this->sharedView) {
      $build['#cache']['contexts'] = ['url.query_args:refs', 'url.query_args:ref'];
      $build['#cache']['tags'] = array_merge(
        $build['#cache']['tags'],
        array_map(static fn (NodeInterface $offer): string => 'node:' . $offer->id(), $offers),
      );
    }
    else {
      $build['#cache']['contexts'] = ['user'];
      $build['#cache']['tags'] = array_merge($build['#cache']['tags'], ['ps_compare:list', 'ps_compare:count']);
    }

    if ($context !== CompareRenderContext::EMAIL) {
      $build['#attached'] = [
        'library' => ['ps_compare/compare-page'],
        'drupalSettings' => [
          'psComparePage' => [
            'context' => $context,
            'collapsibleSections' => $display['collapsible_sections'],
            'shareOffcanvasEndpoint' => Url::fromRoute('ps_compare.share_offcanvas')->toString(),
            'minItems' => $this->compareManager->getMinItems(),
            'undoRemoval' => $this->displaySettings->undoRemoval(),
          ],
        ],
      ];
    }

    return $build;
  }

  /**
   * @param array<string, bool> $display
   *
   * @return array<string, mixed>|null
   */
  private function buildToolbar(array $display): ?array {
    if ($this->renderContext === CompareRenderContext::EMAIL || $this->sharedView) {
      return NULL;
    }

    $showShare = $display['share_button'] && $this->renderContext !== CompareRenderContext::MODAL;
    $showFullPage = $this->renderContext === CompareRenderContext::MODAL;
    if (!$showShare && !$showFullPage) {
      return NULL;
    }

    return [
      '#theme' => 'ps_compare_toolbar',
      '#context' => $this->renderContext,
      '#share_enabled' => $showShare,
      '#compare_url' => $this->shareResolver->buildUrlFromOffers(
        array_values(array_filter(
          $this->compareManager->getCompareList('node'),
          static fn (EntityInterface $entity): bool => $entity instanceof NodeInterface && $entity->bundle() === 'offer',
        )),
      ),
    ];
  }

  /**
   * @param \Drupal\node\NodeInterface[] $offers
   *
   * @return array<int, array<string, mixed>>
   */
  private function buildStaticSections(array $offers): array {
    $staticDefinitions = $this->getStaticRowDefinitions();
    $sections = [];

    foreach ($this->getStaticSectionDefinitions() as $sectionId => $sectionDefinition) {
      $rows = [];
      foreach ($sectionDefinition['row_ids'] as $rowId) {
        if (!isset($staticDefinitions[$rowId])) {
          continue;
        }
        $definition = $staticDefinitions[$rowId];
        $cells = [];
        foreach ($offers as $offer) {
          assert($offer instanceof NodeInterface);
          $cells[] = $this->wrapCell($this->buildStaticRowCell($offer, $definition));
        }
        if ($this->areAllCellsEmpty($cells) && !in_array($rowId, self::ALWAYS_VISIBLE_ROW_IDS, TRUE)) {
          continue;
        }
        $rows[] = [
          'id' => $rowId,
          'label' => $definition['label'],
          'cells' => $cells,
        ];
      }

      if ($rows !== []) {
        $sections[] = [
          'id' => $sectionId,
          'label' => $sectionDefinition['label'],
          'rows' => $rows,
        ];
      }
    }

    return $sections;
  }

  /**
   * @param \Drupal\node\NodeInterface[] $offers
   *
   * @return array<int, array<string, mixed>>
   */
  private function buildFeatureSections(array $offers): array {
    $definitions = $this->resolveFeatureDefinitionsForOffers($offers);
    if ($definitions === []) {
      return [];
    }

    $byGroup = [];
    foreach ($definitions as $definition) {
      $groupId = $definition->getGroup() ?: '_other';
      $byGroup[$groupId][] = $definition;
    }

    $groupStorage = $this->entityTypeManager->getStorage('fb_feature_group');
    $groups = $groupStorage->loadMultiple(array_filter(array_keys($byGroup), static fn (string $id): bool => $id !== '_other'));
    uasort($groups, static function ($a, $b): int {
      $aWeight = method_exists($a, 'getWeight') ? $a->getWeight() : 0;
      $bWeight = method_exists($b, 'getWeight') ? $b->getWeight() : 0;
      if ($aWeight !== $bWeight) {
        return $aWeight <=> $bWeight;
      }
      return strcasecmp((string) $a->label(), (string) $b->label());
    });

    $orderedGroupIds = array_keys($groups);
    foreach (array_keys($byGroup) as $groupId) {
      if (!in_array($groupId, $orderedGroupIds, TRUE)) {
        $orderedGroupIds[] = $groupId;
      }
    }

    $sections = [];
    foreach ($orderedGroupIds as $groupId) {
      if (empty($byGroup[$groupId])) {
        continue;
      }

      $groupDefinitions = $byGroup[$groupId];
      usort($groupDefinitions, static fn (FeatureDefinition $a, FeatureDefinition $b): int => $a->getWeight() <=> $b->getWeight());

      $rows = [];
      foreach ($groupDefinitions as $definition) {
        $cells = [];
        foreach ($offers as $offer) {
          assert($offer instanceof NodeInterface);
          $cells[] = $this->wrapCell($this->buildFeatureCell($offer, $definition));
        }
        if ($this->areAllCellsEmpty($cells)) {
          continue;
        }
        $rows[] = [
          'id' => 'feature_' . $definition->id(),
          'label' => $definition->label(),
          'cells' => $cells,
        ];
      }

      if ($rows === []) {
        continue;
      }

      $label = $groupId === '_other'
        ? $this->t('Other features')
        : ($groups[$groupId]?->label() ?? $groupId);

      $sections[] = [
        'id' => 'group_' . $groupId,
        'label' => $label,
        'rows' => $rows,
      ];
    }

    return $sections;
  }

  /**
   * @param \Drupal\node\NodeInterface[] $offers
   *
   * @return \Drupal\ps_feature\Entity\FeatureDefinition[]
   */
  private function resolveFeatureDefinitionsForOffers(array $offers): array {
    $configuredIds = $this->configFactory->get('ps_compare.settings')->get('compare_feature_codes') ?? [];
    $configuredIds = array_values(array_filter(array_map('strval', $configuredIds)));

    /** @var array<string, \Drupal\ps_feature\Entity\FeatureDefinition> $definitions */
    $definitions = [];

    foreach ($offers as $offer) {
      assert($offer instanceof NodeInterface);
      if (!$offer->hasField('field_features') || $offer->get('field_features')->isEmpty()) {
        continue;
      }

      $assetType = $offer->hasField('field_asset_type') && !$offer->get('field_asset_type')->isEmpty()
        ? (string) $offer->get('field_asset_type')->value
        : '';

      foreach ($offer->get('field_features') as $item) {
        $featureDefinition = $item->getFeatureDefinition();
        if ($featureDefinition === NULL || !$featureDefinition->status()) {
          continue;
        }

        if ($assetType !== '' && !$featureDefinition->isApplicableToAssetType($assetType)) {
          continue;
        }

        if ($featureDefinition->getTypeDriver() === 'flag') {
          $payload = $item->getPayloadArray();
          if (!$this->isFlagPresent($payload)) {
            continue;
          }
        }

        if ($configuredIds !== []
          && !in_array($featureDefinition->id(), $configuredIds, TRUE)
          && !in_array($featureDefinition->getCode(), $configuredIds, TRUE)) {
          continue;
        }

        $definitions[$featureDefinition->id()] = $featureDefinition;
      }
    }

    $definitions = array_values($definitions);
    usort($definitions, static fn (FeatureDefinition $a, FeatureDefinition $b): int => $a->getWeight() <=> $b->getWeight());

    return $definitions;
  }

  /**
   * @return array<string, mixed>
   */
  private function buildGalleryCarouselCell(NodeInterface $offer): array {
    if ($this->renderContext === CompareRenderContext::EMAIL) {
      return $this->buildEmailPhotoCell($offer);
    }

    $summary = $this->offerSummaryBuilder->build($offer);
    $urls = $summary['gallery_urls'] ?? [];
    if ($urls === []) {
      $placeholder = $summary['thumbnail'] ?? NULL;
      if ($placeholder === NULL) {
        return $this->emptyCell();
      }
      $urls = [$placeholder];
    }

    $title = trim((string) ($summary['title'] ?? $offer->label() ?? ''));

    return [
      '#theme' => 'ps_compare_gallery_carousel',
      '#images' => $urls,
      '#entity_id' => (int) $offer->id(),
      '#alt' => $title,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function buildEmailPhotoCell(NodeInterface $offer): array {
    $images = $this->emailImageEncoder->encodeOfferGallery($offer, 1);
    if ($images === []) {
      return $this->emptyCell();
    }

    $summary = $this->offerSummaryBuilder->build($offer);
    $alt = htmlspecialchars(trim((string) ($summary['title'] ?? $offer->label() ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    return [
      '#markup' => Markup::create(sprintf(
        '<img src="%s" alt="%s" width="320" height="220" style="display:block;width:320px;max-width:100%%;height:auto;border:0;">',
        $images[0],
        $alt,
      )),
    ];
  }

  private function buildColumnHeader(NodeInterface $offer, ?array $summary = NULL): array {
    $summary ??= $this->offerSummaryBuilder->build($offer);
    $title = trim((string) ($summary['title'] ?? $offer->label() ?? ''));

    if ($this->renderContext === CompareRenderContext::EMAIL) {
      $surface = trim((string) ($summary['surface'] ?? ''));
      $location = trim((string) ($summary['location'] ?? ''));
      $priceAmount = trim((string) ($summary['price_amount'] ?? ''));
      $priceQualifiers = trim((string) ($summary['price_qualifiers'] ?? ''));
      $reference = NULL;
      if ($offer->hasField('field_reference') && !$offer->get('field_reference')->isEmpty()) {
        $reference = trim((string) $offer->get('field_reference')->value);
      }

      return $this->buildEmailColumnHeader($offer, $title, $surface, $location, $priceAmount, $priceQualifiers, $reference);
    }

    $showActions = !$this->sharedView && $this->renderContext !== CompareRenderContext::EMAIL;
    $address = trim((string) ($summary['location'] ?? ''));
    $gallery = $this->buildGalleryCarouselCell($offer);
    if ($this->isEmptyRenderable($gallery)) {
      $gallery = NULL;
    }

    return [
      '#theme' => 'ps_compare_table_column_header',
      '#title' => $title,
      '#url' => $offer->toUrl()->toString(),
      '#address' => $address,
      '#gallery' => $gallery,
      '#favorite' => $showActions ? $this->favoriteLazyBuilder->buildButtonRenderable($offer, 'search') : NULL,
      '#remove' => $showActions ? $this->lazyBuilder->buildButtonRenderable($offer, 'compare-table') : NULL,
      '#show_actions' => $showActions,
    ];
  }

  /**
   * Whether a render array represents an intentionally empty compare cell.
   */
  private function isEmptyRenderable(array $build): bool {
    return isset($build['#markup']) && trim(strip_tags((string) $build['#markup'])) === '';
  }

  /**
   * @return array<string, mixed>
   */
  private function buildEmailColumnHeader(
    NodeInterface $offer,
    string $title,
    string $surface,
    string $location,
    string $priceAmount,
    string $priceQualifiers,
    ?string $reference,
  ): array {
    $green = '#00915a';
    $build = [
      '#type' => 'container',
      'image' => $this->buildEmailPhotoCell($offer),
    ];

    if ($title !== '') {
      $build['title'] = [
        '#type' => 'link',
        '#title' => $title,
        '#url' => $offer->toUrl('canonical', ['absolute' => TRUE]),
        '#attributes' => [
          'style' => 'color:#333333;text-decoration:none;font-size:16px;font-weight:600;line-height:1.35;display:block;margin:12px 0 6px;',
        ],
      ];
    }

    if ($surface !== '' && !$this->summaryLineIsInTitle($title, $surface)) {
      $build['surface'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $surface,
        '#attributes' => [
          'style' => 'margin:0 0 6px;font-size:14px;line-height:1.4;color:#434f57;',
        ],
      ];
    }

    if ($location !== '') {
      $build['location'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $location,
        '#attributes' => [
          'style' => 'margin:0 0 6px;font-size:13px;line-height:1.4;color:#777e83;',
        ],
      ];
    }

    if ($reference !== NULL && $reference !== '') {
      $build['reference'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => (string) $this->t('Ref. @ref', ['@ref' => $reference]),
        '#attributes' => [
          'style' => 'margin:0 0 8px;font-size:12px;line-height:1.4;color:#777e83;',
        ],
      ];
    }

    if ($priceAmount !== '') {
      $priceLine = $priceAmount . ($priceQualifiers !== '' ? ' ' . $priceQualifiers : '');
      $build['price'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $priceLine,
        '#attributes' => [
          'style' => 'margin:0 0 12px;font-size:15px;font-weight:600;line-height:1.4;color:#333333;',
        ],
      ];
    }

    $build['cta'] = [
      '#type' => 'link',
      '#title' => $this->t('View property'),
      '#url' => $offer->toUrl('canonical', ['absolute' => TRUE]),
      '#attributes' => [
        'style' => 'display:inline-block;padding:10px 16px;background:' . $green . ';color:#ffffff;text-decoration:none;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;',
      ],
    ];

    return $build;
  }

  /**
   * Avoids repeating KPI lines already present in the commercial title.
   */
  private function summaryLineIsInTitle(string $title, string $line): bool {
    $title = trim($title);
    $line = trim($line);
    if ($title === '' || $line === '') {
      return FALSE;
    }

    return mb_stripos($title, $line) !== FALSE;
  }

  /**
   * @return array<string, array{label: \Drupal\Core\StringTranslation\TranslatableMarkup, row_ids: string[]}>
   */
  private function getStaticSectionDefinitions(): array {
    $energyRows = $this->displaySettings->mergeEnergy()
      ? ['energy_combined']
      : ['dpe', 'ges'];

    return [
      'amenagement' => [
        'label' => $this->t('Layout'),
        'row_ids' => ['price', 'surface'],
      ],
      'general' => [
        'label' => $this->t('Overview'),
        'row_ids' => ['operation_type', 'asset_type'],
      ],
      'commercial' => [
        'label' => $this->t('Terms & availability'),
        'row_ids' => ['availability', 'exclusivity', 'divisible'],
      ],
      'energy' => [
        'label' => $this->t('Energy performance'),
        'row_ids' => $energyRows,
      ],
    ];
  }

  /**
   * @return array<string, array{label: \Drupal\Core\StringTranslation\TranslatableMarkup, field?: string, callback?: callable}>
   */
  private function getStaticRowDefinitions(): array {
    return [
      'operation_type' => [
        'label' => $this->t('Transaction type'),
        'field' => 'field_operation_type',
      ],
      'asset_type' => [
        'label' => $this->t('Property type'),
        'field' => 'field_asset_type',
      ],
      'reference' => [
        'label' => $this->t('Reference'),
        'field' => 'field_reference',
      ],
      'surface' => [
        'label' => $this->t('Area'),
        'field' => 'field_surfaces',
      ],
      'price' => [
        'label' => $this->t('Price'),
        'callback' => [$this, 'buildPriceCell'],
      ],
      'availability' => [
        'label' => $this->t('Availability'),
        'field' => 'field_availability',
      ],
      'exclusivity' => [
        'label' => $this->t('Exclusivity'),
        'field' => 'field_mandate_type',
      ],
      'divisible' => [
        'label' => $this->t('Divisible'),
        'callback' => [$this, 'buildDivisibleCell'],
      ],
      'energy_combined' => [
        'label' => $this->t('Energy performance'),
        'callback' => [$this, 'buildEnergyCombinedCell'],
      ],
      'dpe' => [
        'label' => $this->t('DPE'),
        'field' => 'field_diagnostics_dpe',
      ],
      'ges' => [
        'label' => $this->t('GES'),
        'field' => 'field_diagnostics_ges',
      ],
    ];
  }

  /**
   * @param array{label: \Drupal\Core\StringTranslation\TranslatableMarkup, field?: string, callback?: callable} $definition
   *
   * @return array<string, mixed>
   */
  private function buildStaticRowCell(NodeInterface $offer, array $definition): array {
    if (isset($definition['callback']) && is_callable($definition['callback'])) {
      return ($definition['callback'])($offer);
    }

    return $this->renderField($offer, (string) ($definition['field'] ?? ''));
  }

  /**
   * @return array<string, mixed>
   */
  public function buildPriceCell(NodeInterface $offer): array {
    if (!$offer->hasField('field_budget_value')) {
      return $this->emptyCell();
    }

    $display = $this->getCompareDisplay();
    $component = $display->getComponent('field_budget_value');
    if ($component === NULL) {
      return $this->emptyCell();
    }

    if (!$offer->get('field_budget_value')->isEmpty()) {
      $build = $this->getNodeViewBuilder()->viewField($offer->get('field_budget_value'), $component);
      if (!$this->isRenderableEmpty($build)) {
        return $build;
      }
    }

    $onRequest = (string) ($this->configFactory->get('ps_offer.settings')->get('on_request') ?? '');
    if ($onRequest === '') {
      return $this->emptyCell();
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-budget-compare-wrap']],
      'amount' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $onRequest,
        '#attributes' => ['class' => ['ps-offer-budget-compare', 'fw-semibold']],
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  public function buildDivisibleCell(NodeInterface $offer): array {
    if (!$offer->hasField('field_divisible') || $offer->get('field_divisible')->isEmpty()) {
      return $this->emptyCell();
    }

    if (!(bool) $offer->get('field_divisible')->value) {
      return [
        '#markup' => $this->t('No'),
        '#allowed_tags' => [],
      ];
    }

    return [
      '#markup' => $this->t('Yes'),
      '#allowed_tags' => [],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  public function buildEnergyCombinedCell(NodeInterface $offer): array {
    $dpe = $this->renderField($offer, 'field_diagnostics_dpe');
    $ges = $this->renderField($offer, 'field_diagnostics_ges');
    $dpeEmpty = !empty($dpe['#ps_compare_empty']);
    $gesEmpty = !empty($ges['#ps_compare_empty']);

    if ($dpeEmpty && $gesEmpty) {
      return $this->emptyCell();
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-compare-energy-combined', 'd-flex', 'flex-column', 'flex-lg-row', 'gap-2', 'align-items-start']],
      'dpe' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-compare-energy-combined__item']],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $this->t('DPE'),
          '#attributes' => ['class' => ['small', 'text-muted', 'd-block', 'mb-1']],
        ],
        'value' => $dpeEmpty ? $this->emptyCell() : $dpe,
      ],
      'ges' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-compare-energy-combined__item']],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $this->t('GES'),
          '#attributes' => ['class' => ['small', 'text-muted', 'd-block', 'mb-1']],
        ],
        'value' => $gesEmpty ? $this->emptyCell() : $ges,
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function buildFeatureCell(NodeInterface $offer, FeatureDefinition $featureDefinition): array {
    $assetType = '';
    if ($offer->hasField('field_asset_type') && !$offer->get('field_asset_type')->isEmpty()) {
      $assetType = (string) $offer->get('field_asset_type')->value;
    }
    if ($assetType !== '' && !$featureDefinition->isApplicableToAssetType($assetType)) {
      return $this->emptyCell();
    }

    if (!$offer->hasField('field_features') || $offer->get('field_features')->isEmpty()) {
      return $this->emptyCell();
    }

    foreach ($offer->get('field_features') as $item) {
      if ((string) $item->feature_definition_id !== $featureDefinition->id()) {
        continue;
      }

      $payload = $item->getPayloadArray();
      $typeDriver = $featureDefinition->getTypeDriver();

      if ($typeDriver === 'flag') {
        if (!$this->isFlagPresent($payload)) {
          return $this->emptyCell();
        }

        return [
          '#markup' => $this->t('Yes'),
          '#allowed_tags' => [],
        ];
      }

      try {
        $plugin = $this->featureTypeManager->createInstance($typeDriver);
        $formatted = trim((string) $plugin->format($payload));
      }
      catch (\Throwable) {
        return $this->emptyCell();
      }

      if ($formatted === '') {
        return $this->emptyCell();
      }

      return [
        '#markup' => $formatted,
        '#allowed_tags' => ['sup', 'sub', 'br', 'span'],
      ];
    }

    return $this->emptyCell();
  }

  /**
   * @param array<string, mixed> $payload
   */
  private function isFlagPresent(array $payload): bool {
    if (array_key_exists('present', $payload)) {
      return (bool) $payload['present'];
    }
    if (array_key_exists('presence', $payload)) {
      return (bool) $payload['presence'];
    }

    return TRUE;
  }

  /**
   * @return array<string, mixed>
   */
  private function renderField(NodeInterface $offer, string $fieldName): array {
    if ($fieldName === '' || !$offer->hasField($fieldName) || $offer->get($fieldName)->isEmpty()) {
      return $this->emptyCell();
    }

    $display = $this->getCompareDisplay();
    $component = $display->getComponent($fieldName);
    if ($component === NULL) {
      return $this->emptyCell();
    }

    $build = $this->getNodeViewBuilder()->viewField($offer->get($fieldName), $component);
    if ($this->isRenderableEmpty($build)) {
      return $this->emptyCell();
    }

    return $build;
  }

  /**
   * @param array<string, mixed> $build
   */
  private function isRenderableEmpty(array $build): bool {
    $html = trim(strip_tags((string) $this->renderer->renderPlain($build)));
    return $html === '';
  }

  /**
   * @return array<string, mixed>
   */
  private function emptyCell(): array {
    return [
      '#markup' => $this->t('—'),
      '#allowed_tags' => [],
      '#ps_compare_empty' => TRUE,
    ];
  }

  /**
   * @param array<string, mixed> $cell
   *
   * @return array<string, mixed>
   */
  private function wrapCell(array $cell): array {
    if (!empty($cell['#ps_compare_empty'])) {
      return [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-compare-table__cell-content', 'ps-compare-table__cell-content--empty', 'text-muted']],
        'value' => $cell,
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-compare-table__cell-content']],
      'value' => $cell,
    ];
  }

  /**
   * @param array<int, array<string, mixed>> $cells
   */
  private function areAllCellsEmpty(array $cells): bool {
    foreach ($cells as $cell) {
      if (empty($cell['#attributes']['class'])
        || !in_array('ps-compare-table__cell-content--empty', $cell['#attributes']['class'], TRUE)) {
        return FALSE;
      }
    }

    return $cells !== [];
  }

  private function getCompareDisplay(): EntityViewDisplayInterface {
    if ($this->compareDisplay === NULL) {
      $display = $this->entityDisplayRepository->getViewDisplay('node', 'offer', self::VIEW_MODE);
      assert($display instanceof EntityViewDisplayInterface);
      $this->compareDisplay = $display;
    }

    return $this->compareDisplay;
  }

  private function getNodeViewBuilder(): EntityViewBuilderInterface {
    if ($this->nodeViewBuilder === NULL) {
      $this->nodeViewBuilder = $this->entityTypeManager->getViewBuilder('node');
    }

    return $this->nodeViewBuilder;
  }

}
