<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Build offer title or commercial title from CRM source values.
 *
 * Expected source values order:
 * - operation code (LOC, VEN, ...)
 * - asset type code (BUR, COM, ENT, ...)
 * - city or surfaces list (depends on mode)
 * - business ID (fallback)
 *
 * @MigrateProcessPlugin(
 *   id = "offer_composed_title"
 * )
 */
final class OfferComposedTitle extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Dictionary entry storage.
   */
  private EntityStorageInterface $dictionaryEntryStorage;

  /**
   * Cached labels keyed by langcode/type/code.
   *
   * @var array<string, string>
   */
  private array $labelCache = [];

  /**
   * String translation service.
   */
  private TranslationInterface $translationService;

  /**
   * Language config override service.
   */
  private LanguageConfigFactoryOverride $languageConfigOverride;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, TranslationInterface $string_translation, LanguageConfigFactoryOverride $language_config_override) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dictionaryEntryStorage = $entity_type_manager->getStorage('ps_dictionary_entry');
    $this->translationService = $string_translation;
    $this->languageConfigOverride = $language_config_override;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('string_translation'),
      $container->get('language.config_factory_override'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    $source = is_array($value) ? array_values($value) : [$value];

    $operation_code = strtoupper(trim($this->firstScalarString($source[0] ?? NULL)));
    $asset_code = strtoupper(trim($this->firstScalarString($source[1] ?? NULL)));
    $city = trim($this->firstScalarString($source[2] ?? NULL));
    $business_id = trim($this->firstScalarString($source[3] ?? NULL));
    $country_code = strtoupper(trim($this->firstScalarString($source[4] ?? NULL)));
    $forced_langcode = strtolower(trim($this->firstScalarString($source[5] ?? NULL)));
    $langcode = $forced_langcode !== '' ? $forced_langcode : $this->langcodeFromCountry($country_code);

    if ($asset_code === 'LOG') {
      $asset_code = 'ENT';
    }

    $operation_label = $this->resolveDictionaryLabel('operation_type', $operation_code, $langcode) ?? $this->operationLabelFallback($operation_code);
    $asset_label_lower = $this->resolveDictionaryLabel('asset_type', $asset_code, $langcode);
    if ($asset_label_lower === NULL || $asset_label_lower === '') {
      $asset_label_lower = $this->assetLabelFallback($asset_code);
    }
    $asset_label_lower = mb_strtolower($asset_label_lower);
    $asset_label_title = $this->mbUcfirst($asset_label_lower);

    $mode = (string) ($this->configuration['mode'] ?? 'title');

    if ($mode === 'commercial') {
      $surface_label = $this->surfaceLabel($source[2] ?? NULL);
      if ($surface_label !== '') {
        return trim($operation_label . ' ' . $asset_label_title . ' ' . $surface_label);
      }
      return trim($operation_label . ' ' . $asset_label_title);
    }

    if ($city !== '') {
      return trim($operation_label . ' ' . $asset_label_lower . ' ' . $city);
    }

    if ($business_id !== '') {
      return trim($operation_label . ' ' . $asset_label_lower . ' ' . $business_id);
    }

    return trim($operation_label . ' ' . $asset_label_lower);
  }

  /**
   * Build surface label from first available numeric value.
   */
  private function surfaceLabel(mixed $surfaces): string {
    $first = NULL;

    if (is_array($surfaces)) {
      foreach ($surfaces as $candidate) {
        if (is_numeric($candidate)) {
          $first = (float) $candidate;
          break;
        }
      }
    }
    elseif (is_numeric($surfaces)) {
      $first = (float) $surfaces;
    }

    if ($first === NULL) {
      return '';
    }

    $decimals = abs($first - round($first)) < 0.00001 ? 0 : 1;
    $formatted = number_format($first, $decimals, ',', ' ');
    return $formatted . ' m²';
  }

  /**
   * Resolve a dictionary label for a code and language.
   */
  private function resolveDictionaryLabel(string $dictionary_type, string $code, string $langcode): ?string {
    $cache_key = $langcode . ':' . $dictionary_type . ':' . $code;
    if (isset($this->labelCache[$cache_key])) {
      return $this->labelCache[$cache_key];
    }

    $entries = $this->dictionaryEntryStorage->loadByProperties(['type' => $dictionary_type]);
    foreach ($entries as $entry) {
      if (!method_exists($entry, 'getCode') || mb_strtoupper((string) $entry->getCode()) !== mb_strtoupper($code)) {
        continue;
      }

      $translated = $entry;
      if (method_exists($entry, 'hasTranslation') && $entry->hasTranslation($langcode)) {
        $translated = $entry->getTranslation($langcode);
      }
      $label = trim((string) $translated->label());

      // Config entities in this project keep translated labels in language
      // override collections (language.xx), not always via hasTranslation().
      if ($langcode !== '' && method_exists($entry, 'id')) {
        $override = $this->languageConfigOverride->getOverride($langcode, 'ps_dictionary.entry.' . (string) $entry->id());
        $override_label = trim((string) ($override->get('label') ?? ''));
        if ($override_label !== '') {
          $label = $override_label;
        }
      }

      if ($label === '') {
        $label = trim((string) $entry->label());
      }

      if ($label !== '' && $langcode !== '') {
        $translated_label = trim((string) $this->translationService->translate($label, [], ['langcode' => $langcode]));
        if ($translated_label !== '') {
          $label = $translated_label;
        }
      }

      $this->labelCache[$cache_key] = $label;
      return $label !== '' ? $label : NULL;
    }

    return NULL;
  }

  /**
   * Fallback operation label.
   */
  private function operationLabelFallback(string $operation_code): string {
    return match ($operation_code) {
      'LOC', 'RENT' => 'Rent',
      'VEN', 'SALE' => 'Sale',
      default => 'Offer',
    };
  }

  /**
   * Fallback asset label.
   */
  private function assetLabelFallback(string $asset_code): string {
    return match ($asset_code) {
      'BUR' => 'office',
      'ACT' => 'activity unit',
      'ENT', 'LOG' => 'warehouse',
      'COM' => 'retail unit',
      'COW' => 'coworking',
      'TER' => 'land',
      default => 'property',
    };
  }

  /**
   * Convert country ISO code to offer language code.
   */
  private function langcodeFromCountry(string $country_code): string {
    return match ($country_code) {
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
   * Unicode-safe ucfirst.
   */
  private function mbUcfirst(string $value): string {
    if ($value === '') {
      return '';
    }
    return mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1);
  }

  /**
   * Returns first scalar textual value from mixed migrate source input.
   */
  private function firstScalarString(mixed $value): string {
    if (is_array($value)) {
      foreach ($value as $candidate) {
        if (is_scalar($candidate) || $candidate === NULL) {
          return (string) $candidate;
        }
      }
      return '';
    }

    if ($value instanceof \SimpleXMLElement) {
      return trim((string) $value);
    }

    return is_scalar($value) || $value === NULL ? (string) $value : '';
  }

}
