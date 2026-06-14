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
 * Migrates legacy monolithic homepage LB sections to S-D shell layouts.
 */
final class HomepageSdLayoutMigrator {

  /**
   * Legacy monolithic block plugin IDs mapped to § numbers.
   *
   * @var array<string, int>
   */
  public const LEGACY_PLUGIN_SECTIONS = [
    'ps_homepage_services_block' => 2,
    'ps_homepage_tools_block' => 3,
    'ps_homepage_offers_carousel_block' => 4,
    'ps_homepage_search_shortcuts_block' => 5,
    'ps_homepage_expert_journey_block' => 6,
    'ps_homepage_news_block' => 7,
    'ps_homepage_market_studies_block' => 8,
    'ps_homepage_faq_block' => 9,
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageLayoutPersister $layoutPersister,
    private readonly HomepageSectionLibraryTemplateBuilder $templateBuilder,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Migrates the configured front page homepage layout to S-D sections.
   */
  public function migrateFrontPage(): bool {
    $node = $this->loadFrontPageNode();
    return $node instanceof NodeInterface && $this->migrateNode($node);
  }

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

  /**
   * Migrates a single page node layout to S-D sections.
   */
  public function migrateNode(NodeInterface $node): bool {
    if (!$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages() as $langcode => $_language) {
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

      if ($section->getLayoutId() === 'ps_homepage_section') {
        $nextSections[] = $section;
        continue;
      }

      $converted = $this->convertLegacySection($section, $langcode);
      if ($converted instanceof Section) {
        $nextSections[] = $converted;
        $updated = TRUE;
        continue;
      }

      $nextSections[] = $section;
    }

    return $updated ? $nextSections : NULL;
  }

  /**
   * Converts a legacy monolithic section to an S-D shell section.
   */
  private function convertLegacySection(Section $section, string $langcode): ?Section {
    if ($section->getLayoutId() !== 'layout_onecol') {
      return NULL;
    }

    $components = $section->getComponents();
    if (count($components) !== 1) {
      return NULL;
    }

    $component = reset($components);
    if (!$component instanceof SectionComponent) {
      return NULL;
    }

    $configuration = $component->get('configuration');
    if (!is_array($configuration)) {
      return NULL;
    }

    $pluginId = (string) ($configuration['id'] ?? '');
    if ($pluginId === 'ps_homepage_search_hero_block') {
      return NULL;
    }

    $sectionNumber = self::LEGACY_PLUGIN_SECTIONS[$pluginId] ?? NULL;
    if ($sectionNumber === NULL) {
      return NULL;
    }

    return $this->templateBuilder->buildHomepageSection($sectionNumber, $langcode, $configuration);
  }

  /**
   * Loads the configured front page node.
   */
  private function loadFrontPageNode(): ?NodeInterface {
    $front = (string) $this->configFactory->get('system.site')->get('page.front');
    if (!preg_match('/^\/node\/(\d+)$/', $front, $matches)) {
      return NULL;
    }

    $node = $this->entityTypeManager->getStorage('node')->load((int) $matches[1]);
    return $node instanceof NodeInterface ? $node : NULL;
  }

}
