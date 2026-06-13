<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\ps_search\Service\SearchPathResolver;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects /find-property?operation_type=LOC[&asset_type=BUR][&locality=Paris]
 * to the canonical SEO URL /a-louer[/bureaux][/paris]/.
 *
 * Asset-only flexible URLs (/recherche-immobiliere?asset_type=COW) canonicalize
 * to the default rent operation (LOC → /a-louer/coworking/).
 *
 * Also 301-redirects the legacy slug /recherche when it is not the current
 * language search path (e.g. EN bookmarks after migration to /find-property).
 *
 * Priority: 31 — runs after RouterListener (32) and before
 * RouteNormalizerRequestSubscriber (30). AJAX requests are skipped.
 */
final class SearchCanonicalRedirectSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [['onRequest', 31]],
    ];
  }

  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();

    // Skip AJAX / XHR requests (BEF autosubmit, Views AJAX pagination, etc.).
    if ($request->isXmlHttpRequest()) {
      return;
    }

    $pathInfo = $request->getPathInfo();

    // --- Case 1: path is already a SEO URL → set _disable_route_normalizer ---
    // Drupal's RouteProvider caches the route collection. On cache hits it skips
    // processInbound(), so _disable_route_normalizer is never set by the path
    // processor. We set it here (priority 31, every request, no caching) instead.
    //
    // Match /[lang]/operation-slug[/...] patterns.
    if (preg_match('#^((?:/[a-z]{2,8}(?:-[a-z]{2,4})?)?)/([-a-z]+)((?:/[-a-z]*)*?)/?$#', $pathInfo, $seoCheck)) {
      $langPrefix = $seoCheck[1];
      $firstSegment = strtolower($seoCheck[2]);
      $restSegments = trim($seoCheck[3] ?? '', '/');
      $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();
      $m = $this->getMappings($langcode);
      if (in_array($firstSegment, $m['op'], TRUE)) {
        $restParts = $restSegments !== '' ? explode('/', $restSegments) : [];
        if ($restParts !== []) {
          $aliases = $this->searchPathResolver->getAssetSlugAliases($langcode);
          $assetSegment = strtolower($restParts[0]);
          if (isset($aliases[$assetSegment])) {
            $restParts[0] = $aliases[$assetSegment];
            $target = $langPrefix . '/' . $firstSegment . '/' . implode('/', $restParts) . '/';
            $queryString = $request->getQueryString();
            if ($queryString !== NULL && $queryString !== '') {
              $target .= '?' . $queryString;
            }
            $event->setResponse(new RedirectResponse($target, 301));
            return;
          }
        }

        // Check if asset_type query param needs to be incorporated into the path.
        $rawAssetParam = $request->query->all()['asset_type'] ?? NULL;
        if ($rawAssetParam !== NULL) {
          $assetType = is_array($rawAssetParam) ? array_key_first($rawAssetParam) : $rawAssetParam;
          if (is_string($assetType) && !empty($assetType)) {
            $assetSlug = $m['asset'][strtoupper($assetType)] ?? NULL;
            // Only redirect if the asset slug is not already in the path.
            if ($assetSlug !== NULL && strpos($restSegments, $assetSlug) === FALSE) {
              $seoPath = $langPrefix . '/' . $firstSegment . '/' . $assetSlug . '/';
              $remainingQuery = $request->query->all();
              unset($remainingQuery['asset_type'], $remainingQuery['operation_type']);
              if (!empty($remainingQuery)) {
                $seoPath .= '?' . http_build_query($remainingQuery);
              }
              $event->setResponse(new RedirectResponse($seoPath, 301));
              return;
            }
          }
        }
        $request->attributes->set('_disable_route_normalizer', TRUE);
        return;
      }
    }

    // Match /[lang]/search-slug (any configured or legacy search path segment).
    if (!preg_match('#^((?:/[a-z]{2,8}(?:-[a-z]{2,4})?)?)/([^/]+)$#', $pathInfo, $matches)) {
      return;
    }

    $langPrefix = $matches[1] ?? '';
    $segment = strtolower($matches[2]);
    $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();

    if (!$this->searchPathResolver->isSearchPathSegment($segment)) {
      return;
    }

    $currentSlug = $this->searchPathResolver->getSlugForLang($langcode);
    if ($segment === $this->searchPathResolver->getLegacySlug() && $segment !== $currentSlug) {
      $target = $langPrefix . '/' . $currentSlug;
      $queryString = $request->getQueryString();
      if ($queryString !== NULL && $queryString !== '') {
        $target .= '?' . $queryString;
      }
      $event->setResponse(new RedirectResponse($target, 301));
      return;
    }

    $operationType = $this->extractFacetQueryValue($request, 'operation_type');
    $assetType = $this->extractFacetQueryValue($request, 'asset_type');

    if ($operationType === NULL && $assetType === NULL) {
      return;
    }

    // Asset-only flexible URLs canonicalize to default rent (LOC / for-rent).
    if ($operationType === NULL) {
      $operationType = 'LOC';
    }

    // Detect URL language from path prefix (not interface/admin preference).
    $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();
    $m = $this->getMappings($langcode);

    $opSlug = $m['op'][strtoupper($operationType)] ?? NULL;
    if ($opSlug === NULL) {
      return;
    }

    $seoPath = $langPrefix . '/' . $opSlug;

    if ($assetType !== NULL) {
      $assetSlug = $m['asset'][strtoupper($assetType)] ?? NULL;
      if ($assetSlug !== NULL) {
        $seoPath .= '/' . $assetSlug;
      }
    }

    $localityRaw = $request->query->all()['locality'] ?? NULL;
    $tokens = is_array($localityRaw)
      ? array_values(array_filter(array_map('strval', $localityRaw)))
      : (is_string($localityRaw) && $localityRaw !== '' ? $this->extractLocationTokens($localityRaw) : []);

    if ($tokens !== []) {
      if (count($tokens) === 1) {
        $seoPath = $this->seoLocalityPathBuilder->appendSegmentsToPath($seoPath, $tokens[0]);
      }
    }

    $seoPath .= '/';

    // Preserve remaining query params (budget, surface, keywords, etc.).
    $remainingQuery = $request->query->all();
    unset($remainingQuery['operation_type'], $remainingQuery['asset_type']);
    if (count($tokens) <= 1) {
      unset($remainingQuery['locality'], $remainingQuery['locations']);
    }
    elseif ($tokens !== []) {
      $remainingQuery['locations'] = implode(',', $tokens);
      unset($remainingQuery['locality']);
    }
    if (!empty($remainingQuery)) {
      $seoPath .= '?' . http_build_query($remainingQuery);
    }

    $event->setResponse(new RedirectResponse($seoPath, 301));
  }

  private function getMappings(string $langcode): array {
    $m = $this->searchPathResolver->getSeoSlugMappings($langcode);
    return [
      'op' => $m['val_to_op'],
      'asset' => $m['val_to_asset'],
    ];
  }

  private function getDefaultLangcode(): string {
    return \Drupal::languageManager()->getDefaultLanguage()->getId();
  }

  /**
   * Reads a facet query value (scalar or BEF array format).
   */
  private function extractFacetQueryValue(Request $request, string $key): ?string {
    $raw = $request->query->all()[$key] ?? NULL;
    if (is_array($raw)) {
      $value = array_key_first($raw);
      return is_string($value) && $value !== '' ? $value : NULL;
    }
    if (is_string($raw) && $raw !== '') {
      return $raw;
    }
    return NULL;
  }

  /**
   * Parses and sanitizes a comma/semicolon-separated location list.
   *
   * @return string[]
   *   A deduplicated list of up to 10 location tokens.
   */
  private function extractLocationTokens(string $value): array {
    $parts = preg_split('/[,;]+/', $value) ?: [];
    $tokens = [];

    foreach ($parts as $part) {
      $cleaned = trim($part);
      if ($cleaned === '') {
        continue;
      }
      $key = mb_strtolower($cleaned);
      $tokens[$key] = $cleaned;
      if (count($tokens) >= 10) {
        break;
      }
    }

    return array_values($tokens);
  }

}
