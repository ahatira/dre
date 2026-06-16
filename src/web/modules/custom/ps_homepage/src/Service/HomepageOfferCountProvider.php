<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides published offer counts from the Search API offers index.
 */
final class HomepageOfferCountProvider {

  public function __construct(
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
    private readonly LanguageManagerInterface $languageManager,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Counts indexed offers for the active content language (matches search page).
   */
  public function countPublishedOffers(): int {
    $index = Index::load('offers');
    if ($index === NULL) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->applyContentLanguageFilter($query);

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
      return 0;
    }
  }

  /**
   * Restricts the count to the same langcode scope as ps_search list queries.
   */
  private function applyContentLanguageFilter(QueryInterface $query): void {
    $request = $this->requestStack->getCurrentRequest();
    $langcodes = $request !== NULL
      ? $this->contentLanguageResolver->resolveSearchLangcodes($request)
      : $this->fallbackContentLangcodes();

    if ($langcodes === []) {
      return;
    }

    if (count($langcodes) === 1) {
      $query->addCondition('langcode', $langcodes[0]);
      return;
    }

    $group = $query->createConditionGroup('OR');
    foreach ($langcodes as $langcode) {
      $group->addCondition('langcode', $langcode);
    }
    $query->addConditionGroup($group);
  }

  /**
   * @return list<string>
   */
  private function fallbackContentLangcodes(): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    return $langcode !== '' ? [$langcode] : [];
  }

  /**
   * Replaces @count in a template with a formatted integer.
   */
  public function formatOffersLine(string $template, int $count): string {
    if ($template === '') {
      return '';
    }

    return str_replace('@count', number_format($count, 0, '', ' '), $template);
  }

}
