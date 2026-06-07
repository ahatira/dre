<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_diagnostic\Service\DiagnosticSectionIconBuilder;

/**
 * Field preprocess hooks for diagnostics on offer pages.
 */
final class DiagnosticFieldHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly DiagnosticSectionIconBuilder $sectionIconBuilder,
  ) {}

  /**
   * Offer full diagnostics field — section title, icon and layout class.
   */
  #[Hook('preprocess_field')]
  public function preprocessField(array &$variables): void {
    if (($variables['field_name'] ?? '') !== 'field_diagnostics') {
      return;
    }

    if (($variables['element']['#bundle'] ?? '') !== 'offer') {
      return;
    }

    $variables['attributes']['class'][] = 'ps-offer-section';
    $variables['attributes']['class'][] = 'ps-offer-section--energy';
    $variables['title_attributes']['class'][] = 'ps-offer-section__title';

    $section_label = trim((string) ($this->configFactory->get('ps_diagnostic.settings')->get('section_label') ?? ''));
    if ($section_label === '') {
      $section_label = (string) $this->t('Energy & diagnostics');
    }

    $variables['label'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section__title-content']],
      'icon' => $this->sectionIconBuilder->buildRenderable(),
      'text' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $section_label,
        '#attributes' => ['class' => ['ps-offer-section__title-text']],
      ],
    ];

    $variables['#cache']['tags'] = array_merge(
      $variables['#cache']['tags'] ?? [],
      $this->configFactory->get('ps_diagnostic.settings')->getCacheTags(),
    );
  }

}
