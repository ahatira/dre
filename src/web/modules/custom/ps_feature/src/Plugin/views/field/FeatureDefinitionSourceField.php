<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\field;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Displays the catalogue source as a badge.
 */
#[ViewsField('ps_feature_definition_source')]
final class FeatureDefinitionSourceField extends FieldPluginBase {

  use FeatureDefinitionFieldTrait;

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): string|FormattableMarkup {
    $definition = $this->getDefinition($values);
    if ($definition === NULL) {
      return '';
    }
    return new FormattableMarkup('<span class="badge badge--secondary">@source</span>', [
      '@source' => $this->getSourceLabel($definition->getSource()),
    ]);
  }

  /**
   * Gets the human-readable label for a catalogue source.
   */
  private function getSourceLabel(string $source): string {
    return match ($source) {
      FeatureDefinitionSource::BO => (string) $this->t('Back office'),
      FeatureDefinitionSource::XML => (string) $this->t('CRM XML'),
      FeatureDefinitionSource::LEGACY => (string) $this->t('Legacy'),
      default => $source,
    };
  }

}
