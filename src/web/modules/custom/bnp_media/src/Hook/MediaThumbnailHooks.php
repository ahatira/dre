<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\file\Entity\File;
use Drupal\media\MediaInterface;

/**
 * Hook implementations for media thumbnail normalization.
 */
final class MediaThumbnailHooks {

  /**
   * Guard against recursive save loops when normalizing thumbnails post-save.
   *
   * @var array<int, bool>
   */
  private static array $postSaveGuard = [];

  /**
   * File extension specific placeholders.
   *
   * @var array<string, string>
   */
  private const FILE_EXTENSION_MAP = [
    'pdf' => 'bnp_file_pdf.svg',
    'doc' => 'bnp_file_doc.svg',
    'docx' => 'bnp_file_doc.svg',
    'odt' => 'bnp_file_doc.svg',
    'rtf' => 'bnp_file_doc.svg',
    'xls' => 'bnp_file_sheet.svg',
    'xlsx' => 'bnp_file_sheet.svg',
    'csv' => 'bnp_file_sheet.svg',
    'ppt' => 'bnp_file_slide.svg',
    'pptx' => 'bnp_file_slide.svg',
    'zip' => 'bnp_file_zip.svg',
    'txt' => 'bnp_file_text.svg',
    'md' => 'bnp_file_text.svg',
    'json' => 'bnp_file_text.svg',
    'xml' => 'bnp_file_text.svg',
  ];

  /**
   * Bundle-level placeholder mapping.
   *
   * @var array<string, string>
   */
  private const BUNDLE_MAP = [
    'audio' => 'bnp_audio_file.svg',
    'video' => 'bnp_video_file.svg',
    'remote_video' => 'bnp_remote_video.svg',
    'mediahub_video' => 'bnp_mediahub_video.svg',
    'visite_guided' => 'bnp_virtual_tour.svg',
  ];

  public function __construct(
    private readonly FileSystemInterface $fileSystem,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  /**
   * Implements hook_entity_presave().
   */
  #[Hook('entity_presave')]
  public function onEntityPresave(EntityInterface $entity): void {
    if (!$entity instanceof MediaInterface || !$entity->hasField('thumbnail')) {
      return;
    }

    $bundle = $entity->bundle();
    $placeholder = $this->resolvePlaceholderFilename($entity, $bundle);
    if ($placeholder === NULL) {
      return;
    }

    if (!$this->shouldApplyPlaceholder($entity, $bundle)) {
      return;
    }

    $thumbnail_file = $this->ensurePlaceholderFile($placeholder);
    if ($thumbnail_file === NULL) {
      return;
    }

    $entity->set('thumbnail', [
      'target_id' => $thumbnail_file->id(),
      'alt' => $this->buildAltText($bundle),
    ]);
  }

  /**
   * Implements hook_entity_insert().
   */
  #[Hook('entity_insert')]
  public function onEntityInsert(EntityInterface $entity): void {
    $this->normalizeThumbnailPostSave($entity);
  }

  /**
   * Implements hook_entity_update().
   */
  #[Hook('entity_update')]
  public function onEntityUpdate(EntityInterface $entity): void {
    $this->normalizeThumbnailPostSave($entity);
  }

  /**
   * Resolves placeholder image filename for the given media bundle.
   */
  private function resolvePlaceholderFilename(MediaInterface $media, string $bundle): ?string {
    if ($bundle === 'file' && $media->hasField('field_media_file')) {
      $file = $media->get('field_media_file')->entity;
      $filename = $file?->getFilename() ?? '';
      $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      return self::FILE_EXTENSION_MAP[$extension] ?? 'bnp_file_generic.png';
    }

    return self::BUNDLE_MAP[$bundle] ?? NULL;
  }

  /**
   * Determines whether a placeholder should replace current thumbnail.
   */
  private function shouldApplyPlaceholder(MediaInterface $media, string $bundle): bool {
    $thumbnail_file = $media->get('thumbnail')->entity;
    if (!$thumbnail_file) {
      return TRUE;
    }

    if ($bundle === 'file') {
      return TRUE;
    }

    $uri = (string) $thumbnail_file->getFileUri();
    if ($uri === '') {
      return TRUE;
    }

    return str_contains($uri, 'media-icons/generic/') || str_contains($uri, 'public://bnp-media/placeholders/');
  }

  /**
   * Copies placeholder image to public files and returns managed file entity.
   */
  private function ensurePlaceholderFile(string $filename): ?File {
    $module_path = $this->extensionPathResolver->getPath('module', 'bnp_media');
    $source_path = $module_path . '/icons/' . $filename;
    if (!is_file($source_path)) {
      return NULL;
    }

    $directory = 'public://bnp-media/placeholders';
    $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

    $destination = $directory . '/' . $filename;
    if (!file_exists($this->fileSystem->realpath($destination) ?: '')) {
      $this->fileSystem->copy($source_path, $destination, FileSystemInterface::EXISTS_REPLACE);
    }

    $existing = $this->entityTypeManager
      ->getStorage('file')
      ->loadByProperties(['uri' => $destination]);
    if ($existing) {
      $file = reset($existing);
      return $file instanceof File ? $file : NULL;
    }

    $file = File::create([
      'uri' => $destination,
      'status' => 1,
    ]);
    $file->setPermanent();
    $file->save();

    return $file;
  }

  /**
   * Builds consistent alt text for fallback thumbnails.
   */
  private function buildAltText(string $bundle): string {
    return match ($bundle) {
      'audio' => 'Audio placeholder',
      'video' => 'Video placeholder',
      'remote_video' => 'Remote video placeholder',
      'mediahub_video' => 'MediaHub video placeholder',
      'visite_guided' => 'Virtual tour placeholder',
      'file' => 'Document placeholder',
      default => 'Media placeholder',
    };
  }

  /**
   * Enforces placeholder thumbnail after entity save if needed.
   */
  private function normalizeThumbnailPostSave(EntityInterface $entity): void {
    if (!$entity instanceof MediaInterface || !$entity->hasField('thumbnail')) {
      return;
    }

    $media_id = (int) $entity->id();
    if ($media_id > 0 && isset(self::$postSaveGuard[$media_id])) {
      return;
    }

    $bundle = $entity->bundle();
    $placeholder = $this->resolvePlaceholderFilename($entity, $bundle);
    if ($placeholder === NULL) {
      return;
    }

    if (!$this->shouldApplyPlaceholder($entity, $bundle)) {
      return;
    }

    $thumbnail_file = $this->ensurePlaceholderFile($placeholder);
    if ($thumbnail_file === NULL) {
      return;
    }

    $current_target_id = (int) ($entity->get('thumbnail')->target_id ?? 0);
    if ($current_target_id === (int) $thumbnail_file->id()) {
      return;
    }

    if ($media_id > 0) {
      self::$postSaveGuard[$media_id] = TRUE;
    }

    $entity->set('thumbnail', [
      'target_id' => $thumbnail_file->id(),
      'alt' => $this->buildAltText($bundle),
    ]);
    $entity->save();

    if ($media_id > 0) {
      unset(self::$postSaveGuard[$media_id]);
    }
  }

}
