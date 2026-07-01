<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\image\Entity\ImageStyle;
use Drupal\ps_offer\Service\OfferDefaultImageResolver;

/**
 * Builds offer email card props from stored snapshot submission values.
 */
final class OfferContactSnapshotPropsBuilder {

  use StringTranslationTrait;

  private const IMAGE_STYLE = 'bnp_media_admin_card';

  public function __construct(
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly OfferDefaultImageResolver $defaultImageResolver,
  ) {}

  /**
   * @param array<string, mixed> $data
   *   Webform submission values containing snapshot keys.
   *
   * @return array<string, mixed>
   *   Props for offer email card templates.
   */
  public function buildCardPropsFromData(array $data, ?string $langcode = NULL): array {
    $langcode = trim((string) ($data[OfferContactSnapshotFields::OFFER_LANGCODE] ?? $langcode ?? ''));
    $imageUri = trim((string) ($data[OfferContactSnapshotFields::OFFER_IMAGE_URI] ?? ''));
    $image = $imageUri !== ''
      ? $this->buildStyledAbsoluteUrl($imageUri)
      : $this->defaultImageResolver->buildUrl(self::IMAGE_STYLE, FALSE);
    $qualifiers = trim((string) ($data[OfferContactSnapshotFields::OFFER_PRICE_QUALIFIERS] ?? ''));
    $surfacePrimary = trim((string) ($data[OfferContactSnapshotFields::OFFER_SURFACE_PRIMARY] ?? ''));
    $surfaceSuffixRaw = $data[OfferContactSnapshotFields::OFFER_SURFACE_SUFFIX] ?? '';
    $surfaceSuffix = is_string($surfaceSuffixRaw) && trim($surfaceSuffixRaw) !== ''
      ? trim($surfaceSuffixRaw)
      : NULL;
    $locationRaw = $data[OfferContactSnapshotFields::OFFER_LOCATION] ?? NULL;
    $location = is_string($locationRaw) && trim($locationRaw) !== '' ? trim($locationRaw) : NULL;

    $surface = $surfacePrimary !== ''
      ? ($surfaceSuffix !== NULL ? $surfacePrimary . ' ' . $surfaceSuffix : $surfacePrimary)
      : NULL;

    return [
      'title' => trim((string) ($data[OfferContactSnapshotFields::OFFER_TITLE] ?? '')),
      'reference' => trim((string) ($data[OfferContactSnapshotFields::OFFER_REFERENCE] ?? '')),
      'property_type' => trim((string) ($data[OfferContactSnapshotFields::OFFER_ASSET_LABEL] ?? '')),
      'surface' => $surface,
      'surface_primary' => $surfacePrimary !== '' ? $surfacePrimary : NULL,
      'surface_suffix' => $surfaceSuffix,
      'location' => $location,
      'price_amount' => trim((string) ($data[OfferContactSnapshotFields::OFFER_PRICE_DISPLAY] ?? '')),
      'price_qualifiers' => $qualifiers !== ''
        ? Markup::create($this->formatQualifiersMarkup($qualifiers))
        : '',
      'price_on_request_label' => '',
      'exclusive' => ($data[OfferContactSnapshotFields::OFFER_EXCLUSIVE] ?? '0') === '1',
      'url' => trim((string) ($data[OfferContactSnapshotFields::OFFER_URL] ?? '')),
      'cta_label' => (string) $this->t('View the property', [], ['langcode' => $langcode !== '' ? $langcode : NULL]),
      'image' => $image,
      'image_uri' => $imageUri,
      'image_alt' => trim((string) ($data[OfferContactSnapshotFields::OFFER_IMAGE_ALT] ?? '')),
    ];
  }

  private function buildStyledAbsoluteUrl(string $uri): string {
    $style = ImageStyle::load(self::IMAGE_STYLE);
    $url = $style !== NULL ? $style->buildUrl($uri) : $uri;

    return str_contains($url, '://')
      ? $url
      : $this->fileUrlGenerator->generateAbsoluteString($url);
  }

  private function formatQualifiersMarkup(string $qualifiers): string {
    return str_replace('m²', 'm<sup>2</sup>', $qualifiers);
  }

}
