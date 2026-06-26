<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_market_study\Service\MarketStudyDefaultItems;
use Drupal\ps_market_study\Service\MarketStudyListPathResolver;

/**
 * Refreshes the market studies block configuration on the homepage layout.
 */
final class HomepageMarketStudyConfigRefresher {

  private const PLUGIN_ID = 'ps_market_study_market_studies_block';

  private const PLUGIN_PROVIDER = 'ps_market_study';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageBlockDefaultsLoader $defaultsLoader,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ?MarketStudyDefaultItems $marketStudyDefaultItems,
    private readonly ?MarketStudyListPathResolver $marketStudyListPathResolver,
    private readonly HomepageLayoutPersister $layoutPersister,
  ) {}

  /**
   * Applies default market study references to the front page homepage.
   */
  public function refreshFrontPage(): bool {
    if ($this->marketStudyDefaultItems === NULL || $this->marketStudyListPathResolver === NULL) {
      return FALSE;
    }

    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $studies = $this->marketStudyDefaultItems->resolve(2);
    if ($studies === []) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages() as $langcode => $_language) {
      $nextSections = $this->buildRefreshedSections($node->getTranslation($langcode), $langcode, $studies);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * @param list<array{weight: int, nid: int}> $studies
   *
   * @return list<Section>|null
   */
  private function buildRefreshedSections(NodeInterface $translation, string $langcode, array $studies): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $defaultConfig = $this->defaultsLoader->forPlugin(self::PLUGIN_ID, $langcode);
    $defaultConfig['studies'] = $studies;
    unset($defaultConfig['items']);
    $updated = FALSE;
    $nextSections = [];

    foreach ($field->getSections() as $section) {
      if (!$section instanceof Section) {
        $nextSections[] = $section;
        continue;
      }

      $sectionData = $section->toArray();
      $nextComponents = [];
      $sectionHasMarketStudies = $this->sectionHasMarketStudiesBlock($section);

      foreach ($section->getComponents() as $component) {
        if (!$component instanceof SectionComponent) {
          $nextComponents[] = $component->toArray();
          continue;
        }

        $componentData = $component->toArray();
        $configuration = $componentData['configuration'] ?? [];

        if (($configuration['id'] ?? '') === self::PLUGIN_ID) {
          $configuration = $defaultConfig + $configuration;
          $configuration['studies'] = $studies;
          unset($configuration['items']);
          $configuration['id'] = self::PLUGIN_ID;
          $configuration['provider'] = self::PLUGIN_PROVIDER;
          $configuration['label'] = '';
          $configuration['label_display'] = FALSE;
          $componentData['configuration'] = $configuration;
          $nextComponents[] = $componentData;
          $updated = TRUE;
          continue;
        }

        if ($sectionHasMarketStudies && ($configuration['id'] ?? '') === 'ps_homepage_section_footer_block') {
          $configuration['cta_url'] = $this->marketStudyListPathResolver->getPublicPath($langcode);
          $componentData['configuration'] = $configuration;
          $nextComponents[] = $componentData;
          $updated = TRUE;
          continue;
        }

        $nextComponents[] = $componentData;
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

  private function sectionHasMarketStudiesBlock(Section $section): bool {
    foreach ($section->getComponents() as $component) {
      if (!$component instanceof SectionComponent) {
        continue;
      }
      $configuration = $component->get('configuration');
      if (is_array($configuration) && ($configuration['id'] ?? '') === self::PLUGIN_ID) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
