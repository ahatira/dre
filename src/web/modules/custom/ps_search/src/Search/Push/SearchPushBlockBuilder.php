<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Push;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;

/**
 * Builds the configurable search results push card (calculator interstitial).
 */
final class SearchPushBlockBuilder {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Whether the push card should render for the current result set.
   *
   * @param int $totalResults
   *   Total matching offers for the active search.
   * @param int $pageRowCount
   *   Offer rows rendered on the current page.
   */
  public function shouldDisplay(int $totalResults, int $pageRowCount): bool {
    if ($this->build() === NULL) {
      return FALSE;
    }

    $afterResult = $this->getInsertAfterIndex();
    return $totalResults > $afterResult && $pageRowCount >= $afterResult;
  }

  /**
   * Returns the index after which the push card is inserted (1-based).
   */
  public function getInsertAfterIndex(): int {
    return max(1, (int) $this->getConfig()->get('after_result'));
  }

  /**
   * Builds the push card render array, or NULL when disabled/incomplete.
   *
   * @return array<string, mixed>|null
   *   Render array for the search-push-card SDC.
   */
  public function build(): ?array {
    $config = $this->getConfig();
    if (!(bool) $config->get('enabled')) {
      return NULL;
    }

    $title = trim((string) $config->get('title'));
    $body = trim((string) $config->get('body'));
    $ctaLabel = trim((string) $config->get('cta_label'));
    $ctaUrl = trim((string) $config->get('cta_url'));

    if ($title === '' || $ctaLabel === '' || $ctaUrl === '') {
      return NULL;
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-push-card',
      '#props' => [
        'title' => $title,
        'body' => $body,
        'cta_label' => $ctaLabel,
        'cta_url' => $ctaUrl,
      ],
      '#cache' => [
        'tags' => $config->getCacheTags(),
        'contexts' => ['languages:language_interface'],
      ],
    ];
  }

  /**
   * Loads push settings config.
   */
  private function getConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_search.push_settings');
  }

}
