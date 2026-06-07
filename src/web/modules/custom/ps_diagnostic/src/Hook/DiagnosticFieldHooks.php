<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_core\Service\OfferSectionHeadingBuilder;

/**
 * Field preprocess hooks for diagnostics and certification labels on offers.
 */
final class DiagnosticFieldHooks {

  public function __construct(
    private readonly OfferSectionHeadingBuilder $sectionHeadingBuilder,
  ) {}

  #[Hook('preprocess_field')]
  public function preprocessField(array &$variables): void {
    if (($variables['element']['#bundle'] ?? '') !== 'offer') {
      return;
    }

    match ($variables['field_name'] ?? '') {
      'field_diagnostics' => $this->preprocessDiagnosticsField($variables),
      'field_certification_labels' => $this->preprocessCertificationLabelsField($variables),
      default => NULL,
    };
  }

  private function preprocessDiagnosticsField(array &$variables): void {
    $variables['attributes']['class'][] = 'ps-offer-section';
    $variables['attributes']['class'][] = 'ps-offer-section--energy';
    $variables['title_attributes']['class'][] = 'ps-offer-section__title';

    $variables['label'] = $this->sectionHeadingBuilder->buildTitleContent('energy');

    $variables['#cache']['tags'] = array_merge(
      $variables['#cache']['tags'] ?? [],
      $this->sectionHeadingBuilder->getCacheTags(),
    );
  }

  private function preprocessCertificationLabelsField(array &$variables): void {
    $variables['attributes']['class'][] = 'ps-certification-labels-section';
    $variables['label_display'] = 'hidden';
  }

}
