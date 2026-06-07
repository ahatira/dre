<?php

namespace Drupal\ps_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;

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
class GalleryFormatter extends EntityReferenceFormatterBase {

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
      '#title' => $this->t('Image style machine name (optional)'),
      '#description' => $this->t('When provided, image previews use this image style URL derivative.'),
      '#default_value' => (string) $this->getSetting('image_style'),
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
      $summary[] = $this->t('Image style: @style', ['@style' => $imageStyle]);
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
    $styledUrls = $this->buildStyledUrls($entities, $imageStyle);
    $displayTemplate = (string) $this->getSetting('display_template');

    if ($displayTemplate === 'hero') {
      $offer = $items->getEntity();
      $heroData = $this->buildHeroData($entities, $styledUrls);
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
              'images' => $heroData['lightbox_images'],
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

      return $elements;
    }

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
   * @return array{props: array<string, mixed>, lightbox_images: array<int, array<string, string>>}
   */
  private function buildHeroData(array $entities, array $styledUrls): array {
    $images = [];
    $lightboxImages = [];
    $virtualTourUrl = '';
    $planUrl = '';
    $photoCount = 0;

    foreach ($entities as $entity) {
      if (!$entity instanceof MediaInterface) {
        continue;
      }

      $bundle = $entity->bundle();
      if ($bundle === 'visite_guided') {
        if ($entity->hasField('field_media_link') && !$entity->get('field_media_link')->isEmpty()) {
          $virtualTourUrl = (string) ($entity->get('field_media_link')->first()->getUrl()->toString() ?? '');
        }
        continue;
      }

      if ($bundle === 'file') {
        if ($entity->hasField('field_media_file') && !$entity->get('field_media_file')->isEmpty()) {
          $planUrl = \Drupal::service('file_url_generator')->generateAbsoluteString(
            $entity->get('field_media_file')->entity->getFileUri()
          );
        }
        continue;
      }

      if (!in_array($bundle, ['image', 'gallery'], TRUE)) {
        continue;
      }

      $uri = $this->resolvePreviewUri($entity);
      if ($uri === NULL) {
        continue;
      }

      $url = $styledUrls[(int) $entity->id()] ?? \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
      $images[] = [
        'url' => $url,
        'alt' => $entity->label() ?? '',
        'media_id' => (string) $entity->id(),
      ];
      $lightboxImages[] = [
        'url' => $url,
        'alt' => $entity->label() ?? '',
      ];
      $photoCount++;
    }

    return [
      'props' => [
        'images' => $images,
        'photo_count' => $photoCount,
        'virtual_tour_url' => $virtualTourUrl,
        'plan_url' => $planUrl,
        'offer_id' => 0,
      ],
      'lightbox_images' => $lightboxImages,
    ];
  }

  private function extractFileUri(MediaInterface $media, string $fieldName): ?string {
    if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
      return NULL;
    }

    $file = $media->get($fieldName)->entity;
    return $file instanceof FileInterface ? $file->getFileUri() : NULL;
  }

}
