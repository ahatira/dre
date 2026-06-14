<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;

/**
 * Keeps homepage layout structure aligned across node translations.
 */
final class HomepageLayoutStructureSynchronizer {

  private static bool $suspended = FALSE;

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function suspend(): void {
    self::$suspended = TRUE;
  }

  public static function resume(): void {
    self::$suspended = FALSE;
  }

  public function synchronize(NodeInterface $node): void {
    if (self::$suspended || !$node->hasField('layout_builder__layout')) {
      return;
    }

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    if ($node->language()->getId() !== $defaultLangcode) {
      return;
    }

    $sourceField = $node->get('layout_builder__layout');
    if (!$sourceField instanceof SectionListInterface) {
      return;
    }

    $sourceSections = $sourceField->getSections();
    if ($sourceSections === []) {
      return;
    }

    $storedNode = $this->entityTypeManager->getStorage('node')->load($node->id());
    if (!$storedNode instanceof NodeInterface) {
      return;
    }

    foreach ($node->getTranslationLanguages(FALSE) as $langcode => $_language) {
      if ($langcode === $defaultLangcode) {
        continue;
      }

      if (!$storedNode->hasTranslation($langcode)) {
        continue;
      }

      $storedTranslation = $storedNode->getTranslation($langcode);
      $targetField = $storedTranslation->get('layout_builder__layout');
      if (!$targetField instanceof SectionListInterface) {
        continue;
      }

      $translation = $node->getTranslation($langcode);
      $translationField = $translation->get('layout_builder__layout');
      if (!$translationField instanceof SectionListInterface) {
        continue;
      }

      $translationField->setValue($this->mergeSections(
        $sourceSections,
        $targetField->getSections(),
      ));
    }
  }

  /**
   * @param list<\Drupal\layout_builder\Section|\Drupal\layout_builder\SectionStorageInterface> $sourceSections
   * @param list<\Drupal\layout_builder\Section|\Drupal\layout_builder\SectionStorageInterface> $targetSections
   *
   * @return list<\Drupal\layout_builder\Section>
   */
  private function mergeSections(array $sourceSections, array $targetSections): array {
    $targetByUuid = [];
    foreach ($targetSections as $targetSection) {
      if (!$targetSection instanceof Section) {
        continue;
      }
      foreach ($targetSection->getComponents() as $component) {
        if ($component instanceof SectionComponent) {
          $componentData = $component->toArray();
          $targetByUuid[$component->getUuid()] = $componentData['configuration'] ?? [];
        }
      }
    }

    $merged = [];
    foreach ($sourceSections as $sourceSection) {
      if (!$sourceSection instanceof Section) {
        continue;
      }

      $sectionData = $sourceSection->toArray();
      $nextComponents = [];

      foreach ($sourceSection->getComponents() as $component) {
        if (!$component instanceof SectionComponent) {
          continue;
        }

        $uuid = $component->getUuid();
        $componentData = $component->toArray();
        $sourceConfig = $componentData['configuration'] ?? [];
        $targetConfig = $targetByUuid[$uuid] ?? [];
        $pluginId = is_array($sourceConfig) ? (string) ($sourceConfig['id'] ?? '') : '';

        if (HomepageBlockConfiguration::shouldSynchronizeBlockConfiguration($pluginId)) {
          $mergedConfig = HomepageBlockConfiguration::applyNeutralValues(
            is_array($targetConfig) ? $targetConfig : [],
            is_array($sourceConfig) ? $sourceConfig : [],
            $pluginId,
          );
          $mergedConfig['id'] = $pluginId;
          $mergedConfig['provider'] = $sourceConfig['provider'] ?? 'ps_homepage';
        }
        else {
          $mergedConfig = is_array($targetConfig) && $targetConfig !== []
            ? $targetConfig
            : (is_array($sourceConfig) ? $sourceConfig : []);
        }

        $componentData['configuration'] = $mergedConfig;
        $nextComponents[] = $componentData;
      }

      $sectionData['components'] = $nextComponents;
      $merged[] = Section::fromArray($sectionData);
    }

    return $merged;
  }

}
