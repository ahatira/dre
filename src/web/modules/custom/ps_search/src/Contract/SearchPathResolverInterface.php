<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

/**
 * Resolves public search paths for links (SEO-aware in lot L2).
 */
interface SearchPathResolverInterface {

  /**
   * Returns the internal user-facing search base path.
   *
   * Example: /find-property (no language prefix; Drupal Url handles prefix).
   */
  public function getPublicPath(): string;

}
