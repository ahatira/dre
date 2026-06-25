<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Displays required asset types as badges.
 */
#[ViewsField('ps_feature_definition_asset_types')]
final class FeatureDefinitionAssetTypesField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): string|FormattableMarkup {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }

    $required_asset_types = $definition->getRequiredAssetTypes();
    if ($required_asset_types === []) {
      return new FormattableMarkup('<span class="badge badge--info">@text</span>', [
        '@text' => $this->t('All types'),
      ]);
    }

    $badges = [];
    foreach ($required_asset_types as $code) {
      $badges[] = '<span class="badge badge--warning">' . htmlspecialchars($this->getAssetTypeLabel($code), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
    }
    return new FormattableMarkup(implode(' ', $badges), []);
  }

  /**
   * Gets the human-readable label for an asset type code.
   */
  private function getAssetTypeLabel(string $code): string {
    $labels = [
      'BUR' => (string) $this->t('Office'),
      'COW' => (string) $this->t('Coworking'),
      'ENT' => (string) $this->t('Warehouse/Logistics'),
      'ACT' => (string) $this->t('Activity unit'),
      'COM' => (string) $this->t('Retail unit'),
      'TER' => (string) $this->t('Land'),
    ];
    return $labels[$code] ?? $code;
  }

}
