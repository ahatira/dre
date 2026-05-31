<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Builds path segments used by ps_offer custom tokens.
 */
final class OfferPathTokenProvider {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CountryRepositoryInterface $countryRepository,
    private readonly SubdivisionRepositoryInterface $subdivisionRepository,
    private readonly TransliterationInterface $transliteration,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  public function getOperationSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $code = mb_strtoupper($this->getNodeFieldValue($node, 'field_operation_type'));
    if ($code === '') {
      return 'n-a';
    }

    $label = $this->resolveDictionaryLabel('operation_type', $code, $langcode);
    return $this->slugify($label ?: $code);
  }

  public function getAssetSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $code = mb_strtoupper($this->getNodeFieldValue($node, 'field_asset_type'));
    if ($code === '') {
      return 'n-a';
    }

    $label = $this->resolveDictionaryLabel('asset_type', $code, $langcode);
    return $this->slugify($label ?: $code);
  }

  public function getCountrySegment(NodeInterface $node, ?string $langcode = NULL): string {
    $address = $this->getAddressData($node);
    $country_code = (string) ($address['country_code'] ?? '');
    if ($country_code === '') {
      return 'n-a';
    }

    $countries = $this->countryRepository->getList($langcode ?: 'en');
    $country_name = (string) ($countries[$country_code] ?? $country_code);
    return $this->slugify($country_name);
  }

  public function getDepartmentSegment(NodeInterface $node, ?string $langcode = NULL): string {
    $address = $this->getAddressData($node);
    $country_code = (string) ($address['country_code'] ?? '');
    $administrative_area = (string) ($address['administrative_area'] ?? '');
    $postal_code = (string) ($address['postal_code'] ?? '');

    if ($administrative_area === '' && $country_code === 'FR' && $postal_code !== '') {
      $administrative_area = substr($postal_code, 0, 2);
    }

    if ($administrative_area === '') {
      return $country_code !== '' ? $this->slugify($country_code) : 'n-a';
    }

    $subdivision_name = $this->resolveSubdivisionName($country_code, $administrative_area);
    if ($country_code === 'FR') {
      $department_code = $this->normalizeDepartmentCode($administrative_area);
      if ($subdivision_name !== '') {
        return $this->slugify($subdivision_name . '-' . $department_code);
      }
      return $this->slugify($department_code);
    }

    return $this->slugify($subdivision_name !== '' ? $subdivision_name : $administrative_area);
  }

  public function getCitySegment(NodeInterface $node): string {
    $address = $this->getAddressData($node);
    $city = (string) ($address['locality'] ?? '');
    if ($city === '') {
      $city = (string) ($address['dependent_locality'] ?? '');
    }
    if ($city === '') {
      $city = (string) ($address['postal_code'] ?? '');
    }
    return $this->slugify($city !== '' ? $city : 'n-a');
  }

  private function getAddressData(NodeInterface $node): array {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return [];
    }

    $item = $node->get('field_address')->first();
    return $item ? (array) $item->getValue() : [];
  }

  private function getNodeFieldValue(NodeInterface $node, string $field_name): string {
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }
    $item = $node->get($field_name)->first();
    if (!$item) {
      return '';
    }
    return trim((string) ($item->getValue()['value'] ?? $item->value ?? ''));
  }

  private function resolveDictionaryLabel(string $type, string $code, ?string $langcode = NULL): ?string {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entities = $storage->loadByProperties([
      'type' => $type,
      'code' => $code,
    ]);
    if ($entities === []) {
      return NULL;
    }

    $entity = reset($entities);
    if ($entity === FALSE) {
      return NULL;
    }

    if ($langcode && method_exists($this->languageManager, 'getLanguageConfigOverride')) {
      $override = $this->languageManager->getLanguageConfigOverride($langcode, $entity->getConfigDependencyName());
      $label_override = trim((string) $override->get('label'));
      if ($label_override !== '') {
        return $label_override;
      }
    }

    return $entity->label();
  }

  private function resolveSubdivisionName(string $country_code, string $administrative_area): string {
    if ($country_code === '' || $administrative_area === '') {
      return '';
    }

    $candidates = [$administrative_area];
    if (!str_contains($administrative_area, '-')) {
      $candidates[] = $country_code . '-' . $administrative_area;
    }

    foreach ($candidates as $candidate) {
      $subdivision = $this->subdivisionRepository->get($candidate, [$country_code]);
      if (!$subdivision) {
        continue;
      }

      $name = $subdivision->getName() ?: $subdivision->getLocalName();
      if ($name !== '') {
        return $name;
      }
    }

    return '';
  }

  private function normalizeDepartmentCode(string $administrative_area): string {
    $parts = explode('-', mb_strtoupper($administrative_area));
    return end($parts) ?: mb_strtoupper($administrative_area);
  }

  private function slugify(string $value): string {
    $ascii = mb_strtolower(trim($this->transliteration->transliterate($value, 'en')));
    $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $ascii);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'n-a';
  }

}