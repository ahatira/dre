<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Resolves preferred canonical languages from CRM country codes.
 */
final class CanonicalCountryLanguageResolver {

  /**
   * Resolves the preferred Drupal langcode for a country code.
   */
  public function resolvePreferredDrupalLangcode(?string $countryCode): string {
    return match ($this->normalizeCountryCode($countryCode)) {
      'FR', 'BE' => 'fr',
      'LU' => 'lb',
      'DE' => 'de',
      'ES' => 'es',
      'IT' => 'it',
      'PL' => 'pl',
      'NL' => 'nl',
      'GB' => 'en',
      default => 'fr',
    };
  }

  /**
   * Resolves the preferred XML language code for a country code.
   */
  public function resolvePreferredXmlLanguage(?string $countryCode): string {
    return strtoupper($this->resolvePreferredDrupalLangcode($countryCode));
  }

  /**
   * Builds an ordered XML language fallback list for a country code.
   *
   * @return string[]
   *   Uppercase XML language codes.
   */
  public function resolveXmlFallbackLanguages(?string $countryCode): array {
    $preferred = $this->resolvePreferredXmlLanguage($countryCode);
    $languages = [$preferred];

    if ($preferred !== 'EN') {
      $languages[] = 'EN';
    }
    if ($preferred !== 'FR') {
      $languages[] = 'FR';
    }

    return array_values(array_unique($languages));
  }

  /**
   * Converts an XML language code to a Drupal langcode.
   */
  public function xmlToDrupalLangcode(string $xmlLanguage): string {
    return strtolower(trim($xmlLanguage));
  }

  /**
   * Normalizes a CRM country code.
   */
  private function normalizeCountryCode(?string $countryCode): string {
    return strtoupper(trim((string) $countryCode));
  }

}