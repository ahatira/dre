<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Component\Graph\Graph;
use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityOwnerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\default_content\ContentFileStorageInterface;
use Drupal\default_content\Normalizer\ContentEntityNormalizerInterface;
use Drupal\file\FileInterface;

/**
 * Imports missing ps_demo YAML entities without re-importing the full package.
 */
final class DemoPartialContentImporter {

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContentEntityNormalizerInterface $contentEntityNormalizer,
    private readonly ContentFileStorageInterface $contentFileStorage,
    private readonly FileSystemInterface $fileSystem,
    private readonly AccountSwitcherInterface $accountSwitcher,
  ) {}

  /**
   * Imports all ps_demo content YAML except excluded UUIDs.
   *
   * Used when the homepage shell already exists (same UUID as demo export).
   *
   * @param list<string> $excludeUuids
   *   Entity UUIDs to skip (typically the homepage node).
   */
  public function importPackageIfMissing(array $excludeUuids = []): int {
    $exclude = array_fill_keys($excludeUuids, TRUE);
    $folder = $this->moduleExtensionList->getPath('ps_demo') . '/content';
    if (!is_dir($folder)) {
      return 0;
    }

    $rootUser = $this->entityTypeManager->getStorage('user')->load(1);
    if ($rootUser === NULL) {
      return 0;
    }

    $fileMap = [];
    $graph = [];
    $vertexes = [];

    foreach ($this->entityTypeManager->getDefinitions() as $entityTypeId => $entityType) {
      $reflection = new \ReflectionClass($entityType->getClass());
      if ($reflection->implementsInterface(ConfigEntityInterface::class)) {
        continue;
      }
      $entityFolder = $folder . '/' . $entityTypeId;
      if (!is_dir($entityFolder)) {
        continue;
      }

      foreach ($this->contentFileStorage->scan($entityFolder) as $file) {
        /** @var array<string, mixed> $decoded */
        $decoded = Yaml::decode((string) file_get_contents($file->uri));
        $meta = $decoded['_meta'] ?? NULL;
        if (!is_array($meta)) {
          continue;
        }

        $itemUuid = (string) ($meta['uuid'] ?? '');
        if ($itemUuid === '') {
          continue;
        }

        $file->entity_type_id = $entityTypeId;
        $fileMap[$itemUuid] = $file;

        $vertex = $this->vertex($vertexes, $itemUuid);
        $graph[$vertex->id]['edges'] = [];

        if (empty($decoded['_meta']['depends'])) {
          continue;
        }

        foreach (array_keys($decoded['_meta']['depends']) as $dependsUuid) {
          $edge = $this->vertex($vertexes, (string) $dependsUuid);
          $graph[$vertex->id]['edges'][$edge->id] = TRUE;
        }
      }
    }

    if ($fileMap === []) {
      return 0;
    }

    $graphObject = new Graph($graph);
    $sorted = $graphObject->searchAndSort();
    uasort($sorted, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    $sorted = array_reverse($sorted);

    $this->accountSwitcher->switchTo($rootUser);
    $imported = 0;

    try {
      foreach ($sorted as $uuid => $_details) {
        if (!isset($fileMap[$uuid])) {
          continue;
        }
        if (isset($exclude[$uuid])) {
          continue;
        }

        $file = $fileMap[$uuid];
        $entityTypeId = $file->entity_type_id;

        try {
          $existing = $this->entityRepository->loadEntityByUuid($entityTypeId, $uuid);
          if ($existing !== NULL) {
            continue;
          }
        }
        catch (\Exception) {
          // Missing entity — import below.
        }

        try {
          /** @var array<string, mixed> $decoded */
          $decoded = Yaml::decode((string) file_get_contents($file->uri));
          $entity = $this->contentEntityNormalizer->denormalize($decoded);
          $entity->enforceIsNew(TRUE);

          if ($entity instanceof EntityOwnerInterface && empty($entity->getOwnerId())) {
            $entity->setOwner($rootUser);
          }

          if ($entity instanceof FileInterface) {
            $fileSource = $this->fileSystem->dirname($file->uri) . '/' . $entity->getFilename();
            if (file_exists($fileSource)) {
              $targetDirectory = $this->fileSystem->dirname($entity->getFileUri());
              $this->fileSystem->prepareDirectory($targetDirectory, FileSystemInterface::CREATE_DIRECTORY);
              $newUri = $this->fileSystem->copy($fileSource, $entity->getFileUri());
              $entity->setFileUri($newUri);
            }
          }

          $entity->setSyncing(TRUE);
          $entity->save();
          $imported++;
        }
        catch (\Throwable $exception) {
          \Drupal::logger('ps_demo')->error('Partial demo import failed for @file: @message', [
            '@file' => $file->uri,
            '@message' => $exception->getMessage(),
          ]);
        }
      }
    }
    finally {
      $this->accountSwitcher->switchBack();
    }

    return $imported;
  }

  /**
   * Returns or creates a graph vertex for the given UUID.
   *
   * @param array<string, object{id: string}> $vertexes
   *   Vertex map keyed by UUID.
   * @param string $itemUuid
   *   Entity UUID.
   *
   * @return object{id: string}
   *   Graph vertex object.
   */
  private function vertex(array &$vertexes, string $itemUuid): object {
    if (!isset($vertexes[$itemUuid])) {
      $vertexes[$itemUuid] = (object) ['id' => $itemUuid];
    }
    return $vertexes[$itemUuid];
  }

}
