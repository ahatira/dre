<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\geofield_map\Services\GoogleMapsService;

/**
 * Reads Google Maps settings shared with the geofield_map module.
 */
final class GoogleMapsSettings {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ?GoogleMapsService $googleMapsService = NULL,
  ) {}

  /**
   * Returns the Google Maps JavaScript API key.
   */
  public function getApiKey(): string {
    $map_key = trim((string) ($this->configFactory->get('geofield_map.settings')->get('gmap_api_key') ?? ''));
    if ($map_key !== '') {
      return $map_key;
    }

    return trim((string) ($this->configFactory->get('geocoder.geocoder_provider.google_maps')->get('configuration.apiKey') ?? ''));
  }

  /**
   * Returns client-side map settings for the offer detail map.
   *
   * @return array<string, string>
   *   Settings keyed for drupalSettings.psOfferMap.
   */
  public function getClientSettings(): array {
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    return [
      'apiKey' => $this->getApiKey(),
      'language' => $this->mapLanguage($langcode),
      'scriptUrl' => $this->getScriptUrl(),
    ];
  }

  /**
   * Maps Drupal langcode to Google Maps language parameter.
   */
  private function mapLanguage(string $langcode): string {
    return match ($langcode) {
      'zh-hans' => 'zh-CN',
      'zh-hant' => 'zh-TW',
      default => $langcode,
    };
  }

  /**
   * Returns the Google Maps JavaScript API script base URL.
   */
  private function getScriptUrl(): string {
    if ($this->googleMapsService !== NULL) {
      $localization = (string) ($this->configFactory->get('geofield_map.settings')->get('gmap_api_localization') ?? 'default');
      return (string) $this->googleMapsService->getGmapApiLocalization($localization);
    }

    return 'https://maps.googleapis.com/maps/api/js';
  }

}
