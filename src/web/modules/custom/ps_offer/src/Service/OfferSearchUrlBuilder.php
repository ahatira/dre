<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Builds filtered property search URLs from offer field values.
 */
final class OfferSearchUrlBuilder {

  private const SEARCH_ROUTE = 'view.ps_search_offers.page_list';

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly OfferLocationTokenResolver $locationTokenResolver,
  ) {}

  /**
   * Builds the search results URL matching this offer's main filters.
   */
  public function buildResultsPageUrl(NodeInterface $node, ?string $langcode = NULL): Url {
    $langcode ??= $node->language()->getId();
    $language = $this->languageManager->getLanguage($langcode)
      ?? $this->languageManager->getCurrentLanguage();
    $query = $this->buildFilterQuery($node);

    return Url::fromRoute(self::SEARCH_ROUTE, [], [
      'query' => $query,
      'language' => $language,
    ]);
  }

  /**
   * @return array<string, string>
   *   Canonical search filter query parameters.
   */
  public function buildFilterQuery(NodeInterface $node): array {
    $query = [];

    $operationType = $this->readListFieldValue($node, 'field_operation_type');
    if ($operationType !== '') {
      $query['operation_type'] = $operationType;
    }

    $assetType = $this->readListFieldValue($node, 'field_asset_type');
    if ($assetType !== '') {
      $query['asset_type'] = $assetType;
    }

    $locality = $this->locationTokenResolver->resolveFromOffer($node);
    if ($locality !== NULL) {
      $query['locality'] = $locality;
    }

    return $query;
  }

  private function readListFieldValue(NodeInterface $node, string $fieldName): string {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return '';
    }

    $item = $node->get($fieldName)->first();
    if ($item === NULL) {
      return '';
    }

    return trim((string) ($item->getValue()['value'] ?? $item->value ?? ''));
  }

}
