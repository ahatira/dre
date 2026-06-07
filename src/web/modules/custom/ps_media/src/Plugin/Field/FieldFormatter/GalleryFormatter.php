<?php

namespace Drupal\ps_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\media\IFrameUrlHelper;
use Drupal\ps_media\Service\GalleryBadgeIconResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_media_gallery_formatter' formatter.
 *
 * Renders gallery media items with intelligent ordering and thumbnail display.
 *
 * @FieldFormatter(
 *   id = "ps_media_gallery_formatter",
 *   label = @Translation("Gallery (PS Media)"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class GalleryFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a GalleryFormatter instance.
   */
  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly GalleryBadgeIconResolver $badgeIconResolver,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_media.gallery_badge_icon_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'order' => 'type_then_manual',  // 'type_then_manual' or 'manual_only'
      'show_titles' => TRUE,
      'use_thumbnail' => TRUE,
      'lazy_load' => TRUE,
      'max_items' => 0,
      'image_style' => '',
      'hero_image_style' => '',
      'display_template' => 'summary',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['order'] = [
      '#type' => 'select',
      '#title' => $this->t('Media ordering'),
      '#options' => [
        'type_then_manual' => $this->t('By type (images, videos, virtual tours) then manual order'),
        'manual_only' => $this->t('Manual order only'),
      ],
      '#default_value' => $this->getSetting('order'),
    ];

    $elements['show_titles'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show media titles'),
      '#default_value' => $this->getSetting('show_titles'),
    ];

    $elements['use_thumbnail'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use thumbnail images'),
      '#default_value' => $this->getSetting('use_thumbnail'),
    ];

    $elements['lazy_load'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable lazy loading'),
      '#default_value' => $this->getSetting('lazy_load'),
    ];

    $elements['max_items'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum items to render'),
      '#description' => $this->t('Use 0 to render all referenced media items.'),
      '#default_value' => (int) $this->getSetting('max_items'),
      '#min' => 0,
      '#step' => 1,
    ];

    $elements['image_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Thumbnail image style machine name (optional)'),
      '#description' => $this->t('When provided, lightbox thumbnails and non-image previews use this image style.'),
      '#default_value' => (string) $this->getSetting('image_style'),
    ];

    $elements['hero_image_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hero image style machine name (optional)'),
      '#description' => $this->t('When provided, hero carousel and lightbox main images use this image style.'),
      '#default_value' => (string) $this->getSetting('hero_image_style'),
    ];

    $elements['display_template'] = [
      '#type' => 'select',
      '#title' => $this->t('Display template'),
      '#options' => [
        'summary' => $this->t('Summary list'),
        'hero' => $this->t('Hero gallery (offer detail)'),
      ],
      '#default_value' => (string) $this->getSetting('display_template'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $order_options = [
      'type_then_manual' => $this->t('By type then manual'),
      'manual_only' => $this->t('Manual order only'),
    ];
    $summary[] = $this->t('Order: @order', [
      '@order' => $order_options[$this->getSetting('order')] ?? 'unknown',
    ]);
    $maxItems = (int) $this->getSetting('max_items');
    $summary[] = $maxItems > 0
      ? $this->t('Maximum items: @count', ['@count' => $maxItems])
      : $this->t('Maximum items: all');
    $imageStyle = trim((string) $this->getSetting('image_style'));
    if ($imageStyle !== '') {
      $summary[] = $this->t('Thumbnail image style: @style', ['@style' => $imageStyle]);
    }
    $heroImageStyle = trim((string) $this->getSetting('hero_image_style'));
    if ($heroImageStyle !== '') {
      $summary[] = $this->t('Hero image style: @style', ['@style' => $heroImageStyle]);
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($items->isEmpty()) {
      return $elements;
    }

    // Get media entities
    $entities = $this->getEntitiesToView($items, $langcode);

    if (empty($entities)) {
      return $elements;
    }

    // Sort media by type and manual order (Q9)
    if ($this->getSetting('order') === 'type_then_manual') {
      $entities = $this->sortByTypeAndOrder($entities);
    }

    $maxItems = (int) $this->getSetting('max_items');
    if ($maxItems > 0) {
      $entities = array_slice($entities, 0, $maxItems);
    }

    $imageStyle = trim((string) $this->getSetting('image_style'));
    $displayTemplate = (string) $this->getSetting('display_template');

    if ($displayTemplate === 'hero') {
      $thumbImageStyle = $imageStyle !== '' ? $imageStyle : 'ps_offer_gallery_thumb';
      $heroImageStyle = trim((string) $this->getSetting('hero_image_style'));
      if ($heroImageStyle === '') {
        $heroImageStyle = 'ps_offer_gallery_hero';
      }
      $heroStyledUrls = $this->buildStyledUrls($entities, $heroImageStyle);
      $thumbStyledUrls = $this->buildStyledUrls($entities, $thumbImageStyle);

      $offer = $items->getEntity();
      $heroData = $this->buildHeroData($entities, $heroStyledUrls, $thumbStyledUrls);
      $heroData['props']['offer_id'] = $offer ? (int) $offer->id() : 0;
      $heroData['props']['offer_type'] = $offer ? $offer->getEntityTypeId() : 'node';

      $elements[0] = [
        '#type' => 'component',
        '#component' => 'ps_theme:media-gallery-hero',
        '#props' => $heroData['props'],
        '#attached' => [
          'library' => ['ps_theme/offer-gallery'],
          'drupalSettings' => [
            'psOfferGallery' => [
              'slides' => $heroData['slides'],
              'entry_indexes' => $heroData['entry_indexes'],
            ],
          ],
        ],
      ];

      if ($offer && \Drupal::moduleHandler()->moduleExists('ps_favorite')) {
        $elements[0]['#slots']['favorite'] = [
          '#lazy_builder' => ['ps_favorite.lazy_builder:buildButton', [$offer->getEntityTypeId(), (int) $offer->id(), 'gallery']],
          '#create_placeholder' => TRUE,
        ];
      }

      CacheableMetadata::createFromObject($this->badgeIconResolver)
        ->applyTo($elements[0]);

      return $elements;
    }

    $styledUrls = $this->buildStyledUrls($entities, $imageStyle);

    // Build render array for gallery
    $elements[0] = [
      '#theme' => 'gallery_summary',
      '#offer' => $items->getEntity(),
      '#medias' => $entities,
      '#order' => $this->getSetting('order'),
      '#show_titles' => $this->getSetting('show_titles'),
      '#use_thumbnail' => $this->getSetting('use_thumbnail'),
      '#lazy_load' => $this->getSetting('lazy_load'),
      '#styled_urls' => $styledUrls,
      '#attached' => [
        'library' => ['ps_media/gallery'],
      ],
    ];

    return $elements;
  }

  /**
   * Sort media entities by type and manual order.
   *
   * Implements Q9: images → videos → virtual tours, then by delta.
   */
  protected function sortByTypeAndOrder(array $entities): array {
    $typeOrder = [
      'image' => 0,
      'remote_video' => 1,
      'mediahub_video' => 2,
      'video' => 3,
      'audio' => 4,
      'file' => 5,
      'gallery' => 6,
      'visite_guided' => 7,
    ];

    usort($entities, function ($a, $b) use ($typeOrder) {
      $type_a = $a->bundle();
      $type_b = $b->bundle();

      $order_a = $typeOrder[$type_a] ?? 999;
      $order_b = $typeOrder[$type_b] ?? 999;

      return $order_a <=> $order_b;
    });

    return $entities;
  }

  /**
   * @param \Drupal\media\MediaInterface[] $entities
   *   Renderable media entities.
   * @param string $imageStyle
   *   Image style machine name.
   *
   * @return array<int, string>
   *   Styled preview URLs keyed by media ID.
   */
  private function buildStyledUrls(array $entities, string $imageStyle): array {
    if ($imageStyle === '') {
      return [];
    }

    $style = ImageStyle::load($imageStyle);
    if ($style === NULL) {
      return [];
    }

    $urls = [];
    foreach ($entities as $entity) {
      if (!$entity instanceof MediaInterface) {
        continue;
      }

      $uri = $this->resolvePreviewUri($entity);
      if ($uri === NULL) {
        continue;
      }

      $urls[(int) $entity->id()] = $style->buildUrl($uri);
    }

    return $urls;
  }

  private function resolvePreviewUri(MediaInterface $media): ?string {
    $bundle = $media->bundle();

    if (in_array($bundle, ['image', 'visite_guided'], TRUE)) {
      return $this->extractFileUri($media, 'field_media_image');
    }

    if ($bundle === 'gallery') {
      return $this->extractFileUri($media, 'field_media_gallery_image');
    }

    return $this->extractFileUri($media, 'thumbnail');
  }

  /**
   * @return array{
   *   props: array<string, mixed>,
   *   slides: array<int, array<string, string>>,
   *   entry_indexes: array<string, int|null>
   * }
   */
  private function buildHeroData(array $entities, array $heroStyledUrls, array $thumbStyledUrls): array {
    $fileUrlGenerator = \Drupal::service('file_url_generator');
    $iframeUrlHelper = \Drupal::service('media.oembed.iframe_url_helper');
    assert($iframeUrlHelper instanceof IFrameUrlHelper);

    $photoEntities = [];
    $videoEntities = [];
    $visitEntities = [];
    $planEntities = [];

    foreach ($entities as $entity) {
      if (!$entity instanceof MediaInterface) {
        continue;
      }

      $bundle = $entity->bundle();
      if (in_array($bundle, ['image', 'gallery'], TRUE)) {
        $photoEntities[] = $entity;
        continue;
      }
      if (in_array($bundle, ['remote_video', 'mediahub_video', 'video'], TRUE)) {
        $videoEntities[] = $entity;
        continue;
      }
      if ($bundle === 'visite_guided') {
        $visitEntities[] = $entity;
        continue;
      }
      if ($bundle === 'file') {
        $planEntities[] = $entity;
      }
    }

    $images = [];
    $slides = [];

    foreach ($photoEntities as $entity) {
      $uri = $this->resolvePreviewUri($entity);
      if ($uri === NULL) {
        continue;
      }

      $mediaId = (int) $entity->id();
      $url = $heroStyledUrls[$mediaId] ?? $fileUrlGenerator->generateAbsoluteString($uri);
      $thumbUrl = $thumbStyledUrls[$mediaId] ?? $url;
      $alt = $entity->label() ?? '';
      $images[] = [
        'url' => $url,
        'alt' => $alt,
        'media_id' => (string) $mediaId,
      ];
      $slides[] = [
        'type' => 'image',
        'url' => $url,
        'thumb_url' => $thumbUrl,
        'alt' => $alt,
        'label' => $alt,
      ];
    }

    foreach ($videoEntities as $entity) {
      $slide = $this->buildVideoSlide($entity, $thumbStyledUrls, $fileUrlGenerator, $iframeUrlHelper);
      if ($slide !== NULL) {
        $slides[] = $slide;
      }
    }

    foreach ($visitEntities as $entity) {
      $slide = $this->buildVisitSlide($entity, $thumbStyledUrls, $fileUrlGenerator);
      if ($slide !== NULL) {
        $slides[] = $slide;
      }
    }

    foreach ($planEntities as $entity) {
      $slide = $this->buildPlanSlide($entity, $thumbStyledUrls, $fileUrlGenerator);
      if ($slide !== NULL) {
        $slides[] = $slide;
      }
    }

    $entryIndexes = [
      'photos' => $this->firstSlideIndexByType($slides, ['image']),
      'video' => $this->firstSlideIndexByType($slides, ['video_oembed', 'video_file', 'video_url']),
      'visit_3d' => $this->firstSlideIndexByType($slides, ['visit_3d']),
      'plan' => $this->firstSlideIndexByType($slides, ['plan_pdf', 'plan_image']),
    ];

    return [
      'props' => [
        'images' => $images,
        'photo_count' => count($images),
        'video_count' => count($videoEntities),
        'has_visit_3d' => $visitEntities !== [],
        'has_plan' => $planEntities !== [],
        'slides' => $slides,
        'entry_indexes' => $entryIndexes,
        'offer_id' => 0,
        'badge_icons' => $this->badgeIconResolver->resolve(),
      ],
      'slides' => $slides,
      'entry_indexes' => $entryIndexes,
    ];
  }

  /**
   * @param array<int, array<string, string>> $slides
   * @param string[] $types
   */
  private function firstSlideIndexByType(array $slides, array $types): ?int {
    foreach ($slides as $index => $slide) {
      if (in_array($slide['type'] ?? '', $types, TRUE)) {
        return $index;
      }
    }
    return NULL;
  }

  /**
   * @return array<string, string>|null
   */
  private function buildVideoSlide(
    MediaInterface $entity,
    array $styledUrls,
    $fileUrlGenerator,
    IFrameUrlHelper $iframeUrlHelper,
  ): ?array {
    $label = $entity->label() ?? '';
    $bundle = $entity->bundle();

    if ($bundle === 'remote_video') {
      if (!$entity->hasField('field_media_oembed_video') || $entity->get('field_media_oembed_video')->isEmpty()) {
        return NULL;
      }
      $value = (string) $entity->get('field_media_oembed_video')->value;
      $embedUrl = Url::fromRoute('media.oembed_iframe', [], [
        'absolute' => TRUE,
        'query' => [
          'url' => $value,
          'max_width' => 0,
          'max_height' => 0,
          'hash' => $iframeUrlHelper->getHash($value, 0, 0),
        ],
      ])->toString();

      return [
        'type' => 'video_oembed',
        'embed_url' => $embedUrl,
        'thumb_url' => $this->resolveThumbUrl($entity, $styledUrls, $fileUrlGenerator)
          ?? $this->resolveRemoteVideoThumb($value),
        'alt' => $label,
        'label' => $label,
      ];
    }

    if ($bundle === 'mediahub_video') {
      if (!$entity->hasField('field_media_video_url') || $entity->get('field_media_video_url')->isEmpty()) {
        return NULL;
      }
      $videoUrl = (string) $entity->get('field_media_video_url')->value;
      return [
        'type' => 'video_url',
        'video_url' => $videoUrl,
        'thumb_url' => $this->resolveThumbUrl($entity, $styledUrls, $fileUrlGenerator) ?? '',
        'alt' => $label,
        'label' => $label,
      ];
    }

    if ($bundle === 'video') {
      if (!$entity->hasField('field_media_video_file') || $entity->get('field_media_video_file')->isEmpty()) {
        return NULL;
      }
      $file = $entity->get('field_media_video_file')->entity;
      if (!$file instanceof FileInterface) {
        return NULL;
      }
      $videoUrl = $fileUrlGenerator->generateAbsoluteString($file->getFileUri());
      return [
        'type' => 'video_file',
        'video_url' => $videoUrl,
        'thumb_url' => $this->resolveThumbUrl($entity, $styledUrls, $fileUrlGenerator) ?? '',
        'alt' => $label,
        'label' => $label,
      ];
    }

    return NULL;
  }

  /**
   * @return array<string, string>|null
   */
  private function buildVisitSlide(
    MediaInterface $entity,
    array $styledUrls,
    $fileUrlGenerator,
  ): ?array {
    if (!$entity->hasField('field_media_link') || $entity->get('field_media_link')->isEmpty()) {
      return NULL;
    }

    $linkItem = $entity->get('field_media_link')->first();
    $iframeUrl = (string) ($linkItem->getUrl()->toString() ?? '');
    if ($iframeUrl === '') {
      return NULL;
    }

    $label = (string) ($linkItem->title ?? $entity->label() ?? '');
    return [
      'type' => 'visit_3d',
      'iframe_url' => $iframeUrl,
      'external_url' => $iframeUrl,
      'thumb_url' => $this->resolveThumbUrl($entity, $styledUrls, $fileUrlGenerator) ?? '',
      'alt' => $label,
      'label' => $label,
    ];
  }

  /**
   * @return array<string, string>|null
   */
  private function buildPlanSlide(
    MediaInterface $entity,
    array $styledUrls,
    $fileUrlGenerator,
  ): ?array {
    if (!$entity->hasField('field_media_file') || $entity->get('field_media_file')->isEmpty()) {
      return NULL;
    }

    $file = $entity->get('field_media_file')->entity;
    if (!$file instanceof FileInterface) {
      return NULL;
    }

    $fileUrl = $fileUrlGenerator->generateAbsoluteString($file->getFileUri());
    $label = $entity->label() ?? '';
    $mime = $file->getMimeType();
    $isImage = str_starts_with($mime, 'image/');

    return [
      'type' => $isImage ? 'plan_image' : 'plan_pdf',
      'url' => $fileUrl,
      'iframe_url' => $isImage ? '' : $fileUrl,
      'thumb_url' => $isImage ? $fileUrl : '',
      'alt' => $label,
      'label' => $label,
      'mime' => $mime,
    ];
  }

  /**
   * @param mixed $fileUrlGenerator
   */
  private function resolveThumbUrl(MediaInterface $entity, array $styledUrls, $fileUrlGenerator): ?string {
    $mediaId = (int) $entity->id();
    if (isset($styledUrls[$mediaId])) {
      return $styledUrls[$mediaId];
    }

    $uri = $this->resolvePreviewUri($entity);
    if ($uri === NULL) {
      return NULL;
    }

    return $fileUrlGenerator->generateAbsoluteString($uri);
  }

  private function resolveRemoteVideoThumb(string $oembedUrl): string {
    $youtubeId = '';
    if (str_contains($oembedUrl, 'youtube.com/watch?v=')) {
      $youtubeId = explode('v=', $oembedUrl, 2)[1] ?? '';
      $youtubeId = explode('&', $youtubeId)[0];
    }
    elseif (str_contains($oembedUrl, 'youtu.be/')) {
      $youtubeId = explode('youtu.be/', $oembedUrl, 2)[1] ?? '';
      $youtubeId = explode('?', $youtubeId)[0];
    }

    if ($youtubeId !== '') {
      return 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
    }

    return '';
  }

  private function extractFileUri(MediaInterface $media, string $fieldName): ?string {
    if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
      return NULL;
    }

    $file = $media->get($fieldName)->entity;
    return $file instanceof FileInterface ? $file->getFileUri() : NULL;
  }

}
