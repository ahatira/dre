<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

/**
 * Builds the default 9-section homepage Layout Builder layout.
 */
final class HomepageDefaultLayoutBuilder {

  /**
   * Stable component UUIDs for demo export and reinstall idempotency.
   *
   * @var array<string, string>
   */
  private const COMPONENT_UUIDS = [
    'ps_homepage_search_hero_block' => 'b2000002-0000-4000-8000-000000000001',
    'ps_homepage_services_block' => 'b2000003-0000-4000-8000-000000000001',
    'ps_homepage_tools_block' => 'b2000003-0000-4000-8000-000000000002',
    'ps_homepage_offers_carousel_block' => 'b2000003-0000-4000-8000-000000000003',
    'ps_homepage_search_shortcuts_block' => 'b2000003-0000-4000-8000-000000000004',
    'ps_homepage_expert_journey_block' => 'b2000003-0000-4000-8000-000000000005',
    'ps_homepage_news_block' => 'b2000003-0000-4000-8000-000000000006',
    'ps_homepage_market_studies_block' => 'b2000003-0000-4000-8000-000000000007',
    'ps_homepage_faq_block' => 'b2000003-0000-4000-8000-000000000008',
  ];

  /**
   * @var list<array{label: string, plugin_id: string}>
   */
  private const SECTIONS = [
    ['label' => 'Hero', 'plugin_id' => 'ps_homepage_search_hero_block'],
    ['label' => 'Services', 'plugin_id' => 'ps_homepage_services_block'],
    ['label' => 'Tools', 'plugin_id' => 'ps_homepage_tools_block'],
    ['label' => 'Offers', 'plugin_id' => 'ps_homepage_offers_carousel_block'],
    ['label' => 'Search shortcuts', 'plugin_id' => 'ps_homepage_search_shortcuts_block'],
    ['label' => 'Expert journey', 'plugin_id' => 'ps_homepage_expert_journey_block'],
    ['label' => 'News', 'plugin_id' => 'ps_homepage_news_block'],
    ['label' => 'Market studies', 'plugin_id' => 'ps_homepage_market_studies_block'],
    ['label' => 'FAQ', 'plugin_id' => 'ps_homepage_faq_block'],
  ];

  public function __construct(
    private readonly BlockManagerInterface $blockManager,
    private readonly UuidInterface $uuid,
  ) {}

  /**
   * @return list<\Drupal\layout_builder\Section>
   */
  public function buildSections(): array {
    $sections = [];

    foreach (self::SECTIONS as $definition) {
      $pluginId = $definition['plugin_id'];
      $plugin = $this->blockManager->createInstance($pluginId, []);
      $configuration = $plugin->getConfiguration();
      $configuration['id'] = $pluginId;
      $configuration['provider'] = 'ps_homepage';
      $configuration['label'] = '';
      $configuration['label_display'] = FALSE;

      $uuid = self::COMPONENT_UUIDS[$pluginId] ?? $this->uuid->generate();
      $section = new Section('layout_onecol', ['label' => $definition['label']]);
      $section->appendComponent(new SectionComponent($uuid, 'content', $configuration));
      $sections[] = $section;
    }

    return $sections;
  }

}
