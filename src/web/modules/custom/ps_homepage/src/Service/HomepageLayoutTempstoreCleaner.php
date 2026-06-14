<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\TempStore\SharedTempStoreFactory;
use Drupal\node\NodeInterface;

/**
 * Clears stale Layout Builder tempstore overrides for homepage nodes.
 */
final class HomepageLayoutTempstoreCleaner {

  private const TEMPSTORE_COLLECTION = 'layout_builder.section_storage.overrides';

  /**
   * Legacy monolithic block plugin IDs removed during modularization.
   *
   * @var list<string>
   */
  private const LEGACY_PLUGIN_IDS = [
    'ps_homepage_services_block',
    'ps_homepage_tools_block',
    'ps_homepage_offers_carousel_block',
    'ps_homepage_search_shortcuts_block',
    'ps_homepage_expert_journey_block',
    'ps_homepage_news_block',
    'ps_homepage_market_studies_block',
    'ps_homepage_faq_block',
  ];

  public function __construct(
    private readonly SharedTempStoreFactory $tempStoreFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Clears LB tempstore overrides for the configured front page node.
   */
  public function clearFrontPageOverrides(): int {
    $node = $this->loadFrontPageNode();
    if (!$node instanceof NodeInterface) {
      return 0;
    }

    return $this->clearNodeOverrides($node);
  }

  /**
   * Clears LB tempstore overrides for a page node (all translations).
   */
  public function clearNodeOverrides(NodeInterface $node): int {
    if (!$node->hasField('layout_builder__layout')) {
      return 0;
    }

    $tempstore = $this->tempStoreFactory->get(self::TEMPSTORE_COLLECTION);
    $cleared = 0;

    foreach ($node->getTranslationLanguages() as $langcode => $_language) {
      $key = sprintf('node.%d.full.%s', (int) $node->id(), $langcode);
      try {
        $data = $tempstore->get($key);
      }
      catch (\Exception) {
        continue;
      }

      if ($data === NULL) {
        continue;
      }

      if (!$this->containsLegacyPlugins($data)) {
        continue;
      }

      $tempstore->delete($key);
      $cleared++;
    }

    return $cleared;
  }

  /**
   * Determines whether tempstore data references removed block plugins.
   */
  private function containsLegacyPlugins(mixed $data): bool {
    $encoded = serialize($data);
    foreach (self::LEGACY_PLUGIN_IDS as $pluginId) {
      if (str_contains($encoded, $pluginId)) {
        return TRUE;
      }
    }

    return FALSE;
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
