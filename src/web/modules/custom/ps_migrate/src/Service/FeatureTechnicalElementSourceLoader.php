<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\ps_migrate\ValueObject\FeatureTechnicalElement;

/**
 * Loads technical elements using canonical language resolution per offer.
 */
final class FeatureTechnicalElementSourceLoader {

  /**
   * Constructs a FeatureTechnicalElementSourceLoader object.
   */
  public function __construct(
    private readonly FeatureTechnicalElementParser $parser,
    private readonly CanonicalCountryLanguageResolver $canonicalCountryLanguageResolver,
  ) {}

  /**
   * Loads technical elements from an XML file.
   *
   * @return array<int, \Drupal\ps_migrate\ValueObject\FeatureTechnicalElement>
   *   Parsed technical elements.
   */
  public function loadFromFile(string $filePath): array {
    if (!is_file($filePath)) {
      throw new \InvalidArgumentException(sprintf('XML file not found: %s', $filePath));
    }

    $contents = file_get_contents($filePath);
    if ($contents === FALSE) {
      throw new \RuntimeException(sprintf('Unable to read XML file: %s', $filePath));
    }

    $previous = libxml_use_internal_errors(TRUE);
    libxml_clear_errors();

    $document = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
    if ($document === FALSE) {
      $errors = array_map(static fn(\LibXMLError $error): string => trim($error->message), libxml_get_errors());
      libxml_clear_errors();
      libxml_use_internal_errors($previous);

      $message = sprintf('Unable to parse feature XML from %s.', $filePath);
      if ($errors !== []) {
        $message .= ' ' . implode(' ', $errors);
      }

      throw new \RuntimeException($message);
    }

    $offers = $document->xpath('/OFFERS_LIST/OFFER') ?: [];
    if ($offers === []) {
      libxml_clear_errors();
      libxml_use_internal_errors($previous);
      return $this->parser->parseString($contents, $filePath);
    }

    $elements = [];
    $index = 0;

    foreach ($offers as $offer) {
      $countryCode = $this->extractOfferCountryCode($offer);
      $preferredLanguages = $this->canonicalCountryLanguageResolver->resolveXmlFallbackLanguages($countryCode);

      foreach ($offer->xpath('TECHNICAL_ELEMENTS_LIST/TECHNICAL_ELEMENT') ?: [] as $technicalElement) {
        $elements[] = $this->parser->normalizeNode($technicalElement, $index, $preferredLanguages);
        $index++;
      }
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return $elements;
  }

  /**
   * Extracts the country code from an offer node.
   */
  private function extractOfferCountryCode(\SimpleXMLElement $offer): string {
    $countryNodes = $offer->xpath('ADDRESS_LIST/ADDRESS[PRIMARY="true"]/COUNTRY_ISO') ?: [];
    if ($countryNodes === []) {
      $countryNodes = $offer->xpath('ADDRESS_LIST/ADDRESS/COUNTRY_ISO') ?: [];
    }

    return trim((string) ($countryNodes[0] ?? ''));
  }

}