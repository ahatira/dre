<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

/**
 * Post-processes compare table sections (collapsible flags).
 */
final class CompareTableEnhancer {

  public function __construct(
    private readonly CompareDisplaySettings $displaySettings,
  ) {}

  /**
   * @param array<int, array<string, mixed>> $sections
   *
   * @return array{sections: array<int, array<string, mixed>>, summary: array<string, mixed>}
   */
  public function enhance(array $sections): array {
    return [
      'sections' => $this->applySectionCollapsibleFlags($sections),
      'summary' => [],
    ];
  }

  /**
   * @param array<int, array<string, mixed>> $sections
   *
   * @return array<int, array<string, mixed>>
   */
  private function applySectionCollapsibleFlags(array $sections): array {
    if (!$this->displaySettings->collapsibleSections()) {
      return $sections;
    }

    $featureOnly = $this->displaySettings->collapsibleFeatureOnly();
    foreach ($sections as &$section) {
      $sectionId = (string) ($section['id'] ?? '');
      $isFeatureGroup = str_starts_with($sectionId, 'group_');
      $section['collapsible'] = $featureOnly ? $isFeatureGroup : $sectionId !== 'actions';
      $section['collapsed_default'] = $isFeatureGroup;
    }
    unset($section);

    return $sections;
  }

}
