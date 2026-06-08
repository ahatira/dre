<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Reads offer location data from CRM XML address nodes.
 */
final class OfferLocationXmlReader {

  /**
   * Returns whether the CRM address should be shown precisely on the frontend.
   */
  public function showsExactAddress(\SimpleXMLElement $offer): bool {
    $primary = $this->getPrimaryAddress($offer);
    if ($primary === NULL) {
      return TRUE;
    }

    return $this->isDisplayTrue($primary);
  }

  /**
   * Returns address field values for Drupal import.
   *
   * @return array<string, string>
   *   Keys: address_line1, locality, postal_code, country_code.
   */
  public function getAddressValues(\SimpleXMLElement $offer): array {
    $primary = $this->getPrimaryAddress($offer);
    if ($primary === NULL) {
      return [];
    }

    return array_filter([
      'address_line1' => trim((string) ($primary->ADDRESS_LINE_1 ?? '')),
      'locality' => trim((string) ($primary->CITY ?? '')),
      'postal_code' => trim((string) ($primary->ZIP_CODE ?? '')),
      'country_code' => strtoupper(trim((string) ($primary->COUNTRY_ISO ?? ''))),
    ], static fn (string $value): bool => $value !== '');
  }

  /**
   * Returns a WKT POINT string or NULL when coordinates are unavailable.
   */
  public function getGeoWkt(\SimpleXMLElement $offer): ?string {
    $coordinates = $this->resolveCoordinates($offer);
    if ($coordinates === NULL) {
      return NULL;
    }

    return sprintf('POINT (%s %s)', $coordinates['lon'], $coordinates['lat']);
  }

  /**
   * Resolves the best GPS coordinates for the offer map.
   *
   * @return array{lon: string, lat: string}|null
   *   Longitude and latitude, or NULL when unavailable.
   */
  private function resolveCoordinates(\SimpleXMLElement $offer): ?array {
    $primary = $this->getPrimaryAddress($offer);
    $hidden = $this->getHiddenAddress($offer);

    if ($primary !== NULL && $this->isDisplayTrue($primary)) {
      $exact = $this->extractGps($primary);
      if ($exact !== NULL) {
        return $exact;
      }
    }

    if ($hidden !== NULL) {
      $hiddenGps = $this->extractGps($hidden);
      if ($hiddenGps !== NULL) {
        return $hiddenGps;
      }
    }

    if ($primary !== NULL) {
      return $this->extractGps($primary);
    }

    return NULL;
  }

  /**
   * Extracts GPS coordinates from one CRM address node.
   *
   * @return array{lon: string, lat: string}|null
   *   Longitude and latitude, or NULL when unavailable.
   */
  private function extractGps(\SimpleXMLElement $address): ?array {
    $lon = trim((string) ($address->GPS->LONGITUDE ?? ''));
    $lat = trim((string) ($address->GPS->LATITUDE ?? ''));
    if ($lon === '' || $lat === '') {
      return NULL;
    }

    return ['lon' => $lon, 'lat' => $lat];
  }

  /**
   * Returns the primary CRM address node.
   */
  private function getPrimaryAddress(\SimpleXMLElement $offer): ?\SimpleXMLElement {
    $addresses = $offer->xpath('ADDRESS_LIST/ADDRESS') ?: [];
    if ($addresses === []) {
      return NULL;
    }

    foreach ($addresses as $address) {
      $primary = strtolower(trim((string) ($address->PRIMARY ?? '')));
      if ($primary === 'true' || $primary === '1') {
        return $address;
      }
    }

    return $addresses[0];
  }

  /**
   * Returns the hidden CRM address node when present.
   */
  private function getHiddenAddress(\SimpleXMLElement $offer): ?\SimpleXMLElement {
    foreach ($offer->xpath('ADDRESS_LIST/ADDRESS') ?: [] as $address) {
      if (!$this->isDisplayTrue($address)) {
        return $address;
      }
    }

    return NULL;
  }

  /**
   * Checks whether a CRM address should be displayed on the frontend.
   */
  private function isDisplayTrue(\SimpleXMLElement $address): bool {
    $display = strtolower(trim((string) ($address->DISPLAY ?? 'true')));
    return !in_array($display, ['false', '0', 'no'], TRUE);
  }

}
