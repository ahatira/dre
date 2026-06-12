<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_search\Service\SearchPathResolver;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;

/**
 * Builds path segments used by ps_offer custom tokens.
 *
 * Operation, asset and locality segments align with ps_search SEO URL mappings.
 */
final class OfferPathTokenProvider {

  public function __construct(
    private readonly CountryRepositoryInterface $countryRepository,
    private readonly TransliterationInterface $transliteration,
    private readonly LanguageManagerInterface $languageManager,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
    private readonly OfferLocationTokenResolver $locationTokenResolver,
  ) {}

  public function getOperationSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $code = mb_strtoupper($this->getNodeFieldValue($node, 'field_operation_type'));
    if ($code === '') {
      return 'n-a';
    }

    $langcode = $this->resolveLangcode($langcode);
    $slug = $this->searchPathResolver->getSeoSlugMappings($langcode)['val_to_op'][$code] ?? NULL;
    return $slug ?? 'n-a';
  }

  public function getAssetSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $code = mb_strtoupper($this->getNodeFieldValue($node, 'field_asset_type'));
    if ($code === '') {
      return 'n-a';
    }

    $langcode = $this->resolveLangcode($langcode);
    $slug = $this->searchPathResolver->getSeoSlugMappings($langcode)['val_to_asset'][$code] ?? NULL;
    return $slug ?? 'n-a';
  }

  /**
   * Returns the SEO prefix path shared with search listing URLs.
   *
   * Example: a-louer/bureaux/paris-75/paris-12-75012
   */
  public function getSeoPrefixPath(NodeInterface $node, ?string $langcode = NULL): string {
    $parts = [];

    $operation = $this->getOperationSegment($node, $langcode);
    if ($operation !== 'n-a') {
      $parts[] = $operation;
    }

    $asset = $this->getAssetSegment($node, $langcode);
    if ($asset !== 'n-a') {
      $parts[] = $asset;
    }

    $localityToken = $this->locationTokenResolver->resolveFromOffer($node);
    if ($localityToken !== NULL) {
      $segments = $this->seoLocalityPathBuilder->tokenToPathSegments($localityToken);
      if (isset($segments['dept'])) {
        $parts[] = $segments['dept'];
      }
      if (isset($segments['city'])) {
        $parts[] = $segments['city'];
      }
    }

    return $parts !== [] ? implode('/', $parts) : 'n-a';
  }

  public function getCountrySegment(NodeInterface $node, ?string $langcode = NULL): string {
    $address = $this->getAddressData($node);
    $country_code = (string) ($address['country_code'] ?? '');
    if ($country_code === '') {
      return 'n-a';
    }

    $countries = $this->countryRepository->getList($langcode ?: 'en');
    $country_name = (string) ($countries[$country_code] ?? $country_code);
    return $this->slugify($country_name);
  }

  public function getDepartmentSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $localityToken = $this->locationTokenResolver->resolveFromOffer($node);
    if ($localityToken === NULL) {
      return 'n-a';
    }

    $segments = $this->seoLocalityPathBuilder->tokenToPathSegments($localityToken);
    return $segments['dept'] ?? 'n-a';
  }

  public function getCitySegment(NodeInterface $node, ?string $langcode = NULL): string {
    $localityToken = $this->locationTokenResolver->resolveFromOffer($node);
    if ($localityToken === NULL) {
      return 'n-a';
    }

    $segments = $this->seoLocalityPathBuilder->tokenToPathSegments($localityToken);
    return $segments['city'] ?? 'n-a';
  }

  private function getAddressData(NodeInterface $node): array {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return [];
    }

    $item = $node->get('field_address')->first();
    return $item ? (array) $item->getValue() : [];
  }

  private function getNodeFieldValue(NodeInterface $node, string $field_name): string {
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }
    $item = $node->get($field_name)->first();
    if (!$item) {
      return '';
    }
    return trim((string) ($item->getValue()['value'] ?? $item->value ?? ''));
  }

  private function resolveLangcode(?string $langcode): string {
    if ($langcode !== NULL && $langcode !== '') {
      return $langcode;
    }

    return $this->languageManager->getDefaultLanguage()->getId();
  }

  private function slugify(string $value): string {
    $ascii = mb_strtolower(trim($this->transliteration->transliterate($value, 'en')));
    $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $ascii);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'n-a';
  }

}
