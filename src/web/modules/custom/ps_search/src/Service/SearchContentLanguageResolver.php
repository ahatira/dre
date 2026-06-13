<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves Search API content language for search queries.
 *
 * Each UI language only queries offers indexed in that langcode — no fallback
 * to other languages when translations are missing.
 */
final class SearchContentLanguageResolver {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns langcodes to use for Search API business-filter queries.
   *
   * @return list<string>
   *   Exactly one langcode when resolved, or empty when unknown.
   */
  public function resolveSearchLangcodes(Request $request): array {
    $primary = $this->resolvePrimaryLangcode($request);
    return $primary !== '' ? [$primary] : [];
  }

  /**
   * Resolves the requested content language from the HTTP request.
   */
  public function resolvePrimaryLangcode(Request $request): string {
    $langParam = strtolower(trim((string) $request->query->get('lang', '')));
    if ($langParam !== '' && $this->languageManager->getLanguage($langParam) !== NULL) {
      return $langParam;
    }

    return $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
  }

}
