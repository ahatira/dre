<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\ps_search\Search\Filter\FilterBarBuilder;

/**
 * Homepage hero search panel — same filter rules as the search filter bar.
 */
final class HomepageSearchPanelBuilder {

  public function __construct(
    private readonly FilterBarBuilder $filterBarBuilder,
  ) {}

  /**
   * Builds the homepage hero search slot.
   *
   * @param array<string, string> $labels
   *   Localized editorial labels from the block configuration.
   *
   * @return array<string, mixed>
   */
  public function buildPanelContent(array $labels = [], array $options = []): array {
    return $this->filterBarBuilder->buildHomepageEntryPanel($labels, $options);
  }

}
