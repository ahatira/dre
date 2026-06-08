<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_feature\Entity\FeatureGroup;

/**
 * Resolves human-readable labels for feature groups in the UI.
 */
final class FeatureGroupDisplayLabelResolver {

  /**
   * Install config labels keyed by group ID (request-scoped cache).
   *
   * @var array<string, string>|null
   */
  private ?array $installLabels = NULL;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ModuleExtensionList $moduleExtensionList,
  ) {}

  /**
   * Returns the display label for a feature group.
   */
  public function resolve(FeatureGroup $group): string {
    $id = (string) $group->id();
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    if ($langcode !== 'en') {
      $translated = $this->getLanguageOverrideLabel($id, $langcode);
      if ($translated !== NULL) {
        return $translated;
      }
    }

    $label = trim((string) $group->label());
    if ($label !== '' && !$this->isMachineLabel($label, $id)) {
      return $label;
    }

    $configLabel = $this->configFactory->get('ps_feature.feature_group.' . $id)->get('label');
    if (is_string($configLabel) && trim($configLabel) !== '') {
      $configLabel = trim($configLabel);
      if (!$this->isMachineLabel($configLabel, $id)) {
        return $configLabel;
      }
    }

    $installLabel = $this->getInstallLabel($id);
    if ($installLabel !== NULL) {
      return $installLabel;
    }

    return $this->humanizeId($id);
  }

  /**
   * Detects CRM-style machine labels (e.g. AMENAGEMENTS, ACCES_VEHICULES).
   */
  public function isMachineLabel(string $label, string $id): bool {
    if ($label === '') {
      return TRUE;
    }

    if ($label === strtoupper($label) && preg_match('/^[A-Z0-9_]+$/', $label)) {
      return TRUE;
    }

    $normalizedId = strtoupper(str_replace('-', '_', $id));
    $normalizedLabel = strtoupper(str_replace([' ', '-'], '_', $label));
    return $normalizedLabel === $normalizedId;
  }

  /**
   * Returns a translated label from config overrides when available.
   */
  private function getLanguageOverrideLabel(string $id, string $langcode): ?string {
    $override = $this->languageManager->getLanguageConfigOverride($langcode, 'ps_feature.feature_group.' . $id);
    $label = $override->get('label');
    if (!is_string($label) || trim($label) === '') {
      return NULL;
    }
    $label = trim($label);
    if ($this->isMachineLabel($label, $id)) {
      return NULL;
    }
    return $label;
  }

  /**
   * Reads the default EN label shipped with the module.
   */
  private function getInstallLabel(string $id): ?string {
    $labels = $this->loadInstallLabels();
    return $labels[$id] ?? NULL;
  }

  /**
   * Loads install config labels for all feature groups.
   *
   * @return array<string, string>
   */
  private function loadInstallLabels(): array {
    if ($this->installLabels !== NULL) {
      return $this->installLabels;
    }

    $this->installLabels = [];
    $install_dir = DRUPAL_ROOT . '/' . $this->moduleExtensionList->getPath('ps_feature') . '/config/install';
    if (!is_dir($install_dir)) {
      return $this->installLabels;
    }

    foreach (glob($install_dir . '/ps_feature.feature_group.*.yml') ?: [] as $file) {
      $data = Yaml::decode((string) file_get_contents($file));
      if (!is_array($data)) {
        continue;
      }
      $group_id = (string) ($data['id'] ?? '');
      $label = trim((string) ($data['label'] ?? ''));
      if ($group_id !== '' && $label !== '') {
        $this->installLabels[$group_id] = $label;
      }
    }

    return $this->installLabels;
  }

  /**
   * Humanizes a group machine ID as a last-resort fallback.
   */
  private function humanizeId(string $id): string {
    $words = explode('_', str_replace('-', '_', $id));
    $words = array_map(static fn(string $word): string => ucfirst(strtolower($word)), $words);
    return implode(' ', $words);
  }

}
