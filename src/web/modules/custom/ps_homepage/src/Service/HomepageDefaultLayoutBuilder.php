<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\layout_builder\Section;

/**
 * Builds the default 9-section homepage Layout Builder layout (S-D shell).
 */
final class HomepageDefaultLayoutBuilder {

  public function __construct(
    private readonly HomepageSectionLibraryTemplateBuilder $templateBuilder,
  ) {}

  /**
   * @return list<Section>
   *   Homepage LB sections.
   */
  public function buildSections(string $langcode = 'en'): array {
    return $this->templateBuilder->buildHomepageLayout($langcode);
  }

}
