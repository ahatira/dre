<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityOwnerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\default_content\Normalizer\ContentEntityNormalizerInterface;
use Drupal\file\FileInterface;

/**
 * Imports missing ps_demo YAML entities without re-importing the full package.
 */
final class DemoPartialContentImporter {

  /**
   * Editorial demo UUIDs (media assets → taxonomy → articles → market studies).
   *
   * @var list<string>
   */
  private const CONTENT_FILES = [
    'file/c1000001-0000-4000-8000-000000000001.yml',
    'file/c1000003-0000-4000-8000-000000000001.yml',
    'file/c1000005-0000-4000-8000-000000000001.yml',
    'file/c1000007-0000-4000-8000-000000000001.yml',
    'file/c1000009-0000-4000-8000-000000000001.yml',
    'file/c1000011-0000-4000-8000-000000000001.yml',
    'file/c1000013-0000-4000-8000-000000000001.yml',
    'media/c1000002-0000-4000-8000-000000000001.yml',
    'media/c1000004-0000-4000-8000-000000000001.yml',
    'media/c1000006-0000-4000-8000-000000000001.yml',
    'media/c1000008-0000-4000-8000-000000000001.yml',
    'media/c1000010-0000-4000-8000-000000000001.yml',
    'media/c1000012-0000-4000-8000-000000000001.yml',
    'media/c1000014-0000-4000-8000-000000000001.yml',
    'taxonomy_term/b3000001-0000-4000-8000-000000000001.yml',
    'taxonomy_term/b3000001-0000-4000-8000-000000000002.yml',
    'taxonomy_term/b3000002-0000-4000-8000-000000000001.yml',
    'taxonomy_term/b3000002-0000-4000-8000-000000000002.yml',
    'node/b2000005-0000-4000-8000-000000000001.yml',
    'node/b2000005-0000-4000-8000-000000000002.yml',
    'node/b2000005-0000-4000-8000-000000000003.yml',
    'node/b2000006-0000-4000-8000-000000000001.yml',
    'node/b2000006-0000-4000-8000-000000000002.yml',
    'node/b2000006-0000-4000-8000-000000000003.yml',
  ];

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContentEntityNormalizerInterface $contentEntityNormalizer,
  ) {}

  /**
   * Imports editorial demo YAML when any listed UUID is missing.
   */
  public function importEditorialContentIfMissing(): int {
    if (!$this->isAnyEditorialUuidMissing()) {
      return 0;
    }

    $rootUser = $this->entityTypeManager->getStorage('user')->load(1);
    if ($rootUser === NULL) {
      return 0;
    }

    $modulePath = $this->moduleExtensionList->getPath('ps_demo');
    $imported = 0;

    foreach (self::CONTENT_FILES as $relativePath) {
      $absolutePath = DRUPAL_ROOT . '/' . $modulePath . '/content/' . $relativePath;
      if (!is_readable($absolutePath)) {
        continue;
      }

      /** @var array<string, mixed> $decoded */
      $decoded = Yaml::decode((string) file_get_contents($absolutePath));
      $meta = $decoded['_meta'] ?? NULL;
      if (!is_array($meta)) {
        continue;
      }

      $uuid = (string) ($meta['uuid'] ?? '');
      $entityTypeId = (string) ($meta['entity_type'] ?? '');
      if ($uuid === '' || $entityTypeId === '') {
        continue;
      }

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
        $entity = $this->contentEntityNormalizer->denormalize($decoded);
        $entity->enforceIsNew(TRUE);
        if ($entity instanceof EntityOwnerInterface && empty($entity->getOwnerId())) {
          $entity->setOwner($rootUser);
        }
        if ($entity instanceof FileInterface) {
          continue;
        }

        $entity->setSyncing(TRUE);
        $entity->save();
        $imported++;
      }
      catch (\Throwable $exception) {
        \Drupal::logger('ps_demo')->error('Editorial demo import failed for @file: @message', [
          '@file' => $relativePath,
          '@message' => $exception->getMessage(),
        ]);
      }
    }

    return $imported;
  }

  private function isAnyEditorialUuidMissing(): bool {
    foreach (self::CONTENT_FILES as $relativePath) {
      $absolutePath = DRUPAL_ROOT . '/' . $this->moduleExtensionList->getPath('ps_demo') . '/content/' . $relativePath;
      if (!is_readable($absolutePath)) {
        continue;
      }

      /** @var array<string, mixed> $decoded */
      $decoded = Yaml::decode((string) file_get_contents($absolutePath));
      $meta = $decoded['_meta'] ?? NULL;
      if (!is_array($meta)) {
        continue;
      }

      $uuid = (string) ($meta['uuid'] ?? '');
      $entityTypeId = (string) ($meta['entity_type'] ?? '');
      if ($uuid === '' || $entityTypeId === '') {
        continue;
      }

      try {
        $existing = $this->entityRepository->loadEntityByUuid($entityTypeId, $uuid);
        if ($existing === NULL) {
          return TRUE;
        }
      }
      catch (\Exception) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
