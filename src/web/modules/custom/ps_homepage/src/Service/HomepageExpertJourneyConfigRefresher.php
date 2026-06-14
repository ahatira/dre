<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;

/**
 * Refreshes the expert journey block configuration on the homepage layout.
 */
final class HomepageExpertJourneyConfigRefresher {

  private const PLUGIN_ID = 'ps_homepage_expert_journey_block';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageDefaultLayoutBuilder $layoutBuilder,
    private readonly HomepageBlockDefaultsLoader $defaultsLoader,
    private readonly HomepageLayoutPersister $layoutPersister,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Applies default expert journey configuration to the front page homepage.
   */
  public function refreshFrontPage(): bool {
    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages(FALSE) as $langcode => $_language) {
      $nextSections = $this->buildRefreshedSections($node->getTranslation($langcode), $langcode);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * Rebuilds the full homepage layout (demo/install path).
   */
  public function rebuildFrontPageLayout(): bool {
    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $this->layoutPersister->saveAllTranslationLayouts(
      $node,
      fn (string $langcode): array => $this->layoutBuilder->buildSections($langcode),
    );

    return TRUE;
  }

  /**
   * @return array<string, mixed>
   */
  private function defaultConfiguration(string $langcode): array {
    return $this->defaultsLoader->forPlugin(self::PLUGIN_ID, $langcode);
  }

  /**
   * @return list<Section>|null
   */
  private function buildRefreshedSections(NodeInterface $translation, string $langcode): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $defaultConfig = $this->defaultConfiguration($langcode);
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
        if (($configuration['id'] ?? '') !== self::PLUGIN_ID) {
          $nextComponents[] = $componentData;
          continue;
        }

        $configuration = $defaultConfig + $configuration;
        $configuration['steps'] = $defaultConfig['steps'] ?? [];
        $configuration['id'] = self::PLUGIN_ID;
        $configuration['provider'] = 'ps_homepage';
        $configuration['label'] = '';
        $configuration['label_display'] = FALSE;
        $componentData['configuration'] = $configuration;
        $nextComponents[] = $componentData;
        $updated = TRUE;
      }

      $sectionData['components'] = $nextComponents;
      $nextSections[] = Section::fromArray($sectionData);
    }

    if ($updated) {
      return $nextSections;
    }

    return NULL;
  }

  private function loadFrontPageNode(): ?NodeInterface {
    $front = (string) $this->configFactory->get('system.site')->get('page.front');
    if (!preg_match('/^\/node\/(\d+)$/', $front, $matches)) {
      return NULL;
    }

    $node = $this->entityTypeManager->getStorage('node')->load((int) $matches[1]);
    return $node instanceof NodeInterface ? $node : NULL;
  }

}
