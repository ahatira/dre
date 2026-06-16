<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Drupal\default_content\Normalizer\ContentEntityNormalizerInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\user\EntityOwnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Imports demo file + media entities from ps_demo/content (bnppre.fr assets).
 */
final class DemoHeroMediaImporter {

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly ContentEntityNormalizerInterface $contentEntityNormalizer,
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * Creates the importer from the container.
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('extension.list.module'),
      $container->get('entity.repository'),
      $container->get('default_content.content_entity_normalizer'),
      $container->get('file_system'),
    );
  }

  /**
   * Imports or refreshes all demo file/media YAML exports under content/.
   */
  public function importIfMissing(): void {
    $contentPath = $this->moduleExtensionList->getPath('ps_demo') . '/content';
    $rootUser = \Drupal::entityTypeManager()->getStorage('user')->load(1);

    foreach ($this->listYamlExports($contentPath . '/file') as $yaml) {
      $this->importOrRefreshEntity('file', $yaml, $rootUser);
    }

    foreach ($this->listYamlExports($contentPath . '/media') as $yaml) {
      $this->importOrRefreshEntity('media', $yaml, $rootUser);
    }
  }

  /**
   * @return list<string>
   */
  private function listYamlExports(string $directory): array {
    if (!is_dir($directory)) {
      return [];
    }

    $paths = [];
    foreach (scandir($directory) ?: [] as $entry) {
      if (!str_ends_with($entry, '.yml')) {
        continue;
      }
      $paths[] = $directory . '/' . $entry;
    }

    sort($paths, SORT_STRING);
    return $paths;
  }

  /**
   * @param \Drupal\user\UserInterface|null $rootUser
   */
  private function importOrRefreshEntity(string $entityType, string $yaml, mixed $rootUser): void {
    if (!is_readable($yaml)) {
      throw new \RuntimeException("Missing demo media export: {$yaml}");
    }

    /** @var array<string, mixed> $decoded */
    $decoded = Yaml::decode((string) file_get_contents($yaml));
    $meta = $decoded['_meta'] ?? NULL;
    if (!is_array($meta)) {
      return;
    }

    $uuid = (string) ($meta['uuid'] ?? '');
    if ($uuid === '') {
      return;
    }

    try {
      $entity = $this->entityRepository->loadEntityByUuid($entityType, $uuid);
    }
    catch (\Exception) {
      $entity = NULL;
    }

    if ($entity === NULL) {
      $entity = $this->contentEntityNormalizer->denormalize($decoded);
      $entity->enforceIsNew(TRUE);
    }
    elseif ($entity instanceof FileInterface) {
      $this->applyFileDefaultsFromYaml($entity, $decoded);
    }

    if ($entity instanceof FileInterface) {
      $this->syncFileBinary($entity, $yaml);
    }

    if ($entity instanceof EntityOwnerInterface && empty($entity->getOwnerId()) && $rootUser) {
      $entity->setOwner($rootUser);
    }

    if ($entity->isNew()) {
      $entity->setSyncing(TRUE);
      $entity->save();
      return;
    }

    if ($entity instanceof FileInterface) {
      $entity->setSyncing(TRUE);
      $entity->save();
      return;
    }

    if ($entity instanceof MediaInterface) {
      /** @var array<string, mixed> $default */
      $default = $decoded['default'] ?? [];
      $imageField = $default['field_media_image'][0] ?? NULL;
      if (is_array($imageField)) {
        $fileTargetId = $entity->get('field_media_image')->target_id;
        if (isset($imageField['entity']) && is_string($imageField['entity'])) {
          try {
            $file = $this->entityRepository->loadEntityByUuid('file', $imageField['entity']);
            if ($file instanceof FileInterface) {
              $fileTargetId = (int) $file->id();
            }
          }
          catch (\Exception) {
            // Keep existing file reference.
          }
        }
        $entity->set('field_media_image', [
          'target_id' => $fileTargetId,
          'alt' => $imageField['alt'] ?? '',
          'title' => $imageField['title'] ?? '',
          'width' => $imageField['width'] ?? NULL,
          'height' => $imageField['height'] ?? NULL,
        ]);
        $entity->setSyncing(TRUE);
        $entity->save();
      }
    }
  }

  /**
   * @param array<string, mixed> $decoded
   */
  private function applyFileDefaultsFromYaml(FileInterface $entity, array $decoded): void {
    /** @var array<string, mixed> $default */
    $default = $decoded['default'] ?? [];
    $filename = $default['filename'][0]['value'] ?? NULL;
    $uri = $default['uri'][0]['value'] ?? NULL;
    if (is_string($filename) && $filename !== '') {
      $entity->setFilename($filename);
    }
    if (is_string($uri) && $uri !== '') {
      $entity->setFileUri($uri);
    }
  }

  private function syncFileBinary(FileInterface $entity, string $yaml): void {
    $source = dirname($yaml) . '/' . $entity->getFilename();
    if (!is_readable($source)) {
      return;
    }

    $targetDirectory = $this->fileSystem->dirname($entity->getFileUri());
    $this->fileSystem->prepareDirectory($targetDirectory, FileSystemInterface::CREATE_DIRECTORY);
    $newUri = $this->fileSystem->copy($source, $entity->getFileUri(), FileSystemInterface::EXISTS_REPLACE);
    $entity->setFileUri($newUri);
    $entity->set('filesize', filesize($source));
    $entity->set('filemime', mime_content_type($source) ?: $entity->getMimeType());
  }

}
