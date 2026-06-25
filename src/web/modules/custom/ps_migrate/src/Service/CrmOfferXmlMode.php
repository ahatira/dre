<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Built-in extraction modes for the unified CRM offer XML source plugin.
 */
final class CrmOfferXmlMode {

  public const OFFER = 'offer';

  public const AGENT = 'agent';

  public const AGENT_AVATAR = 'agent_avatar';

  public const MEDIA_EXT = 'media_ext';

  public const MEDIA_VIS = 'media_vis';

  public const FILE = 'file';

  public const SURFACE_DIVISION = 'surface_division';

  public const FEATURE_GROUPS = 'feature_groups';

  public const FEATURE_DEFINITIONS = 'feature_definitions';

  public const OFFER_TRANSLATIONS = 'offer_translations';

  /**
   * Returns whether the mode delegates row building to a dedicated provider.
   */
  public static function isDelegateMode(string $mode): bool {
    return in_array($mode, [
      self::FEATURE_GROUPS,
      self::FEATURE_DEFINITIONS,
      self::OFFER_TRANSLATIONS,
    ], TRUE);
  }

  /**
   * Returns the XPath item selector for xpath-based modes.
   */
  public static function itemSelector(string $mode): ?string {
    return match ($mode) {
      self::OFFER => '/OFFERS_LIST/OFFER',
      self::AGENT => '/OFFERS_LIST/OFFER/BUSINESS_LEADERS_LIST/BUSINESS_LEADER[normalize-space(UID) != \'\' and not(UID = preceding::BUSINESS_LEADER/UID)]',
      self::AGENT_AVATAR => '/OFFERS_LIST/OFFER/BUSINESS_LEADERS_LIST/BUSINESS_LEADER[normalize-space(UID) != \'\' and normalize-space(AVATAR_URL) != \'\' and not(UID = preceding::BUSINESS_LEADER/UID)]',
      self::MEDIA_EXT, self::FILE => '/OFFERS_LIST/OFFER/MEDIA_LIST/MEDIA[TYPE_CODE = \'EXT\' and not(contains(URL, \'J%C3%A9r%C3%B4me_Blanche.JPG.png\'))]',
      self::MEDIA_VIS => '/OFFERS_LIST/OFFER/MEDIA_LIST/MEDIA[TYPE_CODE = \'VIS\']',
      self::SURFACE_DIVISION => '/OFFERS_LIST/OFFER/DIVISIONS_LIST/DIVISION[normalize-space(LOT) != \'\' and not(LOT = preceding::DIVISION/LOT)]',
      default => NULL,
    };
  }

}
