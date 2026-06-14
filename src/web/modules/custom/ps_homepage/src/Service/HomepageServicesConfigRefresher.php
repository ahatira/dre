<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;

/**
 * Refreshes the §2 services section on the homepage layout from defaults.
 */
final class HomepageServicesConfigRefresher {

  private const BODY_PLUGIN_ID = 'ps_content_services_grid_block';

  private const BODY_PROVIDER = 'ps_content';

  private const HEADER_PLUGIN_ID = 'ps_homepage_section_header_block';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageBlockDefaultsLoader $defaultsLoader,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly HomepageLayoutPersister $layoutPersister,
  ) {}

  /**
   * Applies default §2 services configuration to the front page homepage.
   */
  public function refreshFrontPage(): bool {
    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }

    $updated = FALSE;
    foreach ($node->getTranslationLanguages() as $langcode => $_language) {
      $defaults = $this->defaultsLoader->forPlugin(self::BODY_PLUGIN_ID, $langcode);
      $nextSections = $this->buildRefreshedSections($node->getTranslation($langcode), $langcode, $defaults);
      if ($nextSections === NULL) {
        continue;
      }

      $this->layoutPersister->saveTranslationLayout($node, $langcode, $nextSections);
      $updated = TRUE;
    }

    return $updated;
  }

  /**
   * @param array<string, mixed> $defaults
   *
   * @return list<Section>|null
   */
  private function buildRefreshedSections(NodeInterface $translation, string $langcode, array $defaults): ?array {
    $field = $translation->get('layout_builder__layout');
    if (!$field instanceof SectionListInterface) {
      return NULL;
    }

    $bodyDefaults = $this->bodyDefaults($defaults);
    $headerDefaults = $this->headerDefaults($defaults);
    $updated = FALSE;
    $nextSections = [];

    foreach ($field->getSections() as $section) {
      if (!$section instanceof Section) {
        $nextSections[] = $section;
        continue;
      }

      if (!$this->sectionIsServices($section)) {
        $nextSections[] = $section;
        continue;
      }

      $sectionData = $section->toArray();
      $layoutSettings = $sectionData['layout_settings'] ?? [];
      if (is_array($layoutSettings)) {
        $layoutSettings['background'] = 'white';
        $sectionData['layout_settings'] = $layoutSettings;
      }
      $nextComponents = [];

      foreach ($section->getComponents() as $component) {
        if (!$component instanceof SectionComponent) {
          $nextComponents[] = $component->toArray();
          continue;
        }

        $componentData = $component->toArray();
        $configuration = $componentData['configuration'] ?? [];
        $pluginId = (string) ($configuration['id'] ?? '');

        if ($pluginId === self::HEADER_PLUGIN_ID) {
          $configuration = array_merge($configuration, $headerDefaults);
          $componentData['configuration'] = $configuration;
          $nextComponents[] = $componentData;
          $updated = TRUE;
          continue;
        }

        if ($pluginId === self::BODY_PLUGIN_ID) {
          $configuration = $bodyDefaults + $configuration;
          $configuration['items'] = $bodyDefaults['items'] ?? [];
          unset($configuration['title'], $configuration['subtitle']);
          $configuration['id'] = self::BODY_PLUGIN_ID;
          $configuration['provider'] = self::BODY_PROVIDER;
          $configuration['label'] = '';
          $configuration['label_display'] = FALSE;
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

    return $updated ? $nextSections : NULL;
  }

  /**
   * @param array<string, mixed> $defaults
   *
   * @return array<string, mixed>
   */
  private function bodyDefaults(array $defaults): array {
    $items = $defaults['items'] ?? [];
    return [
      'items' => is_array($items) ? $items : [],
    ];
  }

  /**
   * @param array<string, mixed> $defaults
   *
   * @return array<string, mixed>
   */
  private function headerDefaults(array $defaults): array {
    return [
      'title' => HomepageBlockConfiguration::string($defaults, 'title'),
      'subtitle' => HomepageBlockConfiguration::string($defaults, 'subtitle'),
      'align' => 'center',
      'accent' => 'bar',
    ];
  }

  private function sectionIsServices(Section $section): bool {
    foreach ($section->getComponents() as $component) {
      if (!$component instanceof SectionComponent) {
        continue;
      }
      $configuration = $component->get('configuration');
      if (is_array($configuration) && ($configuration['id'] ?? '') === self::BODY_PLUGIN_ID) {
        return TRUE;
      }
    }
    return FALSE;
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
