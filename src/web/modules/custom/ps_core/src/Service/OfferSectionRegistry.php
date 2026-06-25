<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_core\Plugin\OfferSection\OfferSectionInterface;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Resolves offer section headings from plugins and site configuration.
 */
final class OfferSectionRegistry {

  use StringTranslationTrait;

  public function __construct(
    private readonly OfferSectionManager $sectionManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns all section plugins sorted by weight.
   *
   * @return \Drupal\ps_core\Plugin\OfferSection\OfferSectionInterface[]
   */
  public function getPlugins(): array {
    $definitions = $this->sectionManager->getDefinitions();
    uasort($definitions, static fn (array $a, array $b): int => ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0));

    $plugins = [];
    foreach (array_keys($definitions) as $plugin_id) {
      $plugins[$plugin_id] = $this->sectionManager->createInstance($plugin_id);
    }

    return $plugins;
  }

  /**
   * Returns the resolved label for a section.
   */
  public function getLabel(string $section_id): string {
    $plugin = $this->getPlugin($section_id);
    if ($plugin === NULL) {
      return '';
    }

    $sectionConfig = $this->getSectionConfig($section_id);
    if ($sectionConfig !== NULL) {
      $stored = trim((string) ($sectionConfig['label'] ?? ''));
      return $stored !== '' ? $stored : $plugin->getDefaultLabel();
    }

    $legacy = $this->getLegacyLabel($section_id);
    if ($legacy !== '') {
      return $legacy;
    }

    return $plugin->getDefaultLabel();
  }

  /**
   * Returns the resolved icon pack:id for a section.
   */
  public function getIconId(string $section_id): string {
    if ($this->getPlugin($section_id) === NULL) {
      return '';
    }

    $sectionConfig = $this->getSectionConfig($section_id);
    if ($sectionConfig !== NULL) {
      $stored = trim((string) ($sectionConfig['icon'] ?? ''));
      return $stored !== '' ? IconIdUtility::normalizeStoredIcon($stored, '') : '';
    }

    $legacy = $this->getLegacyIconId($section_id);
    if ($legacy !== '') {
      return IconIdUtility::normalizeStoredIcon($legacy, '');
    }

    return '';
  }

  /**
   * Returns the feature group ID used for transport in the location section.
   */
  public function getLocationTransportGroup(): string {
    $default = 'equipements';
    if ($this->getPlugin('location') === NULL) {
      return $default;
    }

    $sectionConfig = $this->getSectionConfig('location');
    if ($sectionConfig !== NULL) {
      $stored = trim((string) ($sectionConfig['transport_group'] ?? ''));
      if ($stored !== '') {
        return $stored;
      }
    }

    return $default;
  }

  /**
   * Returns cache tags for section settings.
   *
   * @return string[]
   */
  public function getCacheTags(): array {
    return $this->config()->getCacheTags();
  }

  /**
   * Loads a section plugin when registered.
   */
  public function getPlugin(string $section_id): ?OfferSectionInterface {
    if (!$this->sectionManager->hasDefinition($section_id)) {
      return NULL;
    }

    return $this->sectionManager->createInstance($section_id);
  }

  private function config(): ImmutableConfig {
    return $this->configFactory->get('ps_core.offer_section_settings');
  }

  /**
   * Returns stored section config when the section exists in settings.
   *
   * @return array<string, string>|null
   *   Section config, or NULL when the section was never configured.
   */
  private function getSectionConfig(string $section_id): ?array {
    $sections = (array) ($this->config()->get('sections') ?? []);
    if (!isset($sections[$section_id]) || !is_array($sections[$section_id])) {
      return NULL;
    }

    return $sections[$section_id];
  }

  /**
   * Backward compatibility for energy settings stored in ps_diagnostic.settings.
   */
  private function getLegacyLabel(string $section_id): string {
    if ($section_id !== 'energy') {
      return '';
    }

    return trim((string) ($this->configFactory->get('ps_diagnostic.settings')->get('section_label') ?? ''));
  }

  /**
   * Backward compatibility for energy icon stored in ps_diagnostic.settings.
   */
  private function getLegacyIconId(string $section_id): string {
    if ($section_id !== 'energy') {
      return '';
    }

    return trim((string) ($this->configFactory->get('ps_diagnostic.settings')->get('section_icon') ?? ''));
  }

}
