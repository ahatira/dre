<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_compare\Contract\ComparePathResolverInterface;

/**
 * Resolves internal and public (translated) compare page path slugs.
 */
final class ComparePathResolver implements ComparePathResolverInterface {

  /**
   * Machine route slug — must match ps_compare.routing.yml (never translated).
   */
  private const MACHINE_ROUTE_SLUG = 'compare';

  /**
   * @var array<string, string>
   */
  private array $slugByLang = [];

  /**
   * @var string[]|null
   */
  private ?array $allSlugs = NULL;

  private ?string $machineSlug = NULL;

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
  ) {}

  /**
   *
   */
  public function getInternalPath(): string {
    return '/' . $this->getMachineComparePathSlug();
  }

  /**
   *
   */
  public function getSlugForLang(string $langcode): string {
    if (isset($this->slugByLang[$langcode])) {
      return $this->slugByLang[$langcode];
    }

    $langConfig = $this->langConfigOverride->getOverride($langcode, 'ps_compare.seo_url_mappings');
    $slug = (string) ($langConfig->get('compare_path') ?? $this->getMachineComparePathSlug());
    $slug = strtolower(trim($slug, '/'));
    $this->slugByLang[$langcode] = $slug !== '' ? $slug : 'compare';
    return $this->slugByLang[$langcode];
  }

  /**
   *
   */
  public function getPublicPath(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    return '/' . $this->getSlugForLang($langcode);
  }

  /**
   *
   */
  public function isComparePath(string $path): bool {
    $stripped = trim($path, '/');
    if ($stripped === '') {
      return FALSE;
    }

    $segments = explode('/', $stripped);
    if (count($segments) !== 1) {
      return FALSE;
    }

    return in_array(strtolower($segments[0]), $this->getAllSlugs(), TRUE);
  }

  /**
   * @return string[]
   *   All configured slugs across languages.
   */
  public function getAllSlugs(): array {
    if ($this->allSlugs !== NULL) {
      return $this->allSlugs;
    }

    $slugs = [];
    foreach ($this->languageManager->getLanguages() as $langcode => $_lang) {
      $slugs[] = $this->getSlugForLang($langcode);
    }

    $this->allSlugs = array_values(array_unique(array_filter($slugs)));
    return $this->allSlugs;
  }

  /**
   *
   */
  private function getMachineComparePathSlug(): string {
    if ($this->machineSlug !== NULL) {
      return $this->machineSlug;
    }

    // Base config can carry a translated slug after config export on multilingual
    // sites — the Drupal route is always /compare (see ps_compare.routing.yml).
    $this->machineSlug = self::MACHINE_ROUTE_SLUG;
    return $this->machineSlug;
  }

}
