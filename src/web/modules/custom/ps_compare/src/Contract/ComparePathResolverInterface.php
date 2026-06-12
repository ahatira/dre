<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Contract;

/**
 * Resolves internal and public compare page paths.
 */
interface ComparePathResolverInterface {

  /**
   * Internal Drupal route path (leading slash, machine slug).
   */
  public function getInternalPath(): string;

  /**
   * Public slug for a language (no leading slash).
   */
  public function getSlugForLang(string $langcode): string;

  /**
   * Public path for a language (leading slash).
   */
  public function getPublicPath(?string $langcode = NULL): string;

  /**
   * Whether the full path (no language prefix) is a compare page path.
   */
  public function isComparePath(string $path): bool;

}
