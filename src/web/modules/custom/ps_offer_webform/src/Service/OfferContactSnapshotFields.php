<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

/**
 * Webform element keys for the offer snapshot captured at submission time.
 */
final class OfferContactSnapshotFields {

  public const WEBFORM_IDS = [
    'offer_contact',
    'schedule_visit',
  ];

  public const OFFER_REFERENCE = 'offer_reference';

  public const OFFER_BUSINESS_ID = 'offer_business_id';

  public const OFFER_OPERATION_CODE = 'offer_operation_code';

  public const OFFER_ASSET_CODE = 'offer_asset_code';

  public const OFFER_CAPTURED_AT = 'offer_captured_at';

  public const OFFER_TITLE = 'offer_title';

  public const OFFER_OPERATION_LABEL = 'offer_operation_label';

  public const OFFER_ASSET_LABEL = 'offer_asset_label';

  public const OFFER_SURFACE_PRIMARY = 'offer_surface_primary';

  public const OFFER_SURFACE_SUFFIX = 'offer_surface_suffix';

  public const OFFER_LOCATION = 'offer_location';

  public const OFFER_PRICE_DISPLAY = 'offer_price_display';

  public const OFFER_PRICE_QUALIFIERS = 'offer_price_qualifiers';

  public const OFFER_EXCLUSIVE = 'offer_exclusive';

  public const OFFER_URL = 'offer_url';

  public const OFFER_IMAGE_URI = 'offer_image_uri';

  public const OFFER_IMAGE_ALT = 'offer_image_alt';

  public const OFFER_LANGCODE = 'offer_langcode';

  public const OFFER_AGENT_EMAIL = 'offer_agent_email';

  public const OFFER_AGENT_ID = 'offer_agent_id';

  /**
   * All snapshot element keys in stable order.
   *
   * @return list<string>
   */
  public static function all(): array {
    return [
      self::OFFER_REFERENCE,
      self::OFFER_BUSINESS_ID,
      self::OFFER_OPERATION_CODE,
      self::OFFER_ASSET_CODE,
      self::OFFER_CAPTURED_AT,
      self::OFFER_TITLE,
      self::OFFER_OPERATION_LABEL,
      self::OFFER_ASSET_LABEL,
      self::OFFER_SURFACE_PRIMARY,
      self::OFFER_SURFACE_SUFFIX,
      self::OFFER_LOCATION,
      self::OFFER_PRICE_DISPLAY,
      self::OFFER_PRICE_QUALIFIERS,
      self::OFFER_EXCLUSIVE,
      self::OFFER_URL,
      self::OFFER_IMAGE_URI,
      self::OFFER_IMAGE_ALT,
      self::OFFER_LANGCODE,
      self::OFFER_AGENT_EMAIL,
      self::OFFER_AGENT_ID,
    ];
  }

  /**
   * Keys that must be non-empty after snapshot capture.
   *
   * @return list<string>
   */
  public static function required(): array {
    return [
      self::OFFER_CAPTURED_AT,
      self::OFFER_URL,
      self::OFFER_LANGCODE,
      self::OFFER_AGENT_EMAIL,
    ];
  }

  /**
   * Snapshot keys excluded from visitor email recap output.
   *
   * @return array<string, string>
   */
  public static function excludedElements(): array {
    $excluded = [];
    foreach (self::all() as $key) {
      $excluded[$key] = $key;
    }

    return $excluded;
  }

  /**
   * Snapshot keys stored in submission but omitted from the email recap table.
   *
   * @return list<string>
   */
  public static function excludedFromEmailRecap(): array {
    return [
      self::OFFER_URL,
      self::OFFER_IMAGE_URI,
      self::OFFER_IMAGE_ALT,
      self::OFFER_LANGCODE,
      self::OFFER_AGENT_EMAIL,
      self::OFFER_AGENT_ID,
    ];
  }

  /**
   * Human-readable labels for snapshot email recap (keyed by field name).
   *
   * @return array<string, string>
   */
  public static function labels(): array {
    return [
      self::OFFER_REFERENCE => 'Commercial reference',
      self::OFFER_BUSINESS_ID => 'Business ID (CRM)',
      self::OFFER_OPERATION_CODE => 'Operation code',
      self::OFFER_ASSET_CODE => 'Asset type code',
      self::OFFER_CAPTURED_AT => 'Captured at',
      self::OFFER_TITLE => 'Listing title',
      self::OFFER_OPERATION_LABEL => 'Operation',
      self::OFFER_ASSET_LABEL => 'Asset type',
      self::OFFER_SURFACE_PRIMARY => 'Surface (primary)',
      self::OFFER_SURFACE_SUFFIX => 'Surface (suffix)',
      self::OFFER_LOCATION => 'Location',
      self::OFFER_PRICE_DISPLAY => 'Price',
      self::OFFER_PRICE_QUALIFIERS => 'Price qualifiers',
      self::OFFER_EXCLUSIVE => 'Exclusive mandate',
      self::OFFER_URL => 'Offer URL',
      self::OFFER_IMAGE_URI => 'Image file URI',
      self::OFFER_IMAGE_ALT => 'Image alt text',
      self::OFFER_LANGCODE => 'Language',
      self::OFFER_AGENT_EMAIL => 'Consultant email',
      self::OFFER_AGENT_ID => 'Consultant ID',
    ];
  }

  /**
   * Labels for fields shown in the snapshot email recap table.
   *
   * @return array<string, string>
   */
  public static function emailRecapLabels(): array {
    $excluded = array_flip(self::excludedFromEmailRecap());

    return array_filter(
      self::labels(),
      static fn(string $key): bool => !isset($excluded[$key]),
      ARRAY_FILTER_USE_KEY,
    );
  }

  /**
   * Whether submission data contains a complete offer snapshot.
   *
   * @param array<string, mixed> $data
   */
  public static function isComplete(array $data): bool {
    foreach (self::required() as $key) {
      if (trim((string) ($data[$key] ?? '')) === '') {
        return FALSE;
      }
    }

    return TRUE;
  }

}
