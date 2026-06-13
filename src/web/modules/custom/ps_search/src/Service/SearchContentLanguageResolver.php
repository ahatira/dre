<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves Search API langcode filters with indexed-offer fallback.
 *
 * When the UI/API requests a language with no published offers (e.g. EN UI
 * while all offers are FR-only), queries fall back to languages that have
 * offer content instead of returning zero results.
 */
final class SearchContentLanguageResolver {

  private const CACHE_ID = 'ps_search:offer_content_langcodes';

  private const CACHE_TAGS = ['node_list:offer'];

  private const CACHE_MAX_AGE = 3600;

  public function __construct(
    private readonly Connection $database,
    private readonly LanguageManagerInterface $languageManager,
    private readonly CacheBackendInterface $cache,
  ) {}

  /**
   * Returns langcodes to use for Search API business-filter queries.
   *
   * @return list<string>
   *   One or more langcodes; never empty when offers exist in any language.
   */
  public function resolveSearchLangcodes(Request $request): array {
    $primary = $this->resolvePrimaryLangcode($request);
    $available = $this->getOfferContentLangcodes();

    if ($available === []) {
      return $primary !== '' ? [$primary] : [];
    }

    if ($primary !== '' && in_array($primary, $available, TRUE)) {
      return [$primary];
    }

    return $available;
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

  /**
   * Returns distinct langcodes of published offer nodes.
   *
   * @return list<string>
   *   Langcodes with at least one published offer.
   */
  private function getOfferContentLangcodes(): array {
    $cached = $this->cache->get(self::CACHE_ID);
    if ($cached !== FALSE) {
      return $cached->data;
    }

    $langcodes = $this->database->select('node_field_data', 'n')
      ->fields('n', ['langcode'])
      ->condition('n.type', 'offer')
      ->condition('n.status', 1)
      ->distinct()
      ->orderBy('langcode')
      ->execute()
      ->fetchCol();

    $langcodes = array_values(array_filter(array_map('strval', $langcodes)));

    $this->cache->set(
      self::CACHE_ID,
      $langcodes,
      time() + self::CACHE_MAX_AGE,
      self::CACHE_TAGS,
    );

    return $langcodes;
  }

}
