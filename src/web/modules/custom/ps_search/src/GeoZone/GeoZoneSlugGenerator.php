<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

/**
 * Normalizes human labels into URL slugs aligned with SearchSeoLocalityPathBuilder.
 */
final class GeoZoneSlugGenerator {

  /**
   * Builds a slug from a label and optional administrative code suffix.
   */
  public function build(string $label, ?string $code = NULL): string {
    $slug = $this->toSlug($label);
    if ($code !== NULL && $code !== '') {
      $slug = $slug !== '' ? $slug . '-' . strtolower($code) : strtolower($code);
    }

    return $slug;
  }

  /**
   * Converts a label to a URL slug.
   */
  public function toSlug(string $value): string {
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii === FALSE) {
      $ascii = $value;
    }
    $ascii = strtolower($ascii);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $ascii);

    return trim((string) $slug, '-');
  }

}
