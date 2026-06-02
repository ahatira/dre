<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\source;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\ps_migrate\Service\CanonicalCountryLanguageResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Expands one offer row into one row per target translation language.
 */
#[MigrateSource(id: 'ps_offer_translation_targets')]
final class OfferTranslationTargetsSource extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  /**
   * Constructs the source plugin.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    private readonly CanonicalCountryLanguageResolver $canonicalCountryLanguageResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('ps_migrate.canonical_country_language_resolver'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    return [
      'business_id' => (string) $this->t('Business ID'),
      'offer_xml_node' => (string) $this->t('Full offer XML node'),
      'technical_elements' => (string) $this->t('Technical elements node'),
      'operation_code' => (string) $this->t('Operation code'),
      'type_code' => (string) $this->t('Asset type code'),
      'address_city' => (string) $this->t('City'),
      'all_surface_values' => (string) $this->t('All surface values'),
      'address_country' => (string) $this->t('Country ISO code'),
      'source_xml_language' => (string) $this->t('Canonical XML source language'),
      'source_langcode' => (string) $this->t('Canonical Drupal source language'),
      'target_xml_language' => (string) $this->t('Target XML language'),
      'target_langcode' => (string) $this->t('Target Drupal language'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    return [
      'business_id' => ['type' => 'string'],
      'target_langcode' => ['type' => 'string'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    $urls = $this->configuredUrls();
    return $urls === [] ? 'no-urls-configured' : implode(', ', $urls);
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator(): \Iterator {
    $rows = [];
    $activeLangcodes = array_fill_keys(array_keys($this->languageManager->getLanguages()), TRUE);

    foreach ($this->configuredUrls() as $url) {
      $document = @simplexml_load_file($url);
      if (!$document instanceof \SimpleXMLElement) {
        continue;
      }

      $offers = $document->xpath('/OFFERS_LIST/OFFER') ?: [];
      foreach ($offers as $offer) {
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

    return new \ArrayIterator($rows);
  }

  /**
   * @return string[]
   *   Configured URLs.
   */
  private function configuredUrls(): array {
    $urls = $this->configuration['urls'] ?? [];
    if (!is_array($urls)) {
      $urls = [$urls];
    }
    $urls = array_map(static fn(string $url): string => trim($url), $urls);
    return array_values(array_filter($urls, static fn(string $url): bool => $url !== ''));
  }

  /**
   * @return string[]
   *   Uppercase XML language codes present on the offer.
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
   * Resolves the canonical source language from country and available labels.
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
