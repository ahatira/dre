<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\ps_migrate\ValueObject\FeatureTechnicalElement;

/**
 * Parses CRM XML technical elements into normalized feature records.
 */
final class FeatureTechnicalElementParser {

  /**
   * Parses an XML file into technical element DTOs.
   *
   * @return array<int, \Drupal\ps_migrate\ValueObject\FeatureTechnicalElement>
   *   Parsed technical elements.
   */
  public function parseFile(string $filePath, string|array $preferredLanguage = 'FR'): array {
    if (!is_file($filePath)) {
      throw new \InvalidArgumentException(sprintf('XML file not found: %s', $filePath));
    }

    $contents = file_get_contents($filePath);
    if ($contents === FALSE) {
      throw new \RuntimeException(sprintf('Unable to read XML file: %s', $filePath));
    }

    return $this->parseString($contents, $filePath, $preferredLanguage);
  }

  /**
   * Parses an XML string into technical element DTOs.
   *
   * @return array<int, \Drupal\ps_migrate\ValueObject\FeatureTechnicalElement>
   *   Parsed technical elements.
   */
  public function parseString(string $xmlContents, string $sourceLabel = 'XML string', string|array $preferredLanguage = 'FR'): array {
    $previous = libxml_use_internal_errors(TRUE);
    libxml_clear_errors();

    $document = simplexml_load_string($xmlContents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
    if ($document === FALSE) {
      $errors = array_map(static fn(\LibXMLError $error): string => trim($error->message), libxml_get_errors());
      libxml_clear_errors();
      libxml_use_internal_errors($previous);

      $message = sprintf('Unable to parse feature XML from %s.', $sourceLabel);
      if ($errors !== []) {
        $message .= ' ' . implode(' ', $errors);
      }

      throw new \RuntimeException($message);
    }

    $nodes = $document->xpath('//TECHNICAL_ELEMENTS_LIST/TECHNICAL_ELEMENT') ?: [];
    $elements = [];

    foreach ($nodes as $index => $node) {
      $element = $this->normalizeElement($node, (int) $index, $preferredLanguage);
      $elements[] = $element->withMessages(
        $this->validate($element),
        $this->buildWarnings($element),
      );
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return $elements;
  }

  /**
   * Maps a technical element DTO to a normalized import record.
   */
  public function map(FeatureTechnicalElement $element): array {
    return $element->toRecord();
  }

  /**
   * Normalizes a single XML technical element node.
   */
  public function normalizeNode(\SimpleXMLElement $node, int $index = 0, string|array $preferredLanguage = 'FR'): FeatureTechnicalElement {
    $element = $this->normalizeElement($node, $index, $preferredLanguage);
    return $element->withMessages(
      $this->validate($element),
      $this->buildWarnings($element),
    );
  }

  /**
   * Validates a technical element DTO.
   *
   * @return string[]
   *   Validation errors.
   */
  public function validate(FeatureTechnicalElement $element): array {
    $errors = [];

    if ($element->getGroupCode() === '') {
      $errors[] = 'Missing TECHNICAL_ELEMENT/CODE_GROUP value.';
    }

    if ($element->getFeatureCode() === '') {
      $errors[] = 'Missing TECHNICAL_ELEMENT/CODE_ELEMENT value.';
    }

    return $errors;
  }

  /**
   * Builds soft warnings for a normalized technical element.
   *
   * @return string[]
   *   Warning messages.
   */
  public function buildWarnings(FeatureTechnicalElement $element): array {
    $warnings = [];

    if ($element->getLabel() === '') {
      $warnings[] = 'Missing label value; feature code will be used as fallback label.';
    }

    if ($element->getValue() === NULL && $element->getUnit() === NULL && $element->getComplement() === NULL) {
      $warnings[] = 'Technical element has no payload value.';
    }

    return $warnings;
  }

  /**
   * Normalizes a SimpleXML technical element into a DTO.
   */
  private function normalizeElement(\SimpleXMLElement $node, int $index, string|array $preferredLanguage): FeatureTechnicalElement {
    $group_code = $this->readChildValue($node, 'CODE_GROUP') ?? '';
    $feature_code = $this->readChildValue($node, 'CODE_ELEMENT') ?? '';
    $label = $this->readTranslatedChildValue($node, 'ML_LABEL', 'LABEL', $preferredLanguage)
      ?? $this->readChildValue($node, 'LIBELLE_ELEMENT')
      ?? $this->readChildValue($node, 'ML_LABEL')
      ?? '';
    $value = $this->readChildValue($node, 'VALUE');
    $unit = $this->readChildValue($node, 'UNIT');
    $complement = $this->readTranslatedChildValue($node, 'ML_COMPLEMENT', 'COMPLEMENT', $preferredLanguage)
      ?? $this->readChildValue($node, 'ML_COMPLEMENT');

    if ($label === '') {
      $label = $feature_code !== '' ? $feature_code : $group_code;
    }

    return new FeatureTechnicalElement(
      $this->normalizeScalar($group_code),
      $this->normalizeScalar($feature_code),
      $this->normalizeScalar($label),
      $this->normalizeNullableScalar($value),
      $this->normalizeNullableScalar($unit),
      $this->normalizeNullableScalar($complement),
      $index,
    );
  }

  /**
   * Reads a direct child node value.
   */
  private function readChildValue(\SimpleXMLElement $node, string $childName): ?string {
    $children = $node->children();
    if (!isset($children->{$childName})) {
      return NULL;
    }

    $value = trim((string) $children->{$childName});
    return $value === '' ? NULL : $value;
  }

  /**
   * Reads a translated child value from a nested multilingual node.
   */
  private function readTranslatedChildValue(\SimpleXMLElement $node, string $containerName, string $entryName, string|array $preferredLanguage): ?string {
    $children = $node->children();
    if (!isset($children->{$containerName})) {
      return NULL;
    }

    $container = $children->{$containerName};
    if (!isset($container->{$entryName})) {
      return NULL;
    }

    foreach ($this->normalizePreferredLanguages($preferredLanguage) as $preferredLanguageCode) {
      foreach ($container->{$entryName} as $entry) {
        $language = strtoupper(trim((string) ($entry['LANGUAGE'] ?? '')));
        $value = trim((string) $entry);
        if ($language === $preferredLanguageCode && $value !== '') {
          return $value;
        }
      }
    }

    foreach ($container->{$entryName} as $entry) {
      $value = trim((string) $entry);
      if ($value !== '') {
        return $value;
      }
    }

    return NULL;
  }

  /**
   * Normalizes preferred languages into an ordered uppercase list.
   *
   * @return string[]
   *   Uppercase XML language codes.
   */
  private function normalizePreferredLanguages(string|array $preferredLanguage): array {
    $languages = is_array($preferredLanguage) ? $preferredLanguage : [$preferredLanguage];
    $languages = array_map(static fn(mixed $language): string => strtoupper(trim((string) $language)), $languages);
    $languages = array_values(array_filter($languages, static fn(string $language): bool => $language !== ''));

    return $languages === [] ? ['FR'] : array_values(array_unique($languages));
  }

  /**
   * Normalizes a required scalar string.
   */
  private function normalizeScalar(string $value): string {
    $value = trim($value);
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
  }

  /**
   * Normalizes an optional scalar string.
   */
  private function normalizeNullableScalar(?string $value): ?string {
    if ($value === NULL) {
      return NULL;
    }

    $value = trim($value);
    if ($value === '') {
      return NULL;
    }

    return preg_replace('/\s+/u', ' ', $value) ?: NULL;
  }

}