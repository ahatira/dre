<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\node\NodeInterface;

/**
 * Registers homepage section templates in Section Library.
 */
final class HomepageSectionLibraryInstaller {

  public function __construct(
    private readonly HomepageDefaultLayoutBuilder $layoutBuilder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function install(): void {
    $homepage = $this->loadHomepageNode();
    if (!$homepage instanceof NodeInterface) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('section_library_template');

    foreach ($this->layoutBuilder->buildSections() as $index => $section) {
      if (!$section instanceof Section) {
        continue;
      }

      $label = (string) ($section->getLayoutSettings()['label'] ?? '');
      $templateLabel = 'Homepage §' . ($index + 1) . ' — ' . $label;

      $existing = $storage->loadByProperties(['label' => $templateLabel]);
      if ($existing !== []) {
        continue;
      }

      $entity = $storage->create([
        'label' => $templateLabel,
        'type' => 'section',
        'layout_section' => $section,
        'entity_type' => 'node',
        'entity_id' => $homepage->id(),
      ]);

      $entity->save();
    }
  }

  private function loadHomepageNode(): ?NodeInterface {
    if (!\Drupal::moduleHandler()->moduleExists('ps_demo')) {
      return NULL;
    }

    $uuid = (string) (\Drupal::config('ps_demo.settings')->get('homepage_uuid') ?? '');
    if ($uuid === '') {
      return NULL;
    }

    $node = \Drupal::service('entity.repository')->loadEntityByUuid('node', $uuid);
    return $node instanceof NodeInterface ? $node : NULL;
  }

}
