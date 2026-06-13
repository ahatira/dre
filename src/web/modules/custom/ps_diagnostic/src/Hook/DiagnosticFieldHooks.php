<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_core\Service\OfferSectionHeadingBuilder;
use Drupal\views\ViewExecutable;

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

  /**
   * Renders certification labels as compact picker cards (entity browser).
   */
  #[Hook('preprocess_taxonomy_term')]
  public function preprocessTaxonomyTerm(array &$variables): void {
    if (($variables['view_mode'] ?? '') !== 'badge_picker') {
      return;
    }

    $term = $variables['elements']['#taxonomy_term'] ?? $variables['term'] ?? NULL;
    if (!$term || $term->bundle() !== 'certification_label') {
      return;
    }

    // Suppress the default linked h2 title; keep the name field in content.
    if (isset($variables['name'])) {
      $variables['content']['name'] = $variables['name'];
      $variables['name'] = '';
    }
    $variables['attributes']['class'][] = 'ps-cert-label-card';
  }

  /**
   * Attaches admin styles for the certification label entity browser widget.
   */
  #[Hook('field_widget_form_alter')]
  public function fieldWidgetFormAlter(array &$element, FormStateInterface $form_state, array $context): void {
    if (($context['items']->getName() ?? '') !== 'field_certification_labels') {
      return;
    }

    if (($context['widget']->getPluginId() ?? '') !== 'entity_browser_entity_reference') {
      return;
    }

    $element['#attached']['library'][] = 'ps_diagnostic/certification_label_browser';
  }

  /**
   * Attaches browser card styles when the certification labels view is rendered.
   */
  #[Hook('views_pre_render')]
  public function viewsPreRender(ViewExecutable $view): void {
    if ($view->id() !== 'ps_certification_labels_browser') {
      return;
    }

    $view->element['#attached']['library'][] = 'ps_diagnostic/certification_label_browser';
  }

}
