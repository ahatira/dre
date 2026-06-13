<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_search\Service\SearchPathResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects SEO search paths that use slugs from another URL language.
 *
 * Example: /a-louer/bureaux/ (FR slugs without /fr prefix)
 *   → /fr/a-louer/bureaux/
 * Example: /recherche-immobiliere (FR flexible slug without /fr prefix)
 *   → /fr/recherche-immobiliere/
 */
final class SearchCrossLanguageSlugRedirectSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      // Before RouterListener (32) so foreign slugs never hit a 404 route.
      KernelEvents::REQUEST => [['onRequest', 33]],
    ];
  }

  /**
   * Redirects foreign SEO operation slugs to their owning URL language.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();
    if ($request->isXmlHttpRequest()) {
      return;
    }

    $pathInfo = $request->getPathInfo();
    if ($pathInfo === '' || $pathInfo === '/') {
      return;
    }

    if ($this->isExcludedPath($pathInfo)) {
      return;
    }

    [$currentUrlLang, $relativePath] = $this->parseUrlLanguageAndRelativePath($pathInfo);
    $relativePath = trim($relativePath, '/');
    if ($relativePath === '') {
      return;
    }

    $segments = explode('/', $relativePath);
    $firstSegment = strtolower($segments[0]);
    if ($firstSegment === '') {
      return;
    }

    $ownerLang = NULL;
    if ($this->searchPathResolver->isSearchPathSegment($firstSegment) && count($segments) === 1) {
      $ownerLang = $this->findSearchPathOwnerLangcode($firstSegment);
    }
    else {
      $ownerLang = $this->findOperationSlugOwnerLangcode($firstSegment);
    }

    if ($ownerLang === NULL || $ownerLang === $currentUrlLang) {
      return;
    }

    $this->setRedirectResponse($event, $request, $ownerLang, $relativePath);
  }

  /**
   * Builds and sets a 301 redirect to the slug owner's URL language prefix.
   */
  private function setRedirectResponse(
    RequestEvent $event,
    Request $request,
    string $ownerLang,
    string $relativePath,
  ): void {
    $targetPrefix = $this->urlPrefixForLangcode($ownerLang);
    $targetPath = $targetPrefix . '/' . $relativePath;
    if (!str_ends_with($targetPath, '/')) {
      $targetPath .= '/';
    }

    $queryString = $request->getQueryString();
    if ($queryString !== NULL && $queryString !== '') {
      $targetPath .= '?' . $queryString;
    }

    $event->setResponse(new RedirectResponse($targetPath, 301));
  }

  /**
   * Finds which configured URL language owns a flexible search path slug.
   */
  private function findSearchPathOwnerLangcode(string $segment): ?string {
    foreach ($this->languageManager->getLanguages() as $langcode => $_language) {
      if ($this->searchPathResolver->getSlugForLang($langcode) === $segment) {
        return $langcode;
      }
    }

    return NULL;
  }

  /**
   * Whether the path should bypass cross-language slug detection.
   */
  private function isExcludedPath(string $pathInfo): bool {
    $excludedPrefixes = [
      '/admin',
      '/api/',
      '/batch',
      '/core/',
      '/modules/',
      '/themes/',
      '/sites/',
      '/user/',
      '/node/',
    ];

    foreach ($excludedPrefixes as $prefix) {
      if (str_starts_with($pathInfo, $prefix)) {
        return TRUE;
      }
    }

    return str_ends_with(strtolower($pathInfo), '.html');
  }

  /**
   * Finds which configured URL language owns an operation slug.
   */
  private function findOperationSlugOwnerLangcode(string $operationSlug): ?string {
    foreach ($this->languageManager->getLanguages() as $langcode => $_language) {
      $mappings = $this->searchPathResolver->getSeoSlugMappings($langcode);
      if (isset($mappings['op_to_val'][$operationSlug])) {
        return $langcode;
      }
    }

    return NULL;
  }

  /**
   * Parses path info into URL language and path without the language prefix.
   *
   * @return array{0: string, 1: string}
   *   Tuple of langcode and relative path (may still start with /).
   */
  private function parseUrlLanguageAndRelativePath(string $pathInfo): array {
    $prefixMap = $this->getUrlPrefixByLangcode();
    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();

    uasort($prefixMap, static function (string $a, string $b): int {
      return strlen($b) <=> strlen($a);
    });

    $normalized = trim($pathInfo, '/');
    foreach ($prefixMap as $langcode => $prefix) {
      if ($prefix === '') {
        continue;
      }
      $prefixWithSlash = $prefix . '/';
      if (str_starts_with($normalized, $prefixWithSlash)) {
        $relative = substr($normalized, strlen($prefixWithSlash));
        return [$langcode, $relative];
      }
      if ($normalized === $prefix) {
        return [$langcode, ''];
      }
    }

    return [$defaultLangcode, $normalized];
  }

  /**
   * Returns the public URL prefix (without slashes) for a langcode.
   */
  private function urlPrefixForLangcode(string $langcode): string {
    $prefix = $this->getUrlPrefixByLangcode()[$langcode] ?? '';
    return $prefix !== '' ? '/' . $prefix : '';
  }

  /**
   * Builds a langcode => URL prefix map from language negotiation config.
   *
   * @return array<string, string>
   *   Map keyed by langcode.
   */
  private function getUrlPrefixByLangcode(): array {
    $configured = $this->configFactory->get('language.negotiation')->get('url.prefixes') ?? [];
    $map = [];

    foreach ($this->languageManager->getLanguages() as $langcode => $_language) {
      $prefix = $configured[$langcode] ?? $langcode;
      $map[$langcode] = is_string($prefix) ? trim($prefix, '/') : $langcode;
    }

    return $map;
  }

}
