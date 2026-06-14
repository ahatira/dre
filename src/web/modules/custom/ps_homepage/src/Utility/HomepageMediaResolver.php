<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\file\Entity\File;

/**
 * Resolves managed file URLs for homepage block media fields.
 */
final class HomepageMediaResolver {

  public function __construct(
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  public function resolveUrl(mixed $fid): ?string {
    $fid = (int) $fid;
    if ($fid <= 0) {
      return NULL;
    }

    $file = File::load($fid);
    if ($file === NULL) {
      return NULL;
    }

    return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
  }

}
