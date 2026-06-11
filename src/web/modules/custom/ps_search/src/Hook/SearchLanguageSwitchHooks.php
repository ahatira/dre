<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Keeps language switcher URLs aligned with SEO search paths.
 */
final class SearchLanguageSwitchHooks {

  private const SEARCH_ROUTE = 'view.ps_search_offers.page_list';

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Rebuilds switch links with full search query + target-language SEO slugs.
   */
  #[Hook('language_switch_links_alter')]
  public function languageSwitchLinksAlter(array &$links, $type, Url $url): void {
    if ($this->routeMatch->getRouteName() !== self::SEARCH_ROUTE) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $query = $request->query->all();
    if ($query === []) {
      return;
    }

    foreach ($links as &$link) {
      $language = $link['language'] ?? NULL;
      if (!$language instanceof LanguageInterface) {
        continue;
      }

      $link['url'] = Url::fromRoute(self::SEARCH_ROUTE, [], [
        'query' => $query,
        'language' => $language,
      ]);
      unset($link['query']);
    }
  }

}
