<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

/**
 * Builds and parses BNPPRE-style locality segments in SEO search URLs.
 *
 * Single location examples:
 * - /a-vendre/bureau/paris-75/paris-17-75017/
 * - /a-vendre/bureau/paris-75/
 *
 * Multiple locations stay in the ?locations= query string.
 */
final class SearchSeoLocalityPathBuilder {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
  ) {}

  /**
   * Converts a filter token into dept/city URL path segments.
   *
   * @return array{dept?: string, city?: string}
   *   Path segments without leading/trailing slashes.
   */
  public function tokenToPathSegments(string $token): array {
    $meta = $this->locationSearchFilter->resolveTokenMetadata($token);
    $segments = [];

    if (($meta['type'] ?? '') === 'department') {
      $deptCode = preg_match('/^\d{2,3}$/', $token) === 1 ? $token : '';
      $deptName = (string) ($meta['admin_area'] ?? '');
      if ($deptName !== '' && $deptCode !== '') {
        $segments['dept'] = $this->toSlug($deptName) . '-' . $deptCode;
      }
      return $segments;
    }

    $postalCode = (string) ($meta['postal_code'] ?? '');
    $deptCode = $postalCode !== '' ? substr($postalCode, 0, 2) : '';
    $deptName = (string) ($meta['admin_area'] ?? '');
    if ($deptName !== '' && $deptCode !== '') {
      $segments['dept'] = $this->toSlug($deptName) . '-' . $deptCode;
    }

    $locality = (string) ($meta['locality'] ?? '');
    if ($locality === '') {
      return $segments;
    }

    $citySlug = $this->toSlug($locality);
    if (($meta['type'] ?? '') === 'arrondissement' && $postalCode !== '') {
      $arrNum = (int) substr($postalCode, -2);
      $segments['city'] = $citySlug . '-' . $arrNum . '-' . $postalCode;
      return $segments;
    }

    $segments['city'] = $postalCode !== '' ? $citySlug . '-' . $postalCode : $citySlug;
    return $segments;
  }

  /**
   * Appends BNPPRE locality segments to an SEO path prefix.
   */
  public function appendSegmentsToPath(string $seoPath, string $token): string {
    $segments = $this->tokenToPathSegments($token);
    if (isset($segments['dept'])) {
      $seoPath .= '/' . $segments['dept'];
    }
    if (isset($segments['city'])) {
      $seoPath .= '/' . $segments['city'];
    }
    return $seoPath;
  }

  /**
   * Resolves inbound SEO path segments to a location filter token.
   */
  public function pathSegmentsToToken(?string $deptSegment, ?string $citySegment): ?string {
    if ($citySegment !== NULL && $citySegment !== '') {
      return $this->citySegmentToToken($citySegment);
    }
    if ($deptSegment !== NULL && $deptSegment !== '') {
      return $this->deptSegmentToToken($deptSegment);
    }
    return NULL;
  }

  /**
   * Parses a dept segment such as paris-75 into a department token.
   */
  public function deptSegmentToToken(string $segment): ?string {
    if (preg_match('/-(\d{2,3})$/', $segment, $matches) !== 1) {
      return NULL;
    }
    return $matches[1];
  }

  /**
   * Parses a city segment into a location filter token.
   */
  public function citySegmentToToken(string $segment): ?string {
    $parts = explode('-', $segment);
    if ($parts === []) {
      return NULL;
    }

    $postal = array_pop($parts);
    if (preg_match('/^\d{5}$/', $postal) === 1) {
      if ($parts !== [] && is_numeric($parts[count($parts) - 1])) {
        array_pop($parts);
      }
      return $postal;
    }

    return $this->slugToLabel(implode('-', $parts));
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

  /**
   * Converts a URL slug back to a human label.
   */
  private function slugToLabel(string $slug): string {
    return ucwords(str_replace('-', ' ', $slug));
  }

}
