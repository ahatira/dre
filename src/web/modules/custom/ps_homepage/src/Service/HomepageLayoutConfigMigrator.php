<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;

/**
 * Migrates legacy bilingual homepage block configuration to monolingual layouts.
 */
final class HomepageLayoutConfigMigrator {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageLayoutPersister $layoutPersister,
  ) {}

  /**
   * Migrates all page nodes with a Layout Builder field.
   */
  public function migrateAllPageLayouts(): int {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'page')
      ->exists('layout_builder__layout')
      ->execute();

    $count = 0;
    foreach ($storage->loadMultiple($nids) as $node) {
      if ($node instanceof NodeInterface && $this->migrateNode($node)) {
        $count++;
      }
    }

    return $count;
  }

  public function migrateNode(NodeInterface $node): bool {
    if (!$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages(FALSE) as $langcode => $_language) {
      $translation = $node->getTranslation($langcode);
      $nextSections = $this->migrateTranslationLayout($translation);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * @return list<Section>|null
   *   Migrated sections, or NULL when no migration was required.
   */
  private function migrateTranslationLayout(NodeInterface $translation): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $langcode = $translation->language()->getId();
    $updated = FALSE;
    $nextSections = [];

    foreach ($field->getSections() as $section) {
      if (!$section instanceof Section) {
        $nextSections[] = $section;
        continue;
      }

      $sectionData = $section->toArray();
      $nextComponents = [];

      foreach ($section->getComponents() as $component) {
        if (!$component instanceof SectionComponent) {
          $nextComponents[] = $component->toArray();
          continue;
        }

        $componentData = $component->toArray();
        $configuration = $componentData['configuration'] ?? [];
        if (!is_array($configuration)) {
          $nextComponents[] = $componentData;
          continue;
        }

        $pluginId = (string) ($configuration['id'] ?? '');
        if (!HomepageBlockConfiguration::isHomepagePlugin($pluginId)) {
          $nextComponents[] = $componentData;
          continue;
        }

        if (!HomepageBlockConfiguration::usesLegacyLocalizedKeys($configuration)) {
          $nextComponents[] = $componentData;
          continue;
        }

        $componentData['configuration'] = HomepageBlockConfiguration::migrateForLanguage(
          $configuration,
          $langcode,
          $pluginId,
        );
        $nextComponents[] = $componentData;
        $updated = TRUE;
      }

      $sectionData['components'] = $nextComponents;
      $nextSections[] = Section::fromArray($sectionData);
    }

    return $updated ? $nextSections : NULL;
  }

}
