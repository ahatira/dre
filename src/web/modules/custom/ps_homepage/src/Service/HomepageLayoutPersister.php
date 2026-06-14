<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\node\NodeInterface;

/**
 * Persists Layout Builder sections per translation without cross-language bleed.
 */
final class HomepageLayoutPersister {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * @param list<\Drupal\layout_builder\Section> $sections
   */
  public function saveTranslationLayout(NodeInterface $node, string $langcode, array $sections): void {
    if (!$node->hasTranslation($langcode) || !$node->hasField('layout_builder__layout')) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $editable = $storage->load($node->id());
    if (!$editable instanceof NodeInterface) {
      return;
    }

    $editable->getTranslation($langcode)->get('layout_builder__layout')->setValue(
      $this->cloneSections($sections),
    );

    HomepageLayoutStructureSynchronizer::suspend();
    try {
      $editable->save();
    }
    finally {
      HomepageLayoutStructureSynchronizer::resume();
    }
  }

  /**
   * @param callable(string): list<Section> $sectionsBuilder
   */
  public function saveAllTranslationLayouts(NodeInterface $node, callable $sectionsBuilder): void {
    foreach (array_keys($node->getTranslationLanguages()) as $langcode) {
      $this->saveTranslationLayout($node, $langcode, $sectionsBuilder($langcode));
    }
  }

  /**
   * @param list<Section> $sections
   *
   * @return list<Section>
   */
  public function cloneSections(array $sections): array {
    return array_map(
      static fn (Section $section): Section => Section::fromArray($section->toArray()),
      $sections,
    );
  }

}
