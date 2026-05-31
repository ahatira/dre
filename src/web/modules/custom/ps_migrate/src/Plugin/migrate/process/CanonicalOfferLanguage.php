<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\CanonicalCountryLanguageResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Resolves the canonical source language of an offer from country and XML.
 *
 * @MigrateProcessPlugin(
 *   id = "canonical_offer_language"
 * )
 */
final class CanonicalOfferLanguage extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a CanonicalOfferLanguage object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly CanonicalCountryLanguageResolver $resolver,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.canonical_country_language_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    [$offerNode, $countryCode] = $this->normalizeInputs($value);

    $availableLanguages = $offerNode instanceof \SimpleXMLElement
      ? $this->extractAvailableLanguages($offerNode)
      : [];

    $canonicalXmlLanguage = $this->resolveCanonicalXmlLanguage($availableLanguages, $countryCode);
    $output = strtolower((string) ($this->configuration['output'] ?? 'drupal'));

    return $output === 'xml'
      ? $canonicalXmlLanguage
      : $this->resolver->xmlToDrupalLangcode($canonicalXmlLanguage);
  }

  /**
   * Normalizes plugin inputs.
   *
   * @return array{0:mixed,1:string}
   *   Offer XML node and country code.
   */
  private function normalizeInputs(mixed $value): array {
    if (is_array($value)) {
      return [
        $value[0] ?? NULL,
        (string) ($value[1] ?? ''),
      ];
    }

    return [$value, ''];
  }

  /**
   * Resolves the canonical XML language against available values.
   */
  private function resolveCanonicalXmlLanguage(array $availableLanguages, string $countryCode): string {
    $availableLanguages = array_map('strtoupper', $availableLanguages);
    foreach ($this->resolver->resolveXmlFallbackLanguages($countryCode) as $candidate) {
      if (in_array($candidate, $availableLanguages, TRUE)) {
        return $candidate;
      }
    }

    return $availableLanguages[0] ?? $this->resolver->resolvePreferredXmlLanguage($countryCode);
  }

  /**
   * Extracts languages actually available for imported textual fields.
   *
   * @return string[]
   *   Uppercase XML language codes.
   */
  private function extractAvailableLanguages(\SimpleXMLElement $offerNode): array {
    $languages = [];
    $xpaths = [
      'ML_AVAILABILITY/AVAILABILITY',
      'ML_DESCRIPTION_1/DESCRIPTION',
      'ML_DESCRIPTION_2/DESCRIPTION',
      'ML_DESCRIPTION_4/DESCRIPTION',
    ];

    foreach ($xpaths as $xpath) {
      $nodes = $offerNode->xpath($xpath) ?: [];
      foreach ($nodes as $node) {
        $language = strtoupper(trim((string) ($node['LANGUAGE'] ?? '')));
        $text = trim((string) $node);
        if ($language !== '' && $text !== '') {
          $languages[] = $language;
        }
      }
    }

    return array_values(array_unique($languages));
  }

}