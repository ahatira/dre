<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Offer;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferSurfaceKpiBuilder;
use Drupal\schema_metatag\SchemaMetatagManager;

/**
 * Builds BNPPRE-style head tags and Schema.org values for offer nodes.
 */
final class OfferSeoHeadBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly OfferSurfaceKpiBuilder $surfaceKpiBuilder,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * Builds Metatag-ready values for an offer detail page.
   *
   * @return array<string, mixed>|null
   *   Head tag values, or NULL when the node is not an offer.
   */
  public function build(NodeInterface $node): ?array {
    if ($node->bundle() !== 'offer') {
      return NULL;
    }

    $langcode = $node->language()->getId();
    $siteName = (string) $this->configFactory->get('system.site')->get('name');
    $siteUrl = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();

    $assetLabel = $this->dictionaryLabel($node, 'field_asset_type', 'asset_type', $langcode);
    $operationCode = $this->fieldCode($node, 'field_operation_type');
    $operationPhrase = $this->operationPhrase($operationCode);
    $surface = $this->primarySurfaceLabel($this->surfaceKpiBuilder->buildKpiSummary($node));
    $address = $this->addressData($node);
    $city = trim((string) ($address['locality'] ?? ''));
    $postalCode = trim((string) ($address['postal_code'] ?? ''));
    $reference = trim((string) ($node->get('field_reference')->value ?? ''));
    $commercialTitle = trim((string) ($node->get('field_commercial_title')->value ?? ''));
    $canonicalUrl = $node->toUrl('canonical', ['absolute' => TRUE, 'language' => $node->language()])->toString();
    $imageUrl = $this->resolvePrimaryImageUrl($node);

    $location = trim($city . ($postalCode !== '' ? ' ' . $postalCode : ''));
    $titleParts = array_values(array_filter([
      $assetLabel,
      $surface,
      $operationPhrase,
      $location,
    ]));
    $titleCore = implode(' ', $titleParts);
    if ($reference !== '') {
      $titleCore .= ' - ' . $reference;
    }
    $title = $siteName !== '' ? $titleCore . ' | ' . $siteName : $titleCore;

    $region = $this->regionLabel($address, $langcode);
    $description = $this->buildDescription(
      $node,
      $assetLabel,
      $surface,
      $operationPhrase,
      $city,
      $postalCode,
      $region,
      $reference,
    );

    $placeName = $commercialTitle !== '' ? $commercialTitle : $titleCore;
    $schemaAddress = $this->buildPostalAddress($address, $langcode);
    $schemaGeo = $this->buildGeoCoordinates($node);

    $head = [
      'title' => $title,
      'description' => $description,
      'canonical_url' => $canonicalUrl,
      'og_title' => $titleCore,
      'og_description' => $description,
      'og_url' => $canonicalUrl,
      'twitter_cards_title' => $titleCore,
      'twitter_cards_description' => $description,
      'schema_web_page_type' => 'WebPage',
      'schema_web_page_description' => $description,
      'schema_web_page_id' => $canonicalUrl,
      'schema_web_page_publisher' => $this->serializeSchema([
        '@type' => 'Organization',
        'name' => $siteName !== '' ? $siteName : 'Property Search',
        'url' => $siteUrl,
      ]),
      'schema_place_type' => 'Place',
      'schema_place_name' => $placeName,
      'schema_place_description' => $description,
      'schema_place_url' => $canonicalUrl,
    ];

    if ($schemaAddress !== '') {
      $head['schema_place_address'] = $schemaAddress;
    }
    if ($schemaGeo !== '') {
      $head['schema_place_geo'] = $schemaGeo;
    }
    if ($imageUrl !== NULL) {
      $head['og_image'] = $imageUrl;
      $head['og_image_url'] = $imageUrl;
      $head['twitter_cards_image'] = $imageUrl;
      $head['schema_place_image'] = $this->serializeSchema([
        '@type' => 'ImageObject',
        'url' => $imageUrl,
        'contentUrl' => $imageUrl,
        'name' => $placeName,
      ]);
    }

    return $head;
  }

  /**
   * Builds hreflang alternates for translated offer nodes.
   *
   * @return array<string, string>
   *   Alternate URLs keyed by langcode and optionally x-default.
   */
  public function buildAlternateUrls(NodeInterface $node): array {
    if (!$node->isTranslatable()) {
      return [];
    }

    $urls = [];
    foreach ($node->getTranslationLanguages() as $langcode => $language) {
      if (!$node->hasTranslation($langcode)) {
        continue;
      }
      $translation = $node->getTranslation($langcode);
      if (!$translation->isPublished()) {
        continue;
      }
      $urls[$langcode] = $translation->toUrl('canonical', [
        'absolute' => TRUE,
        'language' => $language,
      ])->toString();
    }

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    if (isset($urls[$defaultLangcode])) {
      $urls['x-default'] = $urls[$defaultLangcode];
    }

    return $urls;
  }

  /**
   * Builds a meta description aligned with BNPPRE offer pages.
   */
  private function buildDescription(
    NodeInterface $node,
    string $assetLabel,
    string $surface,
    string $operationPhrase,
    string $city,
    string $postalCode,
    string $region,
    string $reference,
  ): string {
    if ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $body = $node->get('body')->first();
      $summary = trim(strip_tags((string) ($body->summary ?? '')));
      if ($summary === '') {
        $summary = trim(strip_tags((string) ($body->value ?? '')));
        if (mb_strlen($summary) > 160) {
          $summary = rtrim(mb_substr($summary, 0, 157)) . '…';
        }
      }
      if ($summary !== '') {
        return $this->truncateDescription($summary);
      }
    }

    $location = $city;
    if ($postalCode !== '') {
      $location = $city !== ''
        ? $this->t('@city (@postal_code)', ['@city' => $city, '@postal_code' => $postalCode])
        : $postalCode;
    }
    if ($region !== '' && $location !== '') {
      $location = $this->t('@location, @region', [
        '@location' => $location,
        '@region' => $region,
      ]);
    }

    $parts = array_values(array_filter([$assetLabel, $surface, $operationPhrase]));
    $lead = implode(' ', $parts);
    if ($location !== '') {
      $lead = $this->t('@lead in @location', [
        '@lead' => $lead,
        '@location' => $location,
      ]);
    }

    if ($reference !== '') {
      $lead = $this->t('@lead. Ref. @reference.', [
        '@lead' => rtrim((string) $lead, '.'),
        '@reference' => $reference,
      ]);
    }

    return $this->truncateDescription((string) $lead);
  }

  /**
   * Truncates meta descriptions to a search-friendly length.
   */
  private function truncateDescription(string $text): string {
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
    if ($text === '') {
      return '';
    }
    if (mb_strlen($text) <= 160) {
      return $text;
    }

    return rtrim(mb_substr($text, 0, 157)) . '…';
  }

  /**
   * Returns a translated rent/sale phrase for titles and descriptions.
   */
  private function operationPhrase(string $code): string {
    return match (strtoupper($code)) {
      'LOC' => (string) $this->t('for rent'),
      'VEN' => (string) $this->t('for sale'),
      default => strtolower($code),
    };
  }

  /**
   * Reads the primary surface fragment without divisibility suffix.
   */
  private function primarySurfaceLabel(string $kpi): string {
    if ($kpi === '') {
      return '';
    }

    $parenPos = mb_strpos($kpi, ' (', 0, 'UTF-8');
    if ($parenPos !== FALSE) {
      return trim(mb_substr($kpi, 0, $parenPos, 'UTF-8'));
    }

    $separator = (string) ($this->configFactory->get('ps_offer.settings')->get('surface_kpi_separator') ?? ' · ');
    $parts = explode($separator, $kpi);
    return trim($parts[0]);
  }

  /**
   * Resolves a dictionary label for a node field.
   */
  private function dictionaryLabel(
    NodeInterface $node,
    string $fieldName,
    string $dictionaryType,
    string $langcode,
  ): string {
    $code = $this->fieldCode($node, $fieldName);
    if ($code === '') {
      return '';
    }

    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entities = $storage->loadByProperties([
      'type' => $dictionaryType,
      'code' => $code,
    ]);
    if ($entities === []) {
      return $code;
    }

    $entity = reset($entities);
    if ($entity === FALSE) {
      return $code;
    }

    $override = $this->languageManager->getLanguageConfigOverride($langcode, $entity->getConfigDependencyName());
    $labelOverride = trim((string) $override->get('label'));
    if ($labelOverride !== '') {
      return $labelOverride;
    }

    return (string) $entity->label();
  }

  /**
   * Reads a dictionary-backed field code from a node.
   */
  private function fieldCode(NodeInterface $node, string $fieldName): string {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return '';
    }

    return strtoupper(trim((string) $node->get($fieldName)->value));
  }

  /**
   * Returns structured address field values for an offer.
   *
   * @return array<string, string>
   *   Address components keyed by Address field property names.
   */
  private function addressData(NodeInterface $node): array {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return [];
    }

    $item = $node->get('field_address')->first();
    return $item ? array_map('strval', (array) $item->getValue()) : [];
  }

  /**
   * Builds a human-readable region label from address data.
   */
  private function regionLabel(array $address, string $langcode): string {
    $countryCode = (string) ($address['country_code'] ?? '');
    $adminArea = (string) ($address['administrative_area'] ?? '');
    $postalCode = (string) ($address['postal_code'] ?? '');

    if ($adminArea === '' && $countryCode === 'FR' && $postalCode !== '') {
      $adminArea = substr($postalCode, 0, 2);
    }

    if ($adminArea === '') {
      return '';
    }

    if ($countryCode === 'FR' && preg_match('/^\d{2,3}$/', $adminArea)) {
      $departments = [
        '75' => 'Paris',
        '91' => 'Essonne',
        '94' => 'Val-de-Marne',
      ];
      return $departments[$adminArea] ?? ('Department ' . $adminArea);
    }

    return $adminArea;
  }

  /**
   * Builds a Schema.org PostalAddress serialized value.
   */
  private function buildPostalAddress(array $address, string $langcode): string {
    if ($address === []) {
      return '';
    }

    $streetAddress = trim(implode(' ', array_filter([
      trim((string) ($address['address_line1'] ?? '')),
      trim((string) ($address['address_line2'] ?? '')),
    ])));

    $payload = array_filter([
      '@type' => 'PostalAddress',
      'streetAddress' => $streetAddress,
      'addressLocality' => trim((string) ($address['locality'] ?? '')),
      'postalCode' => trim((string) ($address['postal_code'] ?? '')),
      'addressCountry' => trim((string) ($address['country_code'] ?? '')),
      'addressRegion' => $this->regionLabel($address, $langcode),
    ], static fn(mixed $value): bool => is_string($value) && $value !== '');

    return $this->serializeSchema($payload);
  }

  /**
   * Builds a Schema.org GeoCoordinates serialized value.
   */
  private function buildGeoCoordinates(NodeInterface $node): string {
    if (!$node->hasField('field_geo') || $node->get('field_geo')->isEmpty()) {
      return '';
    }

    $wkt = (string) $node->get('field_geo')->value;
    if (!preg_match('/POINT\s*\(([^)]+)\)/', $wkt, $matches)) {
      return '';
    }

    $coordinates = preg_split('/\s+/', trim($matches[1])) ?: [];
    if (count($coordinates) < 2) {
      return '';
    }

    return $this->serializeSchema([
      '@type' => 'GeoCoordinates',
      'latitude' => $coordinates[1],
      'longitude' => $coordinates[0],
    ]);
  }

  /**
   * Resolves the first gallery image absolute URL for OG/Schema tags.
   */
  private function resolvePrimaryImageUrl(NodeInterface $node): ?string {
    if (!$node->hasField('field_media_gallery') || $node->get('field_media_gallery')->isEmpty()) {
      return NULL;
    }

    foreach ($node->get('field_media_gallery') as $item) {
      $media = $item->entity;
      if (!$media instanceof MediaInterface || !$media->hasField('field_media_image')) {
        continue;
      }
      if ($media->get('field_media_image')->isEmpty()) {
        continue;
      }
      $file = $media->get('field_media_image')->entity;
      if ($file === NULL) {
        continue;
      }

      return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
    }

    return NULL;
  }

  /**
   * Serializes a Schema Metatag nested value.
   *
   * @param array<string, mixed> $value
   *   Schema.org property tree.
   *
   * @return string
   *   Serialized value for Metatag storage.
   */
  private function serializeSchema(array $value): string {
    return (string) SchemaMetatagManager::serialize($value);
  }

}
