<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
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
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityRepositoryInterface $entityRepository,
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
   * Removes legacy monolithic section library templates (pre S-D migration).
   */
  public function removeLegacyTemplates(): int {
    $storage = $this->entityTypeManager->getStorage('section_library_template');
    $removed = 0;

    foreach ($storage->loadMultiple() as $entity) {
      $label = (string) $entity->label();
      if (!preg_match('/^Homepage §\d+ —/', $label)) {
        continue;
      }
      if (str_starts_with($label, 'Homepage SD')) {
        continue;
      }

      $entity->delete();
      $removed++;
    }

    return $removed;
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

  /**
   *
   */
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

  /**
   *
   */
  private function loadHomepageNode(): ?NodeInterface {
    $uuid = HomepageShellInstaller::homepageUuid($this->configFactory);
    if ($uuid === '') {
      return NULL;
    }

    try {
      $node = $this->entityRepository->loadEntityByUuid('node', $uuid);
    }
    catch (\Exception) {
      $node = NULL;
    }

    return $node instanceof NodeInterface ? $node : NULL;
  }

}
