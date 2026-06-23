<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Symfony\Component\Yaml\Yaml;

/**
 * French administrative regions referential (slug, label, departments).
 *
 * Used for SEO region URLs (/ile-de-france/) and region-level search filters.
 * Other countries use geo zone config (type region) via GeoZoneRepository.
 */
final class AdministrativeRegionRegistry {

  public const TOKEN_PREFIX = 'region:';

  /**
   * @var array<string, array{slug: string, label: string, departments: list<string>}>|null
   */
  private ?array $bySlug = NULL;

  /**
   * @var array<string, string>|null
   */
  private ?array $departmentToRegionSlug = NULL;

  public function __construct(
    private readonly string $moduleRelativePath,
  ) {}

  /**
   * Returns a region definition by URL slug, or NULL when unknown.
   *
   * @return array{slug: string, label: string, departments: list<string>}|null
   */
  public function findBySlug(string $slug): ?array {
    $slug = strtolower(trim($slug));
    if ($slug === '') {
      return NULL;
    }

    return $this->loadBySlug()[$slug] ?? NULL;
  }

  /**
   * Returns the region label for a department code, or NULL when unknown.
   */
  public function getRegionLabelForDepartment(string $departmentCode): ?string {
    $region = $this->findRegionForDepartment($departmentCode);

    return $region['label'] ?? NULL;
  }

  /**
   * Returns the region slug for a department code, or NULL when unknown.
   */
  public function getRegionSlugForDepartment(string $departmentCode): ?string {
    $code = strtoupper(trim($departmentCode));
    if ($code === '') {
      return NULL;
    }

    $slug = $this->loadDepartmentIndex()[$code] ?? NULL;

    return $slug !== NULL ? $this->findBySlug($slug)['slug'] ?? $slug : NULL;
  }

  /**
   * @return array{slug: string, label: string, departments: list<string>}|null
   */
  public function findRegionForDepartment(string $departmentCode): ?array {
    $slug = $this->getRegionSlugForDepartment($departmentCode);

    return $slug !== NULL ? $this->findBySlug($slug) : NULL;
  }

  /**
   * Builds the internal location filter token for a region slug.
   */
  public function buildRegionToken(string $slug): string {
    return self::TOKEN_PREFIX . strtolower(trim($slug));
  }

  /**
   * Whether a location token represents an administrative region.
   */
  public function isRegionToken(string $token): bool {
    return str_starts_with($token, self::TOKEN_PREFIX);
  }

  /**
   * Extracts the region slug from a region token.
   */
  public function parseRegionToken(string $token): ?string {
    if (!$this->isRegionToken($token)) {
      return NULL;
    }

    $slug = substr($token, strlen(self::TOKEN_PREFIX));

    return trim($slug) !== '' ? $slug : NULL;
  }

  /**
   * @return list<array{slug: string, label: string, departments: list<string>}>
   */
  public function searchByLabelPrefix(string $query, int $limit = 5): array {
    $needle = $this->normalizeForSearch($query);
    if ($needle === '') {
      return [];
    }

    $matches = [];
    foreach ($this->loadBySlug() as $region) {
      if ($this->matchesSearchNeedle($needle, $region['label'], $region['slug'])) {
        $matches[] = $region;
      }
    }

    usort($matches, static function (array $a, array $b): int {
      return strcasecmp($a['label'], $b['label']);
    });

    return array_slice($matches, 0, max(1, $limit));
  }

  /**
   * Normalizes text for accent-insensitive prefix search.
   */
  public function normalizeForSearch(string $value): string {
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii === FALSE) {
      $ascii = $value;
    }
    $ascii = strtolower($ascii);
    $ascii = preg_replace('/[^a-z0-9]+/', '-', $ascii);

    return trim((string) $ascii, '-');
  }

  /**
   * Whether a label or slug matches a normalized search needle.
   */
  public function matchesSearchNeedle(string $needle, string $label, string $slug): bool {
    if ($needle === '') {
      return FALSE;
    }

    $labelNorm = $this->normalizeForSearch($label);
    $slugNorm = $this->normalizeForSearch(str_replace('-', ' ', $slug));

    return str_starts_with($labelNorm, $needle)
      || str_contains($labelNorm, '-' . $needle)
      || str_starts_with($slugNorm, $needle)
      || str_contains($slugNorm, $needle);
  }

  /**
   * @return array<string, array{slug: string, label: string, departments: list<string>}>
   */
  private function loadBySlug(): array {
    if ($this->bySlug !== NULL) {
      return $this->bySlug;
    }

    $path = DRUPAL_ROOT . '/' . trim($this->moduleRelativePath, '/') . '/data/regions/fr.yml';
    if (!is_file($path)) {
      $this->bySlug = [];
      return $this->bySlug;
    }

    $parsed = Yaml::parseFile($path);
    if (!is_array($parsed)) {
      $this->bySlug = [];
      return $this->bySlug;
    }

    $this->bySlug = [];
    foreach ($parsed as $slug => $data) {
      if (!is_string($slug) || !is_array($data)) {
        continue;
      }
      $label = trim((string) ($data['label'] ?? ''));
      $departments = [];
      foreach ($data['departments'] ?? [] as $code) {
        if (is_string($code) || is_numeric($code)) {
          $normalized = strtoupper(trim((string) $code));
          if ($normalized !== '') {
            $departments[] = $normalized;
          }
        }
      }
      if ($label === '' || $departments === []) {
        continue;
      }
      $normalizedSlug = strtolower(trim($slug));
      $this->bySlug[$normalizedSlug] = [
        'slug' => $normalizedSlug,
        'label' => $label,
        'departments' => $departments,
      ];
    }

    return $this->bySlug;
  }

  /**
   * @return array<string, string>
   */
  private function loadDepartmentIndex(): array {
    if ($this->departmentToRegionSlug !== NULL) {
      return $this->departmentToRegionSlug;
    }

    $this->departmentToRegionSlug = [];
    foreach ($this->loadBySlug() as $slug => $region) {
      foreach ($region['departments'] as $departmentCode) {
        $this->departmentToRegionSlug[$departmentCode] = $slug;
      }
    }

    return $this->departmentToRegionSlug;
  }

}
