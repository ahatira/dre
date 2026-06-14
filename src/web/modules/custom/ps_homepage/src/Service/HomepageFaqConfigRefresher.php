<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_faq\Service\FaqDefaultItems;

/**
 * Refreshes the FAQ block configuration on the homepage layout.
 */
final class HomepageFaqConfigRefresher {

  private const PLUGIN_ID = 'ps_faq_faq_block';

  private const PLUGIN_PROVIDER = 'ps_faq';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageBlockDefaultsLoader $defaultsLoader,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FaqDefaultItems $faqDefaultItems,
    private readonly HomepageLayoutPersister $layoutPersister,
  ) {}

  /**
   * Applies default FAQ configuration to the front page homepage.
   */
  public function refreshFrontPage(): bool {
    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $faqItems = $this->faqDefaultItems->resolve();
    if ($faqItems === []) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages() as $langcode => $_language) {
      $nextSections = $this->buildRefreshedSections($node->getTranslation($langcode), $langcode, $faqItems);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * @param list<array<string, mixed>> $faqItems
   *
   * @return list<Section>|null
   */
  private function buildRefreshedSections(NodeInterface $translation, string $langcode, array $faqItems): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $defaultConfig = $this->defaultsLoader->forPlugin(self::PLUGIN_ID, $langcode);
    $defaultConfig['faq_items'] = $faqItems;
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
        $configuration['faq_items'] = $faqItems;
        $configuration['id'] = self::PLUGIN_ID;
        $configuration['provider'] = self::PLUGIN_PROVIDER;
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
