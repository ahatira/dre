<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\search_api\Entity\Index;

/**
 * Provides published offer counts from the Search API offers index.
 */
final class HomepageOfferCountProvider {

  /**
   * Counts all indexed offers (published nodes in the offers index).
   */
  public function countPublishedOffers(): int {
    $index = Index::load('offers');
    if ($index === NULL) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
      return 0;
    }
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
