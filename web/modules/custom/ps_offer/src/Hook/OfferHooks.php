<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Drupal\ps_diagnostic\Entity\PsDiagnosticInterface;
use Drupal\ps_diagnostic\Service\DiagnosticClassCalculatorInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Drupal\ps_offer\Service\OfferReferenceBuilder;

/**
 * Hooks for Offer nodes.
 */
class OfferHooks {

  use StringTranslationTrait;

  /**
   * Constructs the OfferHooks object.
   */
  public function __construct(
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected OfferReferenceBuilder $referenceBuilder,
    protected MessengerInterface $messenger,
    protected AccountProxyInterface $currentUser,
    protected ?EntityTypeManagerInterface $entityTypeManager = NULL,
    protected ?ModuleHandlerInterface $moduleHandler = NULL,
    protected ?DiagnosticClassCalculatorInterface $diagnosticClassCalculator = NULL,
    protected ?FileUrlGeneratorInterface $fileUrlGenerator = NULL,
    protected ?FeatureManagerInterface $featureManager = NULL,
    protected ?DictionaryManagerInterface $dictionaryManager = NULL,
  ) {}

  /**
   * Implements hook_entity_view_alter().
   *
   * Build one normalized offer_full component to avoid raw field-block output
   * when Layout Builder is enabled on the offer default display.
   */
  #[Hook('entity_view_alter')]
  public function entityViewAlter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display): void {
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    if ($display->getMode() === 'card_search') {
      $cache = $build['#cache'] ?? [];
      $attached = $build['#attached'] ?? [];
      $surface = $this->buildSurfaceSummary($entity);
      $location = $this->buildLocationSummary($entity);
      $price = $this->buildCardSearchPriceSummary($entity);
      $mandateRaw = $this->getFieldString($entity, 'field_mandate_type');
      $mandateLabel = $this->resolveDictionaryValue('mandate_type', $mandateRaw);

      $favoriteControl = $this->currentUser->isAuthenticated()
        ? [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['ps-favorites-toggle', 'ps-favorites-toggle--logged'],
          ],
          'link' => [
            '#lazy_builder' => ['flag.link_builder:build', ['node', (string) $entity->id(), 'offer_favorite', $display->getMode()]],
            '#create_placeholder' => TRUE,
          ],
          '#attached' => [
            'library' => ['ps_favorites/favorites'],
          ],
        ]
        : [
          '#theme' => 'ps_favorites_offer_toggle',
          '#nid' => (int) $entity->id(),
          '#attached' => [
            'library' => ['ps_favorites/favorites'],
          ],
        ];

      $slots = [
        'exclusivity_badge' => $mandateLabel !== ''
          ? [
            '#type' => 'inline_template',
            '#template' => '<span class="ps-card-offer-search__badge ps-card-offer-search__badge--exclusive">{{ label }}</span>',
            '#context' => [
              'label' => $mandateLabel,
            ],
          ]
          : NULL,
        'favorite_control' => $favoriteControl,
      ];

      $slots = array_filter($slots, static fn ($slot): bool => $slot !== NULL);

      $build = [
        '#entity_type' => 'node',
        '#node' => $entity,
        '#view_mode' => $display->getMode(),
        '#cache' => $cache,
        '#attached' => $attached,
        'ps_offer_card_search_component' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnppre:card_offer_search',
          '#props' => [
            'attributes' => [
              'data-offer-id' => (string) $entity->id(),
            ],
            'images' => $this->extractCardSearchImages($entity),
            'title' => trim((string) $entity->label()),
            'surface' => $surface ? trim($surface['value'] . ' ' . $surface['unit']) : '',
            'location' => $location,
            'price_prefix' => $price['prefix'],
            'price' => $price['value'],
            'price_unit' => $price['unit'],
            'is_viewed' => FALSE,
            'compare_url' => Url::fromRoute('ps_offer.compare_toggle', ['node' => (int) $entity->id()])->toString(),
            'show_comparator_control' => TRUE,
            'show_favorite_control' => FALSE,
            'cta_url' => $entity->toUrl()->toString(),
            'cta_label' => (string) $this->t('View the property'),
          ],
          '#slots' => $slots,
        ],
      ];

      return;
    }

    if (!in_array($display->getMode(), ['default', 'full'], TRUE)) {
      return;
    }

    $slots = [
      'hero' => $this->buildHeroSlot($entity),
      'meta' => $this->buildMetaSlot($entity, $display),
      'description' => $this->buildDescriptionSlot($entity, $display),
      'features' => $this->buildFeaturesSlot($entity),
      'energy' => $this->buildDiagnosticsSlot($entity),
      'surface' => $this->buildSurfaceSlot($entity),
      'location' => $this->buildLocationSlot($entity),
      'sidebar' => $this->buildSidebarSlot($entity),
      'map' => $this->buildSlotContainer('ps-offer-slot ps-offer-slot--map', [
        $this->buildField($entity, $display, 'field_geofield', ['label' => 'hidden']),
      ]),
    ];

    $slots = array_filter($slots, static fn ($slot): bool => $slot !== NULL);
    if ($slots === []) {
      return;
    }

    $cache = $build['#cache'] ?? [];
    $attached = $build['#attached'] ?? [];

    $build = [
      '#entity_type' => 'node',
      '#node' => $entity,
      '#view_mode' => $display->getMode(),
      '#cache' => $cache,
      '#attached' => $attached,
      'ps_offer_full_component' => [
        '#type' => 'component',
        '#component' => 'ui_suite_bnppre:offer_full',
        '#props' => [
          'modifier_class' => 'layout layout--offer-full-section',
        ],
        '#slots' => $slots,
      ],
    ];
  }

  /**
   * Extracts normalized image URLs for card_offer_search.
   *
   * @return array<int, array<string, string>>
   *   List of image items with `url` and `alt` keys.
   */
  protected function extractCardSearchImages(NodeInterface $node): array {
    if (!$node->hasField('field_media_photos') || $node->get('field_media_photos')->isEmpty()) {
      return [];
    }

    $images = [];
    foreach ($node->get('field_media_photos')->referencedEntities() as $media) {
      if (!$media instanceof EntityInterface) {
        continue;
      }

      $file = NULL;
      if ($media->hasField('field_media_image') && !$media->get('field_media_image')->isEmpty()) {
        $file = $media->get('field_media_image')->entity;
      }
      elseif ($media->hasField('thumbnail') && !$media->get('thumbnail')->isEmpty()) {
        $file = $media->get('thumbnail')->entity;
      }

      if ($file === NULL || !method_exists($file, 'getFileUri')) {
        continue;
      }

      $uri = (string) $file->getFileUri();
      if ($uri === '') {
        continue;
      }

      $images[] = [
        'url' => $this->getFileUrlGenerator()->generateString($uri),
        'alt' => (string) ($media->label() ?: $node->label()),
      ];
    }

    return $images;
  }

  /**
   * Implements hook_node_presave() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_presave
   */
  #[Hook('node_presave')]
  public function nodePresave(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->ensureReference($node);
    $this->ensureMinDivisibleSurface($node);

    $this->loggerFactory->get('ps_offer')->debug(
      'Offer node @id presave triggered with reference @reference',
      [
        '@id' => $node->id() ?? 'new',
        '@reference' => (string) ($node->get('field_reference')->value ?? 'pending'),
      ],
    );
  }

  /**
   * Implements hook_node_insert() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_insert
   */
  #[Hook('node_insert')]
  public function nodeInsert(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id created',
      ['@id' => $node->id()],
    );
  }

  /**
   * Implements hook_node_update() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_update
   */
  #[Hook('node_update')]
  public function nodeUpdate(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id updated',
      ['@id' => $node->id()],
    );
  }

  /**
   * Implements hook_node_delete() for offer nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @hook node_delete
   */
  #[Hook('node_delete')]
  public function nodeDelete(NodeInterface $node): void {
    if ($node->getType() !== 'offer') {
      return;
    }

    $this->loggerFactory->get('ps_offer')->info(
      'Offer node @id deleted',
      ['@id' => $node->id()],
    );
  }

  /**
   * Ensures the offer reference follows the required 12-character format.
   */
  protected function ensureReference(NodeInterface $node): void {
    if (!$node->hasField('field_reference')) {
      return;
    }

    // Once persisted, offer references are immutable. Keep original value even
    // if a submitted form/client attempts to change it.
    if (!$node->isNew() && isset($node->original) && $node->original instanceof NodeInterface && $node->original->hasField('field_reference')) {
      $original_reference = strtoupper(trim((string) $node->original->get('field_reference')->value));
      if ($original_reference !== '') {
        $node->set('field_reference', $original_reference);
        return;
      }
    }

    $reference = strtoupper(trim((string) $node->get('field_reference')->value));
    if ($this->referenceBuilder->isReferenceValid($reference)) {
      $node->set('field_reference', $reference);
      return;
    }

    $generated = $this->referenceBuilder->generate($node);
    $node->set('field_reference', $generated['reference']);

    foreach ($generated['warnings'] as $warning) {
      $this->loggerFactory->get('ps_offer')->warning($warning);
      $this->messenger->addWarning($this->t('@message', ['@message' => $warning]));
    }
  }

  /**
   * Ensures minimum divisible surface remains consistent with offer data.
   */
  protected function ensureMinDivisibleSurface(NodeInterface $node): void {
    if (!$node->hasField('field_min_divisible_surface') || !$node->hasField('field_is_divisible')) {
      return;
    }

    $isDivisible = (bool) ($node->get('field_is_divisible')->value ?? FALSE);
    if (!$isDivisible) {
      $node->set('field_min_divisible_surface', NULL);
      return;
    }

    $existing = $node->get('field_min_divisible_surface')->value;
    if ($existing !== NULL && $existing !== '' && (float) $existing > 0.0) {
      return;
    }

    $computed = $this->resolveMinDivisibleSurfaceFromDivisions($node);
    if ($computed !== NULL) {
      $node->set('field_min_divisible_surface', $computed);
    }
  }

  /**
   * Computes minimum divisible surface from linked division surfaces.
   */
  protected function resolveMinDivisibleSurfaceFromDivisions(NodeInterface $node): ?float {
    if (!$node->hasField('field_divisions') || $node->get('field_divisions')->isEmpty()) {
      return NULL;
    }

    $min = NULL;
    foreach ($node->get('field_divisions') as $divisionReference) {
      $division = $divisionReference->entity;
      if (!$division || !$division->hasField('surfaces')) {
        continue;
      }

      foreach ($division->get('surfaces') as $surface) {
        $value = $surface->get('value')->getValue();
        $unit = strtoupper((string) ($surface->get('unit')->getValue() ?? ''));

        if (!is_numeric($value) || (float) $value <= 0.0) {
          continue;
        }

        // Keep the computation safe by considering explicit m2 values only.
        if ($unit !== '' && $unit !== 'M2') {
          continue;
        }

        $floatValue = (float) $value;
        $min = $min === NULL ? $floatValue : min($min, $floatValue);
      }
    }

    return $min;
  }

  /**
   * Builds hero slot from media references with image-only rendering.
   */
  protected function buildHeroSlot(NodeInterface $node): ?array {
    $slides = [];
    $toolbarItems = [];
    $slideIndex = 0;

    $mediaGroups = [
      'field_media_photos' => [
        'media_type' => 'photos',
        'icon' => 'camera',
        'fallback_icon' => 'camera',
        'label_singular' => '@count photo',
        'label_plural' => '@count photos',
      ],
      'field_media_videos' => [
        'media_type' => 'videos',
        'icon' => 'video',
        'fallback_icon' => 'video',
        'label_singular' => '@count video',
        'label_plural' => '@count videos',
      ],
    ];

    foreach ($mediaGroups as $fieldName => $config) {
      if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
        continue;
      }

      $groupSlideIndex = $slideIndex;
      $groupCount = 0;

      foreach ($node->get($fieldName)->referencedEntities() as $media) {
        $slide = $this->buildMediaSlide($media, $slideIndex, $config['fallback_icon']);
        if ($slide === NULL) {
          continue;
        }

        $slides[] = $slide;
        $groupCount++;
        $slideIndex++;
      }

      if ($groupCount > 0) {
        $toolbarItems[] = [
          'media_type' => $config['media_type'],
          'label' => (string) $this->formatPlural($groupCount, $config['label_singular'], $config['label_plural']),
          'icon' => $config['icon'],
          'slide_index' => $groupSlideIndex,
          'open_mode' => 'media',
        ];
      }
    }

    $virtualTourItems = $this->buildVirtualTourExternalItems($node);
    if ($virtualTourItems !== []) {
      $groupSlideIndex = $slideIndex;
      foreach ($virtualTourItems as $tourItem) {
        $slides[] = $this->buildExternalPlaceholderSlide(
          $tourItem['title'] ?? (string) $this->t('3D visit'),
          'cubefocus',
          $slideIndex,
        );
        $slideIndex++;
      }

      $toolbarItems[] = [
        'media_type' => '3d-visit',
        'label' => (string) $this->formatPlural(
          count($virtualTourItems),
          '@count 3D visit',
          '@count 3D visits',
        ),
        'icon' => 'cubefocus',
        'slide_index' => $groupSlideIndex,
        'open_mode' => 'external',
        'external_items' => $virtualTourItems,
      ];
    }

    // Plans are now displayed after 3D visits.
    if ($node->hasField('field_media_plans') && !$node->get('field_media_plans')->isEmpty()) {
      $groupSlideIndex = $slideIndex;
      $groupCount = 0;

      foreach ($node->get('field_media_plans')->referencedEntities() as $media) {
        $slide = $this->buildMediaSlide($media, $slideIndex, 'cards');
        if ($slide === NULL) {
          continue;
        }

        $slides[] = $slide;
        $groupCount++;
        $slideIndex++;
      }

      if ($groupCount > 0) {
        $toolbarItems[] = [
          'media_type' => 'plans',
          'label' => (string) $this->formatPlural($groupCount, '@count plan', '@count plans'),
          'icon' => 'cards',
          'slide_index' => $groupSlideIndex,
          'open_mode' => 'media',
        ];
      }
    }

    if ($slides === []) {
      return NULL;
    }

    $carousel = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:carousel',
      '#props' => [
        'with_controls' => TRUE,
        'with_indicators' => TRUE,
        'interval' => 0,
        'carousel_id' => 'offer-hero-' . ($node->id() ?? 'new'),
        'toolbar_items' => $toolbarItems,
        'toolbar_modal_enabled' => TRUE,
      ],
      '#slots' => [
        'slides' => $slides,
      ],
    ];

    // Favorite button uses Flag for authenticated users and local storage
    // fallback for anonymous users to keep a persistent UX.
    $favorite = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-offer-hero__favorite-wrapper'],
      ],
      '#attached' => [
        'library' => ['ps_favorites/favorites'],
      ],
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['node:' . $node->id()],
      ],
    ];

    if ($this->currentUser->isAuthenticated()) {
      $favorite['link'] = [
        '#lazy_builder' => ['flag.link_builder:build', ['node', (string) $node->id(), 'offer_favorite', 'full']],
        '#create_placeholder' => TRUE,
      ];
    }
    else {
      $favorite['link'] = [
        '#theme' => 'ps_favorites_offer_toggle',
        '#nid' => (int) $node->id(),
      ];
    }

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--hero', [$carousel, $favorite]);
  }

  /**
   * Builds one hero slide from media with image-first fallback placeholders.
   */
  protected function buildMediaSlide(EntityInterface $media, int $slideIndex, string $fallbackIcon): ?array {
    $image = NULL;
    $mediaBundle = $media->bundle();
    $attributes = ['class' => $slideIndex === 0 ? ['carousel-item', 'active'] : ['carousel-item']];

    // For video media: try cover image first, then thumbnail (oEmbed auto-generated).
    if ($mediaBundle === 'videos' || $mediaBundle === 'remote_video') {
      if ($media->hasField('field_media_cover_image') && !$media->get('field_media_cover_image')->isEmpty()) {
        $image = $media->get('field_media_cover_image')->view([
          'label' => 'hidden',
          'type' => 'image',
        ]);
      }
      elseif ($media->hasField('thumbnail') && !$media->get('thumbnail')->isEmpty()) {
        $image = $media->get('thumbnail')->view([
          'label' => 'hidden',
          'type' => 'image',
        ]);
      }

      // Mark the slide as video for JavaScript handling.
      if ($image !== NULL) {
        $attributes['data-media-type'] = 'video';
        $attributes['data-media-bundle'] = $mediaBundle;

        // Extract and store video URL for modal playback.
        if ($mediaBundle === 'videos' && $media->hasField('field_media_video_file') && !$media->get('field_media_video_file')->isEmpty()) {
          $videoFile = $media->get('field_media_video_file')->first();
          if ($videoFile !== NULL) {
            $videoUrl = $this->getFileUrlGenerator()->generateString($videoFile->entity->getFileUri());
            $attributes['data-video-url'] = $videoUrl;
          }
        }
        elseif ($mediaBundle === 'remote_video' && $media->hasField('field_media_oembed_video') && !$media->get('field_media_oembed_video')->isEmpty()) {
          $oembedUrl = trim((string) $media->get('field_media_oembed_video')->value);
          if ($oembedUrl !== '') {
            $attributes['data-video-url'] = $oembedUrl;
          }
        }
      }
    }

    // For other media types: check field_media_image, then thumbnail.
    if ($image === NULL) {
      if ($media->hasField('field_media_image') && !$media->get('field_media_image')->isEmpty()) {
        $image = $media->get('field_media_image')->view([
          'label' => 'hidden',
          'type' => 'image',
        ]);
      }
      elseif ($media->hasField('thumbnail') && !$media->get('thumbnail')->isEmpty()) {
        $image = $media->get('thumbnail')->view([
          'label' => 'hidden',
          'type' => 'image',
        ]);
      }
    }

    if ($image !== NULL) {
      return [
        '#type' => 'container',
        '#attributes' => $attributes,
        'image' => $image,
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => array_merge(
          $attributes['class'] ?? [],
          ['ps-carousel__slide--placeholder'],
        ),
      ],
      'content' => [
        '#type' => 'inline_template',
        '#template' => '<div class="ps-carousel__external-slide"><span class="ps-carousel__external-slide-icon" data-icon="{{ icon }}" aria-hidden="true"></span><p class="ps-carousel__external-slide-title">{{ title }}</p></div>',
        '#context' => [
          'icon' => $fallbackIcon,
          'title' => (string) $media->label(),
        ],
      ],
    ];
  }

  /**
   * Builds a placeholder slide for external-link groups (3D visits, docs...).
   */
  protected function buildExternalPlaceholderSlide(string $title, string $icon, int $slideIndex): array {
    $slideClasses = $slideIndex === 0 ? ['carousel-item', 'active'] : ['carousel-item'];

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => array_merge($slideClasses, ['ps-carousel__slide--placeholder']),
      ],
      'content' => [
        '#type' => 'inline_template',
        '#template' => '<div class="ps-carousel__external-slide"><span class="ps-carousel__external-slide-icon" data-icon="{{ icon }}" aria-hidden="true"></span><p class="ps-carousel__external-slide-title">{{ title }}</p></div>',
        '#context' => [
          'icon' => $icon,
          'title' => $title,
        ],
      ],
    ];
  }

  /**
   * Extracts normalized external items for virtual tours.
   */
  protected function buildVirtualTourExternalItems(NodeInterface $node): array {
    if (!$node->hasField('field_media_virtual_tours') || $node->get('field_media_virtual_tours')->isEmpty()) {
      return [];
    }

    $items = [];
    foreach ($node->get('field_media_virtual_tours') as $delta => $tourItem) {
      $uri = trim((string) ($tourItem->uri ?? ''));
      if ($uri === '') {
        continue;
      }

      try {
        $resolvedUrl = Url::fromUri($uri)->toString();
      }
      catch (\InvalidArgumentException) {
        continue;
      }

      $scheme = strtolower((string) parse_url($resolvedUrl, PHP_URL_SCHEME));
      if (!in_array($scheme, ['http', 'https'], TRUE)) {
        continue;
      }

      $title = trim((string) ($tourItem->title ?? ''));
      if ($title === '') {
        $title = (string) $this->t('3D visit @number', ['@number' => $delta + 1]);
      }

      $items[] = [
        'url' => $resolvedUrl,
        'title' => $title,
      ];
    }

    return $items;
  }

  /**
   * Checks if a toolbar media type already exists in the toolbar config.
   */
  protected function hasToolbarMediaType(array $toolbarItems, string $mediaType): bool {
    foreach ($toolbarItems as $item) {
      if (($item['media_type'] ?? '') === $mediaType) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Builds meta slot with structured legacy-like props.
   */
  protected function buildMetaSlot(NodeInterface $node, EntityViewDisplayInterface $display): ?array {
    $price = $this->buildField($node, $display, 'field_prices', [
      'label' => 'hidden',
      'type' => 'ps_price_offer',
      'settings' => [
        'show_tooltip' => TRUE,
      ],
    ]);

    $surface = $this->buildSurfaceSummary($node);
    $location = $this->buildLocationSummary($node);
    $referenceValue = $this->getFieldString($node, 'field_reference');
    $title = trim((string) $node->label());
    $commercialTitle = $this->getFieldString($node, 'field_commercial_title');
    $surfaceText = trim((string) ($surface['value'] ?? '') . ' ' . (string) ($surface['unit'] ?? ''));
    $availabilityValue = $this->getFieldString($node, 'field_availability');
    $mandateRaw = $this->getFieldString($node, 'field_mandate_type');
    $mandateValue = $this->resolveDictionaryValue('mandate_type', $mandateRaw);
    $actions = $this->buildMetaActions($node);
    $actions = array_merge($actions, $this->buildBrochureActions($node));

    $top = NULL;
    if ($referenceValue !== '') {
      $top = [
        '#type' => 'inline_template',
        '#template' => '<div class="ps-offer-meta__reference">{{ label }}: <span>{{ value }}</span></div>',
        '#context' => [
          'label' => (string) $this->t('Reference'),
          'value' => $referenceValue,
        ],
      ];
    }

    $headerLeft = NULL;
    if ($commercialTitle !== '' || $title !== '' || $surfaceText !== '' || $location !== '') {
      $headerLeft = [
        '#type' => 'inline_template',
        '#template' => '{% if commercial_title %}<p class="ps-offer-meta__commercial-title">{{ commercial_title }}</p>{% endif %}{% if title %}<h1 class="ps-offer-meta__title">{{ title }}</h1>{% endif %}{% if surface or location %}<div class="ps-offer-meta__primary-info">{% if surface %}<span class="ps-offer-meta__surface">{{ surface }}</span>{% endif %}{% if surface and location %}<span class="offer-meta__separator" aria-hidden="true">&bull;</span>{% endif %}{% if location %}<span class="ps-offer-meta__location">{{ location }}</span>{% endif %}</div>{% endif %}',
        '#context' => [
          'commercial_title' => $commercialTitle,
          'title' => $title,
          'surface' => $surfaceText,
          'location' => $location,
        ],
      ];
    }

    $content = NULL;
    if ($availabilityValue !== '' || $mandateValue !== '') {
      $content = [
        '#type' => 'inline_template',
        '#template' => '<div class="ps-offer-meta__secondary-info">{% if availability_value %}<div class="ps-offer-meta__availability"><span class="ps-offer-meta__label">{{ availability_label }}:</span><strong class="ps-offer-meta__value">{{ availability_value }}</strong></div>{% endif %}{% if mandate_value %}<div class="ps-offer-meta__mandate"><span class="ps-offer-meta__label">{{ mandate_label }}:</span><strong class="ps-offer-meta__value">{{ mandate_value }}</strong></div>{% endif %}</div>',
        '#context' => [
          'availability_label' => (string) $this->t('Availability'),
          'availability_value' => $availabilityValue,
          'mandate_label' => (string) $this->t('Mandate type'),
          'mandate_value' => $mandateValue,
        ],
      ];
    }

    $actionParts = [];
    foreach ($actions as $action) {
      $label = trim((string) ($action['label'] ?? ''));
      $url = trim((string) ($action['url'] ?? ''));
      if ($label === '' || $url === '') {
        continue;
      }

      $actionParts[] = [
        '#type' => 'inline_template',
        '#template' => "{{ include('ui_suite_bnppre:button', {attributes: create_attribute().addClass('ps-offer-meta__action'), label: label, url: url, variant: variant}, with_context = false) }}",
        '#context' => [
          'label' => $label,
          'url' => $url,
          'variant' => (string) ($action['variant'] ?? 'primary'),
        ],
      ];
    }

    $footerActions = $this->buildSlotContainer('ps-offer-meta__actions', $actionParts);

    $footer = $this->buildSlotContainer('ps-offer-meta__footer-extra', [$footerActions]);

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:offer_meta',
      '#props' => [],
    ];

    $slots = [];
    if ($top !== NULL) {
      $slots['top'] = $top;
    }

    if ($headerLeft !== NULL) {
      $slots['header_left'] = $headerLeft;
    }

    if ($price !== NULL) {
      $slots['header_right'] = $price;
    }

    if ($content !== NULL) {
      $slots['content'] = $content;
    }

    if ($footer !== NULL) {
      $slots['footer'] = $footer;
    }

    if ($slots !== []) {
      $component['#slots'] = $slots;
    }

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--meta', [$component]);
  }

  /**
   * Builds diagnostics slot using the ps_diagnostic domain model.
   */
  protected function buildDiagnosticsSlot(NodeInterface $node): ?array {
    if (!$node->hasField('field_diagnostics') || $node->get('field_diagnostics')->isEmpty()) {
      return NULL;
    }

    $items = [];
    foreach ($node->get('field_diagnostics') as $diagnosticItem) {
      $data = $diagnosticItem->getValue();
      $typeId = (string) ($data['type_id'] ?? '');
      $definition = $this->loadDiagnosticDefinition($typeId);
      if ($definition === NULL) {
        continue;
      }

      $displayInfo = $this->getDiagnosticClassCalculator()->getDisplayInfo($data);
      $isSpecial = !empty($displayInfo['is_special']);
      $displayText = $isSpecial && isset($displayInfo['display_text']) ? (string) $displayInfo['display_text'] : '';
      $items[] = [
        'label' => $definition->getLabel(),
        'icon' => trim($definition->getIcon()),
        'value' => isset($data['value']) && is_numeric($data['value']) ? (float) $data['value'] : NULL,
        'unit' => $definition->getUnit(),
        'valid_from' => (string) ($data['valid_from'] ?? ''),
        'valid_to' => (string) ($data['valid_to'] ?? ''),
        'classes' => $definition->getClasses(),
        'is_dimmed' => !empty($data['no_classification']) || !empty($data['non_applicable']),
        'display_text_override' => $displayText,
        'empty_message' => !empty($data['non_applicable'])
          ? (string) $this->t('Diagnostic not applicable')
          : (string) $this->t('Energy label not provided by the owner'),
      ];
    }

    if ($items === []) {
      return NULL;
    }

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:diagnostics',
      '#props' => [
        'title' => (string) $this->t('Diagnostics'),
        'icon' => 'energy-cons',
        'diagnostics' => $items,
      ],
    ];

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--energy', [$component]);
  }

  /**
   * Builds a sidebar with agent and visit cards plus brochures.
   */
  protected function buildSidebarSlot(NodeInterface $node): ?array {
    $parts = [];
    $agentCard = $this->buildAgentCard($node);
    if ($agentCard !== NULL) {
      $parts[] = $agentCard;

      $visitCard = $this->buildVisitCard($node);
      if ($visitCard !== NULL) {
        $parts[] = $visitCard;
      }
    }

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--sidebar d-flex gap-4', $parts);
  }

  /**
   * Builds the surface section from divisions or total surface fallback.
   */
  protected function buildSurfaceSlot(NodeInterface $node): ?array {
    $headers = [
      (string) $this->t('Lot'),
      (string) $this->t('Floor'),
      (string) $this->t('Availability'),
      (string) $this->t('Surface'),
    ];

    $rows = [];
    if ($node->hasField('field_divisions') && !$node->get('field_divisions')->isEmpty()) {
      foreach ($node->get('field_divisions')->referencedEntities() as $division) {
        $surfaceSummary = $division->hasField('surfaces') ? $this->summarizeSurfaceItems($division->get('surfaces')) : NULL;
        $rows[] = [
          $division->hasField('lot') ? trim((string) $division->get('lot')->value) : '',
          $division->hasField('floor') ? trim((string) $division->get('floor')->value) : '',
          $division->hasField('availability') ? trim((string) $division->get('availability')->value) : '',
          $surfaceSummary ? trim($surfaceSummary['value'] . ' ' . $surfaceSummary['unit']) : '',
        ];
      }
    }

    if ($rows === []) {
      $surfaceSummary = $this->buildSurfaceSummary($node);
      if ($surfaceSummary !== NULL) {
        $headers = [
          (string) $this->t('Availability'),
          (string) $this->t('Surface'),
        ];
        $rows[] = [
          $this->getFieldString($node, 'field_availability'),
          trim($surfaceSummary['value'] . ' ' . $surfaceSummary['unit']),
        ];
      }
    }

    if ($rows === []) {
      return NULL;
    }

    $table = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:table',
      '#props' => [
        'hover' => TRUE,
        'responsive' => 'responsive-lg',
        'borders' => 'borderless',
      ],
      '#slots' => [
        'header' => $this->buildTableHeader($headers),
        'rows' => $this->buildTableRows($rows),
      ],
    ];

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:offer_surface',
      '#props' => [
        'title' => (string) $this->t('Surface table'),
        'icon' => 'surface',
      ],
      '#slots' => [
        'table' => $table,
      ],
      '#cache' => [
        'tags' => $node->getCacheTags(),
      ],
    ];

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--surface', [$component]);
  }

  /**
   * Builds the description section with read-more behavior.
   */
  protected function buildDescriptionSlot(NodeInterface $node, EntityViewDisplayInterface $display): ?array {
    $content = $this->buildField($node, $display, 'body', ['label' => 'hidden']);
    if ($content === NULL) {
      return NULL;
    }

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:offer_description',
      '#props' => [
        'title' => (string) $this->t('Description'),
        'max_height' => 150,
        'expand_label' => (string) $this->t('Read more'),
        'collapse_label' => (string) $this->t('Read less'),
      ],
      '#slots' => [
        'content' => $content,
      ],
    ];

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--description', [$component]);
  }

  /**
   * Builds the features section from normalized offer features.
   */
  protected function buildFeaturesSlot(NodeInterface $node): ?array {
    $groups = [];
    $cacheTags = ['ps_feature_list', 'ps_dictionary:feature_group'];

    if (!$node->hasField('field_features') || $node->get('field_features')->isEmpty()) {
      return NULL;
    }

    foreach ($node->get('field_features') as $featureItem) {
      $featureId = (string) ($featureItem->feature_id ?? '');
      if ($featureId === '' || $this->isTransportFeature($featureId) || $featureId === 'availability_status') {
        continue;
      }

      $formatted = $this->formatFeatureItem($featureItem);
      if ($formatted === NULL) {
        continue;
      }

      if ($formatted['cache_tag'] !== NULL) {
        $cacheTags[] = $formatted['cache_tag'];
      }

      $groupKey = $formatted['group_code'];
      if (!isset($groups[$groupKey])) {
        $groups[$groupKey] = [
          'title' => $formatted['group_label'],
          'icon' => $formatted['group_icon'],
          'columns_min_items' => $this->resolveFeatureGroupColumnsMinItems($groupKey),
          'weight' => $formatted['group_weight'],
          'items' => [],
        ];
      }

      $groups[$groupKey]['items'][] = [
        'text' => $formatted['text'],
        'weight' => $formatted['item_weight'],
      ];
    }

    if ($groups === []) {
      return NULL;
    }

    uasort($groups, static fn (array $left, array $right): int => $left['weight'] <=> $right['weight']);

    $normalizedGroups = [];
    foreach ($groups as $group) {
      usort($group['items'], static fn (array $left, array $right): int => $left['weight'] <=> $right['weight']);
      $normalizedGroups[] = [
        'title' => $group['title'],
        'icon' => $group['icon'],
        'columns_min_items' => $group['columns_min_items'],
        'items' => array_column($group['items'], 'text'),
      ];
    }

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:offer_features',
      '#props' => [
        'title' => (string) $this->t('Features'),
        'groups' => $normalizedGroups,
      ],
      '#cache' => [
        'tags' => array_values(array_unique($cacheTags)),
      ],
    ];

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--features', [$component]);
  }

  /**
   * Builds the location section with address and transport details.
   */
  protected function buildLocationSlot(NodeInterface $node): ?array {
    $addressLines = [];
    if ($node->hasField('field_address') && !$node->get('field_address')->isEmpty()) {
      $item = $node->get('field_address')->first();
      if ($item !== NULL) {
        foreach ([
          trim((string) ($item->address_line1 ?? '')),
          trim((string) ($item->address_line2 ?? '')),
          trim(implode(' ', array_filter([
            (string) ($item->postal_code ?? ''),
            (string) ($item->locality ?? ''),
          ]))),
        ] as $line) {
          if ($line !== '') {
            $addressLines[] = $line;
          }
        }
      }
    }

    $transportItems = $this->buildTransportItems($node);
    if ($addressLines === [] && $transportItems === []) {
      return NULL;
    }

    $component = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:offer_location',
      '#props' => [
        'title' => (string) $this->t('Location'),
        'icon' => 'pin-map',
        'address_lines' => $addressLines,
        'transport_title' => $transportItems !== [] ? (string) $this->t('Nearby transport') : '',
        'transport_items' => $transportItems,
      ],
      '#cache' => [
        'tags' => $node->getCacheTags(),
      ],
    ];

    return $this->buildSlotContainer('ps-offer-slot ps-offer-slot--location', [$component]);
  }

  /**
   * Returns a plain string value for simple textual fields.
   */
  protected function getFieldString(NodeInterface $node, string $fieldName): string {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return '';
    }

    return trim($node->get($fieldName)->getString());
  }

  /**
   * Builds concise CTA actions for the offer meta block.
   *
   * @return array<int, array{label: string, url: string, variant: string}>
   *   Action definitions.
   */
  protected function buildMetaActions(NodeInterface $node): array {
    $actions = [];

    $hasDivisions = $node->hasField('field_divisions') && !$node->get('field_divisions')->isEmpty();
    $hasSurfaces = $node->hasField('field_surfaces') && !$node->get('field_surfaces')->isEmpty();

    if ($hasDivisions || $hasSurfaces) {
      $actions[] = [
        'label' => (string) $this->t('Access the surface table'),
        'url' => '#surface-table',
        'variant' => 'outline_primary',
      ];
    }

    return $actions;
  }

  /**
   * Builds brochure actions from referenced media files.
   *
   * @return array<int, array{label: string, url: string, variant: string}>
   *   Brochure actions.
   */
  protected function buildBrochureActions(NodeInterface $node): array {
    if (!$node->hasField('field_media_brochures') || $node->get('field_media_brochures')->isEmpty()) {
      return [];
    }

    $actions = [];
    $total = count($node->get('field_media_brochures')->referencedEntities());
    foreach ($node->get('field_media_brochures')->referencedEntities() as $delta => $media) {
      $url = $this->extractMediaFileUrl($media);
      if ($url === '') {
        continue;
      }

      $label = $total > 1
        ? (string) $this->t('Download brochure @number', ['@number' => $delta + 1])
        : (string) $this->t('Download brochure');

      $actions[] = [
        'label' => $label,
        'url' => $url,
        'variant' => 'primary',
      ];
    }

    return $actions;
  }

  /**
   * Extracts a downloadable URL from a media file entity.
   */
  protected function extractMediaFileUrl(EntityInterface $media): string {
    foreach (['field_media_file', 'field_file', 'field_document', 'field_media_document'] as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }

      $file = $media->get($fieldName)->entity;
      if ($file && method_exists($file, 'getFileUri')) {
        return $this->getFileUrlGenerator()->generateString((string) $file->getFileUri());
      }
    }

    return '';
  }

  /**
   * Builds the table header cells.
   *
   * @param list<string> $labels
   *   Column labels.
   *
   * @return array<int, array>
   *   Header cell render arrays.
   */
  protected function buildTableHeader(array $labels): array {
    $cells = [];
    foreach ($labels as $label) {
      $cells[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnppre:table_cell',
        '#props' => ['tag' => 'th'],
        '#slots' => ['content' => ['#plain_text' => $label]],
      ];
    }

    return $cells;
  }

  /**
   * Builds body rows for the table component.
   *
   * @param list<list<string>> $rows
   *   Table rows.
   *
   * @return array<int, array>
   *   Row render arrays.
   */
  protected function buildTableRows(array $rows): array {
    $builtRows = [];
    foreach ($rows as $row) {
      $cells = [];
      foreach ($row as $value) {
        $cells[] = [
          '#type' => 'component',
          '#component' => 'ui_suite_bnppre:table_cell',
          '#slots' => ['content' => ['#plain_text' => $value]],
        ];
      }

      $builtRows[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnppre:table_row',
        '#slots' => ['cells' => $cells],
      ];
    }

    return $builtRows;
  }

  /**
   * Builds normalized transport items from transport-related features.
   *
   * @return list<array{name: string, value: string, icon: string}>
   *   Transport items.
   */
  protected function buildTransportItems(NodeInterface $node): array {
    if (!$node->hasField('field_features') || $node->get('field_features')->isEmpty()) {
      return [];
    }

    $mapping = [
      'transport_metro' => [
        'label' => (string) $this->t('Metro'),
        'icon' => 'metro-borders',
      ],
      'transport_rer' => [
        'label' => (string) $this->t('RER'),
        'icon' => 'rer-borders',
      ],
      'transport_tram' => [
        'label' => (string) $this->t('Tram'),
        'icon' => 'tram-borders',
      ],
      'transport_road_access' => [
        'label' => (string) $this->t('Road access'),
        'icon' => 'car',
      ],
    ];

    $items = [];
    foreach ($node->get('field_features') as $feature) {
      $featureId = (string) ($feature->feature_id ?? '');
      $value = trim((string) ($feature->value_string ?? ''));
      if ($value === '' || !isset($mapping[$featureId])) {
        continue;
      }

      $items[] = [
        'name' => $mapping[$featureId]['label'],
        'value' => $value,
        'icon' => $mapping[$featureId]['icon'],
      ];
    }

    return $items;
  }

  /**
   * Formats one feature item for the features list.
   *
   * @return array{text: string, cache_tag: string|null, group_code: string, group_label: string, group_icon: string, group_weight: int, item_weight: int}|null
   *   Formatted item text and optional cache tag.
   */
  protected function formatFeatureItem(object $featureItem): ?array {
    $featureId = (string) ($featureItem->feature_id ?? '');
    if ($featureId === '') {
      return NULL;
    }

    $definition = $this->getFeatureManager()->getFeature($featureId);
    $label = $definition?->label() ?? $this->humanizeFeatureId($featureId);
    $valueType = (string) ($featureItem->value_type ?? $definition?->getValueType() ?? '');
    $dictionaryType = (string) ($definition?->getDictionaryType() ?? $featureItem->dictionary_type ?? '');
    $complement = trim((string) ($featureItem->complement ?? ''));
    $cacheTag = $dictionaryType !== '' ? 'ps_dictionary:' . $dictionaryType : NULL;
    $groupCode = (string) ($definition?->getGroup() ?? 'more_information');
    $groupDefinition = $this->getDictionaryManager()->getEntry('feature_group', strtoupper($groupCode));

    $text = match ($valueType) {
      'flag' => $label,
      'yesno', 'boolean' => $label . ': ' . (!empty($featureItem->value_boolean) ? (string) $this->t('Yes') : (string) $this->t('No')),
      'dictionary' => $this->formatLabeledFeatureValue($label, $this->resolveDictionaryValue($dictionaryType, (string) ($featureItem->value_string ?? ''))),
      'numeric' => $this->formatLabeledFeatureValue($label, $this->formatNumericFeatureValue($featureItem, $definition?->getUnit())),
      'range' => $this->formatLabeledFeatureValue($label, $this->formatRangeFeatureValue($featureItem, $definition?->getUnit())),
      default => $this->formatLabeledFeatureValue($label, trim((string) ($featureItem->value_string ?? ''))),
    };

    if ($text === NULL) {
      return NULL;
    }

    if ($complement !== '') {
      $text .= ' (' . $complement . ')';
    }

    return [
      'text' => $text,
      'cache_tag' => $cacheTag,
      'group_code' => $groupCode,
      'group_label' => $groupDefinition?->getLabel() ?? $this->humanizeFeatureId($groupCode),
      'group_icon' => $this->resolveFeatureGroupIcon($groupCode, (string) ($groupDefinition?->getMetadataValue('icon', 'info') ?? 'info')),
      'group_weight' => $groupDefinition?->getWeight() ?? 99,
      'item_weight' => $definition?->getWeight() ?? 99,
    ];
  }

  /**
   * Returns a valid icon name for one feature group.
   */
  protected function resolveFeatureGroupIcon(string $groupCode, string $candidate): string {
    $availableIcons = [
      'equipement',
      'offices',
      'business-premises',
      'information',
    ];

    if (in_array($candidate, $availableIcons, TRUE)) {
      return $candidate;
    }

    return match ($groupCode) {
      'equipments' => 'equipement',
      'services' => 'offices',
      'building_condition' => 'business-premises',
      'more_information', 'commercial_conditions' => 'information',
      default => 'information',
    };
  }

  /**
   * Returns the minimum item count before enabling two-column layout.
   */
  protected function resolveFeatureGroupColumnsMinItems(string $groupCode): int {
    return match ($groupCode) {
      'services' => 4,
      default => 0,
    };
  }

  /**
   * Formats a feature label/value pair.
   */
  protected function formatLabeledFeatureValue(string $label, string $value): ?string {
    $label = trim($label);
    $value = trim($value);

    if ($label === '' && $value === '') {
      return NULL;
    }

    if ($value === '') {
      return $label !== '' ? $label : NULL;
    }

    return $label !== '' ? $label . ': ' . $value : $value;
  }

  /**
   * Formats a numeric feature value with its unit.
   */
  protected function formatNumericFeatureValue(object $featureItem, ?string $defaultUnit = NULL): string {
    if (!is_numeric($featureItem->value_numeric ?? NULL)) {
      return '';
    }

    $value = $this->formatNumber((float) $featureItem->value_numeric);
    $unit = trim((string) ($featureItem->unit ?? $defaultUnit ?? ''));
    return trim($value . ' ' . $unit);
  }

  /**
   * Formats a range feature value with its unit.
   */
  protected function formatRangeFeatureValue(object $featureItem, ?string $defaultUnit = NULL): string {
    $min = is_numeric($featureItem->value_range_min ?? NULL) ? $this->formatNumber((float) $featureItem->value_range_min) : '';
    $max = is_numeric($featureItem->value_range_max ?? NULL) ? $this->formatNumber((float) $featureItem->value_range_max) : '';
    if ($min === '' && $max === '') {
      return '';
    }

    $value = $min !== '' && $max !== '' ? $min . ' - ' . $max : ($min !== '' ? $min : $max);
    $unit = trim((string) ($featureItem->unit ?? $defaultUnit ?? ''));
    return trim($value . ' ' . $unit);
  }

  /**
   * Resolves a dictionary value to its label when possible.
   */
  protected function resolveDictionaryValue(string $dictionaryType, string $value): string {
    $value = trim($value);
    if ($dictionaryType === '' || $value === '') {
      return $value;
    }

    return $this->getDictionaryManager()->getLabel($dictionaryType, $value) ?? $value;
  }

  /**
   * Indicates whether a feature belongs to the transport block.
   */
  protected function isTransportFeature(string $featureId): bool {
    return in_array($featureId, [
      'transport_bus_nearby',
      'transport_metro',
      'transport_rer',
      'transport_tram',
      'transport_road_access',
      'transport_walking_minutes',
      'transport_cycling',
    ], TRUE);
  }

  /**
   * Creates a readable fallback label from a feature machine name.
   */
  protected function humanizeFeatureId(string $featureId): string {
    return ucwords(str_replace('_', ' ', trim($featureId)));
  }

  /**
   * Builds the main agent card from the first referenced agent.
   */
  protected function buildAgentCard(NodeInterface $node): ?array {
    $agent = $this->getPrimaryAgent($node);
    if ($agent === NULL) {
      return NULL;
    }

    $ctaUrl = Url::fromUri('internal:/webform/ps_contact_offer', [
      'query' => [
        'source_entity_type' => 'node',
        'source_entity_id' => (string) $node->id(),
      ],
    ])->toString();
    $ctaAttributes = [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => json_encode([
        'width' => 584,
        'title' => (string) $this->t('Contact the consultancy'),
        'dialogHeadingLevel' => 3,
        'dialogClass' => 'ps-contact-modal-dialog',
      ], JSON_UNESCAPED_SLASHES),
    ];
    $attached = [
      'library' => ['core/drupal.dialog.ajax'],
    ];

    return [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:card_agent',
      '#props' => [
        'title' => (string) $this->t('Your agent:'),
        'name' => $agent->label(),
        'phone' => (string) ($agent->getPhone() ?? ''),
        'phone_label' => (string) ($agent->getPhone() ?? ''),
        'cta_label' => (string) $this->t('Contact the agent'),
        'cta_url' => $ctaUrl,
        'cta_attributes' => $ctaAttributes,
        'cta_variant' => 'primary',
        'image_url' => $this->getAgentAvatarUrl($agent),
        'image_alt' => $agent->label(),
        'avatar_gender' => $this->getAvatarGenderFromCivility($agent),
      ],
      '#attached' => $attached,
    ];
  }

  /**
   * Builds the visit card using the best available contact channel.
   */
  protected function buildVisitCard(NodeInterface $node): ?array {
    $agent = $this->getPrimaryAgent($node);
    if ($agent === NULL) {
      return NULL;
    }

    $ctaUrl = '';
    $email = $agent->getEmail();
    if ($email) {
      $subject = rawurlencode((string) $this->t('Visit request for @offer', ['@offer' => $node->label()]));
      $ctaUrl = 'mailto:' . $email . '?subject=' . $subject;
    }
    elseif ($agent->getPhone()) {
      $ctaUrl = 'tel:' . preg_replace('/[^\d+]/', '', $agent->getPhone());
    }

    if ($ctaUrl === '') {
      return NULL;
    }

    return [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:card_agent_visit',
      '#props' => [
        'title' => (string) $this->t('Would you like to visit?'),
        'cta_label' => (string) $this->t('Schedule a visit'),
        'cta_aria_label' => (string) $this->t('Schedule a visit for @offer', ['@offer' => $node->label()]),
        'cta_url' => $ctaUrl,
        'cta_variant' => 'outline_primary',
        'show_icon' => TRUE,
      ],
    ];
  }

  /**
   * Resolves avatar gender from agent civility metadata.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface|null $agent
   *   The agent entity.
   *
   * @return string
   *   The avatar gender ('male' or 'female') from dictionary metadata, defaults to 'male'.
   */
  protected function getAvatarGenderFromCivility(?AgentInterface $agent): string {
    if (!$agent || !$this->dictionaryManager) {
      return 'male';
    }

    $civility = $agent->getCivility();
    if (!$civility) {
      return 'male';
    }

    $metadata = $this->dictionaryManager->getMetadata('civility', $civility);
    return (string) ($metadata['avatar_gender'] ?? 'male');
  }

  /**
   * Builds a displayable total surface summary.
   *
   * @return array{value: string, unit: string}|null
   *   Surface data or NULL.
   */
  protected function buildSurfaceSummary(NodeInterface $node): ?array {
    if (!$node->hasField('field_surfaces') || $node->get('field_surfaces')->isEmpty()) {
      return NULL;
    }

    $selectedItem = NULL;
    foreach ($node->get('field_surfaces') as $surfaceItem) {
      if (($surfaceItem->qualification ?? '') === 'TOTAL') {
        $selectedItem = $surfaceItem;
        break;
      }
      if ($selectedItem === NULL) {
        $selectedItem = $surfaceItem;
      }
    }

    if ($selectedItem === NULL || !is_numeric($selectedItem->value)) {
      return NULL;
    }

    return [
      'value' => $this->formatNumber((float) $selectedItem->value),
      'unit' => $this->mapSurfaceUnit((string) ($selectedItem->unit ?? '')),
    ];
  }

  /**
   * Builds a summary from ps_surface field items.
   *
   * @param iterable $items
   *   Surface items.
   *
   * @return array{value: string, unit: string}|null
   *   Surface summary.
   */
  protected function summarizeSurfaceItems(iterable $items): ?array {
    $total = 0.0;
    $unit = '';
    foreach ($items as $item) {
      if (!is_numeric($item->value ?? NULL)) {
        continue;
      }
      $total += (float) $item->value;
      if ($unit === '' && !empty($item->unit)) {
        $unit = $this->mapSurfaceUnit((string) $item->unit);
      }
    }

    if ($total <= 0.0) {
      return NULL;
    }

    return [
      'value' => $this->formatNumber($total),
      'unit' => $unit,
    ];
  }

  /**
   * Builds a concise location string from the postal address.
   */
  protected function buildLocationSummary(NodeInterface $node): string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return '';
    }

    $item = $node->get('field_address')->first();
    if ($item === NULL) {
      return '';
    }

    $postalCode = trim((string) ($item->postal_code ?? ''));
    $locality = trim((string) ($item->locality ?? ''));
    $administrativeArea = trim((string) ($item->administrative_area ?? ''));

    $parts = array_filter([
      $postalCode,
      $locality !== '' ? mb_strtoupper($locality) : '',
      ($administrativeArea !== '' && strcasecmp($administrativeArea, $locality) !== 0) ? mb_strtoupper($administrativeArea) : '',
    ]);

    return implode(' ', $parts);
  }

  /**
   * Builds one compact price summary for the search card.
   *
   * @return array{prefix: string, value: string, unit: string}
   *   Formatted prefix, value and unit parts.
   */
  protected function buildCardSearchPriceSummary(NodeInterface $node): array {
    if (!$node->hasField('field_prices') || $node->get('field_prices')->isEmpty()) {
      return ['prefix' => '', 'value' => '', 'unit' => ''];
    }

    $item = $node->get('field_prices')->first();
    if ($item === NULL) {
      return ['prefix' => '', 'value' => '', 'unit' => ''];
    }

    if (!empty($item->is_on_request)) {
      return ['prefix' => '', 'value' => (string) $this->t('On request'), 'unit' => ''];
    }

    $amount = is_numeric($item->amount ?? NULL) ? (float) $item->amount : 0.0;
    $currencyCode = trim((string) ($item->currency_code ?? ''));
    $currencySymbol = $currencyCode;
    if ($currencyCode !== '') {
      $currencySymbol = (string) ($this->getDictionaryManager()->getMetadataValue('currency', $currencyCode, 'symbol', $currencyCode) ?? $currencyCode);
    }

    $pricePrefix = !empty($item->is_from) ? (string) $this->t('From') : '';
    $valueParts = [];
    $valueParts[] = $this->formatPriceAmount($amount);
    if ($currencySymbol !== '') {
      $valueParts[] = $currencySymbol;
    }

    $unitParts = [];
    $flags = [];
    if (!empty($item->is_vat_excluded)) {
      $flags[] = 'HT';
    }
    if (empty($item->is_charges_included)) {
      $flags[] = 'HC';
    }
    if ($flags !== []) {
      $unitParts[] = implode('/', $flags);
    }

    $unitCode = trim((string) ($item->unit_code ?? ''));
    if ($unitCode !== '') {
      $unitSymbol = (string) ($this->getDictionaryManager()->getMetadataValue('price_unit', $unitCode, 'symbol', '') ?? '');
      if ($unitSymbol === '') {
        $unitSymbol = (string) ($this->getDictionaryManager()->getLabel('price_unit', $unitCode) ?? $unitCode);
      }
      $unitSymbol = str_replace('²', '2', $unitSymbol);
      $unitParts[] = ltrim($unitSymbol, '/');
    }

    $periodCode = trim((string) ($item->period_code ?? ''));
    if ($periodCode !== '') {
      $unitParts[] = ltrim($this->mapPricePeriodCode($periodCode), '/');
    }

    return [
      'prefix' => $pricePrefix,
      'value' => implode(' ', array_filter($valueParts, static fn (string $part): bool => $part !== '')),
      'unit' => implode('/', array_filter($unitParts, static fn (string $part): bool => $part !== '')),
    ];
  }

  /**
   * Formats one amount according to locale conventions.
   */
  protected function formatPriceAmount(float $amount): string {
    $formatter = \NumberFormatter::create('fr_FR', \NumberFormatter::DECIMAL);
    if (!$formatter instanceof \NumberFormatter) {
      return (string) $amount;
    }

    if (fmod($amount, 1.0) === 0.0) {
      $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);
      $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
    }
    else {
      $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
      $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);
    }

    $formatted = $formatter->format($amount);
    return $formatted !== FALSE ? (string) $formatted : (string) $amount;
  }

  /**
   * Maps period code to compact display label.
   */
  protected function mapPricePeriodCode(string $periodCode): string {
    return match (strtoupper($periodCode)) {
      'ANN' => 'an',
      'MEN' => 'mois',
      'TRI' => 'trimestre',
      'SEM' => 'semaine',
      default => $periodCode,
    };
  }

  /**
   * Returns the first referenced agent.
   */
  protected function getPrimaryAgent(NodeInterface $node): ?AgentInterface {
    if (!$node->hasField('field_agents') || $node->get('field_agents')->isEmpty()) {
      return NULL;
    }

    foreach ($node->get('field_agents')->referencedEntities() as $agent) {
      if ($agent instanceof AgentInterface) {
        return $agent;
      }
    }

    return NULL;
  }

  /**
   * Resolves the agent avatar URL when available.
   */
  protected function getAgentAvatarUrl(AgentInterface $agent): string {
    if (!$agent->hasField('avatar') || $agent->get('avatar')->isEmpty()) {
      return '';
    }

    $file = $agent->get('avatar')->entity;
    if ($file === NULL || !$file->hasField('uri')) {
      return '';
    }

    return $this->getFileUrlGenerator()->generateString((string) $file->getFileUri());
  }

  /**
   * Returns the entity type manager with compatibility fallback.
   */
  protected function getEntityTypeManager(): EntityTypeManagerInterface {
    if ($this->entityTypeManager === NULL) {
      $this->entityTypeManager = \Drupal::service('entity_type.manager');
    }

    return $this->entityTypeManager;
  }

  /**
   * Returns the diagnostic calculator with compatibility fallback.
   */
  protected function getDiagnosticClassCalculator(): DiagnosticClassCalculatorInterface {
    if ($this->diagnosticClassCalculator === NULL) {
      $service = \Drupal::service('ps_diagnostic.class_calculator');
      assert($service instanceof DiagnosticClassCalculatorInterface);
      $this->diagnosticClassCalculator = $service;
    }

    return $this->diagnosticClassCalculator;
  }

  /**
   * Returns the file URL generator with compatibility fallback.
   */
  protected function getFileUrlGenerator(): FileUrlGeneratorInterface {
    if ($this->fileUrlGenerator === NULL) {
      $service = \Drupal::service('file_url_generator');
      assert($service instanceof FileUrlGeneratorInterface);
      $this->fileUrlGenerator = $service;
    }

    return $this->fileUrlGenerator;
  }

  /**
   * Returns the feature manager with compatibility fallback.
   */
  protected function getFeatureManager(): FeatureManagerInterface {
    if ($this->featureManager === NULL) {
      $service = \Drupal::service('ps_features.manager');
      assert($service instanceof FeatureManagerInterface);
      $this->featureManager = $service;
    }

    return $this->featureManager;
  }

  /**
   * Returns the dictionary manager with compatibility fallback.
   */
  protected function getDictionaryManager(): DictionaryManagerInterface {
    if ($this->dictionaryManager === NULL) {
      $service = \Drupal::service('ps_dictionary.manager');
      assert($service instanceof DictionaryManagerInterface);
      $this->dictionaryManager = $service;
    }

    return $this->dictionaryManager;
  }

  /**
   * Loads one diagnostic configuration entity.
   */
  protected function loadDiagnosticDefinition(string $typeId): ?PsDiagnosticInterface {
    if ($typeId === '') {
      return NULL;
    }

    $definition = $this->getEntityTypeManager()->getStorage('diagnostic')->load($typeId);
    return $definition instanceof PsDiagnosticInterface ? $definition : NULL;
  }

  /**
   * Formats a decimal number for display.
   */
  protected function formatNumber(float $value): string {
    $decimals = fmod($value, 1.0) === 0.0 ? 0 : 1;
    return number_format($value, $decimals, '.', ' ');
  }

  /**
   * Maps surface unit codes to front-end labels.
   */
  protected function mapSurfaceUnit(string $unit): string {
    return match (strtoupper($unit)) {
      'M2' => 'm²',
      default => $unit,
    };
  }

  /**
   * Builds one field render array for the target display.
   */
  protected function buildField(NodeInterface $node, EntityViewDisplayInterface $display, string $fieldName, array $override = []): ?array {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return NULL;
    }

    $component = $display->getComponent($fieldName);
    if (!is_array($component) || $component === []) {
      return NULL;
    }

    $options = is_array($component) ? $component : [];
    $options = array_replace($options, $override);
    if (!isset($options['label'])) {
      $options['label'] = 'hidden';
    }

    return $node->get($fieldName)->view($options);
  }

  /**
   * Wraps non-empty renderables in a slot container.
   */
  protected function buildSlotContainer(string $class, array $parts): ?array {
    $parts = array_values(array_filter($parts, static fn ($part): bool => $part !== NULL));
    if ($parts === []) {
      return NULL;
    }

    $bubbleableMetadata = new BubbleableMetadata();
    foreach ($parts as $part) {
      if (is_array($part)) {
        $bubbleableMetadata = $bubbleableMetadata->merge(BubbleableMetadata::createFromRenderArray($part));
      }
    }

    $container = [
      '#type' => 'container',
      '#attributes' => [
        'class' => explode(' ', $class),
      ],
    ];

    foreach ($parts as $delta => $part) {
      $container['item_' . $delta] = $part;
    }

    $bubbleableMetadata->applyTo($container);

    return $container;
  }

}
