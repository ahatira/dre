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
    private readonly HomepageSectionLibraryTemplateBuilder $templateBuilder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Installs S-D shell section library templates.
   */
  public function install(): void {
    $homepage = $this->loadHomepageNode();
    if (!$homepage instanceof NodeInterface) {
      return;
    }

    $this->installSdTemplates($homepage);
  }

  /**
   * Refreshes existing S-D section library templates (upsert by label).
   *
   * @param list<int> $sectionNumbers
   *   Optional § numbers to limit refresh (empty = all SD templates).
   */
  public function refreshSdTemplates(array $sectionNumbers = []): void {
    $homepage = $this->loadHomepageNode();
    if (!$homepage instanceof NodeInterface) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('section_library_template');

    foreach ($this->templateBuilder->buildTemplateSections() as $template) {
      $label = $template['label'];
      if ($sectionNumbers !== []) {
        if (!preg_match('/^Homepage SD §(\d+) —/', $label, $matches)) {
          continue;
        }
        if (!in_array((int) $matches[1], $sectionNumbers, TRUE)) {
          continue;
        }
      }

      $existing = $storage->loadByProperties(['label' => $label]);
      if ($existing !== []) {
        $entity = reset($existing);
        $entity->set('layout_section', $template['section']);
        $entity->save();
        continue;
      }

      $this->createTemplateIfMissing($storage, $label, $template['section'], $homepage);
    }
  }

  private function installSdTemplates(NodeInterface $homepage): void {
    $storage = $this->entityTypeManager->getStorage('section_library_template');

    foreach ($this->templateBuilder->buildTemplateSections() as $template) {
      $this->createTemplateIfMissing(
        $storage,
        $template['label'],
        $template['section'],
        $homepage,
      );
    }
  }

  /**
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   */
  private function createTemplateIfMissing($storage, string $templateLabel, Section $section, NodeInterface $homepage): void {
    $existing = $storage->loadByProperties(['label' => $templateLabel]);
    if ($existing !== []) {
      return;
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
