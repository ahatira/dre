<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;

/**
 * Applies per-site allowed countries on the offer address field (CMI).
 *
 * Maps Property Search country codes (fr, es, com, …) to Address module ISO
 * codes and persists them in field.field.node.offer.field_address.
 */
final class OfferAddressCountryConfigurator {

  private const FIELD_CONFIG = 'field.field.node.offer.field_address';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Resolves ISO 3166-1 alpha-2 codes allowed for a PS country site.
   *
   * @return string[]
   *   Empty list means all countries (international / com site).
   */
  public function resolveAvailableCountryCodes(string $psCountryCode): array {
    $psCountryCode = strtolower(trim($psCountryCode));
    if ($psCountryCode === '' || $psCountryCode === 'com') {
      return [];
    }

    return [strtoupper($psCountryCode)];
  }

  /**
   * Applies available_countries for the current site when needed.
   *
   * @return bool
   *   TRUE when active config was updated.
   */
  public function applyForCurrentSite(): bool {
    $psCountryCode = Settings::get('ps_country_code');
    if (!is_string($psCountryCode) || $psCountryCode === '') {
      return FALSE;
    }

    return $this->applyForCountry($psCountryCode);
  }

  /**
   * Applies available_countries for a given PS country code.
   *
   * @return bool
   *   TRUE when active config was updated.
   */
  public function applyForCountry(string $psCountryCode): bool {
    $available = $this->resolveAvailableCountryCodes($psCountryCode);
    $config = $this->configFactory->getEditable(self::FIELD_CONFIG);
    if (!$config->isNew() && $config->get('settings.available_countries') === $available) {
      return FALSE;
    }

    $config->set('settings.available_countries', $available)->save(TRUE);
    return TRUE;
  }

}
