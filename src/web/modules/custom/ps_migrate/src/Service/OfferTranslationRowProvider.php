<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Builds offer translation target rows from CRM offer XML.
 */
final class OfferTranslationRowProvider {

  public function __construct(
    private readonly CrmOfferXmlDocumentLoader $documentLoader,
    private readonly CanonicalCountryLanguageResolver $canonicalCountryLanguageResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param string[] $urls
   *
   * @return array<int, array<string, mixed>>
   */
  public function buildRows(array $urls): array {
    $rows = [];
    $activeLangcodes = array_fill_keys(array_keys($this->languageManager->getLanguages()), TRUE);

    foreach ($urls as $url) {
      $url = trim($url);
      if ($url === '') {
        continue;
      }

      $document = $this->documentLoader->loadDocument($url);
      $offers = $document->xpath('/OFFERS_LIST/OFFER') ?: [];
      foreach ($offers as $offer) {
        if (!$offer instanceof \SimpleXMLElement) {
          continue;
        }

        $businessId = trim((string) ($offer->BUSINESS_ID ?? ''));
        if ($businessId === '') {
          continue;
        }

        $countryCode = strtoupper(trim((string) ($offer->xpath('ADDRESS_LIST/ADDRESS[DISPLAY="true"]/COUNTRY_ISO')[0] ?? '')));
        $availableXmlLanguages = $this->extractAvailableLanguages($offer);
        $sourceXmlLanguage = $this->resolveSourceXmlLanguage($countryCode, $availableXmlLanguages);
        $sourceLangcode = $this->canonicalCountryLanguageResolver->xmlToDrupalLangcode($sourceXmlLanguage);
        $technicalElements = $offer->xpath('TECHNICAL_ELEMENTS_LIST')[0] ?? NULL;
        $operationCode = strtoupper(trim((string) ($offer->xpath('OPERATIONS_LIST/OPERATION_CODE[1]')[0] ?? '')));
        $typeCode = strtoupper(trim((string) ($offer->TYPE_CODE ?? '')));
        $addressCity = trim((string) ($offer->xpath('ADDRESS_LIST/ADDRESS[DISPLAY="true"]/CITY')[0] ?? ''));
        $allSurfaceValues = [];
        foreach (($offer->xpath('GLOBAL_SURFACES/SURFACE/VALUE') ?: []) as $surfaceValue) {
          $surfaceText = trim((string) $surfaceValue);
          if ($surfaceText !== '') {
            $allSurfaceValues[] = $surfaceText;
          }
        }

        foreach ($availableXmlLanguages as $targetXmlLanguage) {
          $targetLangcode = $this->canonicalCountryLanguageResolver->xmlToDrupalLangcode($targetXmlLanguage);
          if ($targetLangcode === $sourceLangcode) {
            continue;
          }
          if (!isset($activeLangcodes[$targetLangcode])) {
            continue;
          }

          $rows[] = [
            'business_id' => $businessId,
            'offer_xml_node' => $offer,
            'technical_elements' => $technicalElements,
            'operation_code' => $operationCode,
            'type_code' => $typeCode,
            'address_city' => $addressCity,
            'all_surface_values' => $allSurfaceValues,
            'address_country' => $countryCode,
            'source_xml_language' => $sourceXmlLanguage,
            'source_langcode' => $sourceLangcode,
            'target_xml_language' => $targetXmlLanguage,
            'target_langcode' => $targetLangcode,
          ];
        }
      }
    }

    return $rows;
  }

  /**
   * @return string[]
   */
  private function extractAvailableLanguages(\SimpleXMLElement $offer): array {
    $languages = [];
    $xpaths = [
      'ML_AVAILABILITY/AVAILABILITY',
      'ML_DESCRIPTION_1/DESCRIPTION',
      'ML_DESCRIPTION_2/DESCRIPTION',
      'ML_DESCRIPTION_4/DESCRIPTION',
      'TECHNICAL_ELEMENTS_LIST/TECHNICAL_ELEMENT/ML_LABEL/LABEL',
      'TECHNICAL_ELEMENTS_LIST/TECHNICAL_ELEMENT/ML_COMPLEMENT/COMPLEMENT',
    ];

    foreach ($xpaths as $xpath) {
      $nodes = $offer->xpath($xpath) ?: [];
      foreach ($nodes as $node) {
        $language = strtoupper(trim((string) ($node['LANGUAGE'] ?? '')));
        $text = trim((string) $node);
        if ($language !== '' && $text !== '') {
          $languages[] = $language;
        }
      }
    }

    if ($languages === []) {
      return ['FR'];
    }

    return array_values(array_unique($languages));
  }

  /**
   * @param string[] $availableXmlLanguages
   */
  private function resolveSourceXmlLanguage(string $countryCode, array $availableXmlLanguages): string {
    $available = array_fill_keys($availableXmlLanguages, TRUE);
    foreach ($this->canonicalCountryLanguageResolver->resolveXmlFallbackLanguages($countryCode) as $candidate) {
      if (isset($available[$candidate])) {
        return $candidate;
      }
    }

    return $availableXmlLanguages[0] ?? $this->canonicalCountryLanguageResolver->resolvePreferredXmlLanguage($countryCode);
  }

}
