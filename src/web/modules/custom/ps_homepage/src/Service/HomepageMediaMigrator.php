<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Service\HomepageLayoutPersister;

/**
 * Migrates homepage block image fields from file IDs to media references.
 */
final class HomepageMediaMigrator {

  /**
   * Homepage block plugin IDs with image configuration.
   *
   * @var list<string>
   */
  private const HOMEPAGE_BLOCK_IDS = [
    'ps_content_experts_accompagnement_block',
    'ps_content_outils_accordion_block',
    'ps_market_study_market_studies_block',
    'ps_homepage_search_hero_block',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageLayoutPersister $layoutPersister,
  ) {}

  /**
   * Migrates all page layouts from legacy file IDs to media references.
   */
  public function migrateAllPageLayouts(): int {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'page')
      ->exists('layout_builder__layout')
      ->execute();

    $count = 0;
    foreach ($storage->loadMultiple($nids) as $node) {
      if ($node instanceof NodeInterface && $this->migrateNode($node)) {
        $count++;
      }
    }

    return $count;
  }

  public function migrateNode(NodeInterface $node): bool {
    if (!$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages(FALSE) as $langcode => $_language) {
      $translation = $node->getTranslation($langcode);
      $nextSections = $this->migrateTranslationLayout($translation);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * @return list<Section>|null
   */
  private function migrateTranslationLayout(NodeInterface $translation): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $updated = FALSE;
    $nextSections = [];

    foreach ($field->getSections() as $section) {
      if (!$section instanceof Section) {
        $nextSections[] = $section;
        continue;
      }

      $sectionData = $section->toArray();
      $nextComponents = [];

      foreach ($section->getComponents() as $component) {
        if (!$component instanceof SectionComponent) {
          $nextComponents[] = $component->toArray();
          continue;
        }

        $componentData = $component->toArray();
        $settings = $componentData['configuration'] ?? [];
        if (!is_array($settings) || ($settings['id'] ?? '') === '') {
          $nextComponents[] = $componentData;
          continue;
        }

        if (!in_array((string) $settings['id'], self::HOMEPAGE_BLOCK_IDS, TRUE)) {
          $nextComponents[] = $componentData;
          continue;
        }

        if ($this->migrateBlockSettings($settings)) {
          $componentData['configuration'] = $settings;
          $updated = TRUE;
        }

        $nextComponents[] = $componentData;
      }

      $sectionData['components'] = $nextComponents;
      $nextSections[] = Section::fromArray($sectionData);
    }

    return $updated ? $nextSections : NULL;
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function migrateBlockSettings(array &$settings): bool {
    $blockId = (string) ($settings['id'] ?? '');
    $changed = FALSE;

    match ($blockId) {
      'ps_content_experts_accompagnement_block' => $changed = $this->migrateExpertJourney($settings),
      'ps_content_outils_accordion_block' => $changed = $this->migrateTools($settings),
      'ps_market_study_market_studies_block' => $changed = $this->migrateMarketStudies($settings),
      'ps_homepage_search_hero_block' => $changed = $this->migrateSearchHero($settings),
      default => NULL,
    };

    return $changed;
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function migrateExpertJourney(array &$settings): bool {
    $steps = $settings['steps'] ?? NULL;
    if (!is_array($steps)) {
      return FALSE;
    }

    $changed = FALSE;
    foreach ($steps as $index => $step) {
      if (!is_array($step)) {
        continue;
      }

      $alt = trim((string) ($step['image_alt'] ?? ''));
      $credit = trim((string) ($step['image_credit'] ?? ''));
      $mid = $this->resolveMediaId($step['image'] ?? NULL, $alt, $credit);
      if ($mid !== NULL) {
        $steps[$index]['image'] = $mid;
        $changed = TRUE;
      }

      if (array_key_exists('image_alt', $steps[$index])) {
        unset($steps[$index]['image_alt']);
        $changed = TRUE;
      }
      if (array_key_exists('image_credit', $steps[$index])) {
        unset($steps[$index]['image_credit']);
        $changed = TRUE;
      }
    }

    $settings['steps'] = array_values($steps);
    return $changed;
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function migrateTools(array &$settings): bool {
    $changed = FALSE;
    $legacyIllustration = $settings['illustration'] ?? NULL;
    $legacyAlt = trim((string) ($settings['illustration_alt'] ?? ''));

    $items = $settings['items'] ?? NULL;
    if (is_array($items)) {
      foreach ($items as $index => $item) {
        if (!is_array($item)) {
          continue;
        }

        if (empty($item['illustration']) && $legacyIllustration !== NULL && $index === 0) {
          $item['illustration'] = $legacyIllustration;
        }

        $alt = trim((string) ($item['illustration_alt'] ?? $legacyAlt));
        $mid = $this->resolveMediaId($item['illustration'] ?? NULL, $alt, '');
        if ($mid !== NULL) {
          $items[$index]['illustration'] = $mid;
          $changed = TRUE;
        }

        if (array_key_exists('illustration_alt', $items[$index])) {
          unset($items[$index]['illustration_alt']);
          $changed = TRUE;
        }
      }
      $settings['items'] = array_values($items);
    }

    if (array_key_exists('illustration', $settings)) {
      unset($settings['illustration']);
      $changed = TRUE;
    }
    if (array_key_exists('illustration_alt', $settings)) {
      unset($settings['illustration_alt']);
      $changed = TRUE;
    }

    return $changed;
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function migrateMarketStudies(array &$settings): bool {
    $items = $settings['items'] ?? NULL;
    if (!is_array($items)) {
      return FALSE;
    }

    $changed = FALSE;
    foreach ($items as $index => $item) {
      if (!is_array($item)) {
        continue;
      }

      $alt = trim((string) ($item['image_alt'] ?? ''));
      $mid = $this->resolveMediaId($item['image'] ?? NULL, $alt, '');
      if ($mid !== NULL) {
        $items[$index]['image'] = $mid;
        $changed = TRUE;
      }

      if (array_key_exists('image_alt', $items[$index])) {
        unset($items[$index]['image_alt']);
        $changed = TRUE;
      }
    }

    $settings['items'] = array_values($items);
    return $changed;
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function migrateSearchHero(array &$settings): bool {
    $changed = FALSE;

    foreach (['background_image', 'promo_background_image'] as $key) {
      $altKey = $key === 'background_image' ? 'background_alt' : 'promo_background_alt';
      $alt = trim((string) ($settings[$altKey] ?? ''));
      $mid = $this->resolveMediaId($settings[$key] ?? NULL, $alt, '');
      if ($mid !== NULL) {
        $settings[$key] = $mid;
        $changed = TRUE;
      }

      if (array_key_exists($altKey, $settings)) {
        unset($settings[$altKey]);
        $changed = TRUE;
      }
    }

    return $changed;
  }

  /**
   * Converts a legacy file ID or existing media ID to a media ID.
   */
  private function resolveMediaId(mixed $reference, string $alt, string $credit): ?int {
    $id = (int) $reference;
    if ($id <= 0) {
      return NULL;
    }

    $media = Media::load($id);
    if ($media instanceof MediaInterface) {
      $this->ensureMediaMetadata($media, $alt, $credit);
      return $id;
    }

    $file = File::load($id);
    if (!$file instanceof FileInterface) {
      return NULL;
    }

    return $this->createImageMediaFromFile($file, $alt, $credit);
  }

  private function ensureMediaMetadata(MediaInterface $media, string $alt, string $credit): void {
    $changed = FALSE;

    if ($credit !== '' && $media->hasField('field_credit') && $media->get('field_credit')->isEmpty()) {
      $media->set('field_credit', $credit);
      $changed = TRUE;
    }

    if ($alt !== '' && $media->hasField('field_media_image') && !$media->get('field_media_image')->isEmpty()) {
      $values = $media->get('field_media_image')->getValue();
      if (trim((string) ($values[0]['alt'] ?? '')) === '') {
        $values[0]['alt'] = $alt;
        $media->set('field_media_image', $values);
        $changed = TRUE;
      }
    }

    if ($changed) {
      $media->save();
    }
  }

  private function createImageMediaFromFile(FileInterface $file, string $alt, string $credit): int {
    $file->setPermanent();
    $file->save();

    $values = [
      'bundle' => 'image',
      'name' => $file->getFilename(),
      'status' => 1,
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $alt,
      ],
    ];

    if ($credit !== '') {
      $values['field_credit'] = $credit;
    }

    $media = Media::create($values);
    $media->save();

    return (int) $media->id();
  }

}
