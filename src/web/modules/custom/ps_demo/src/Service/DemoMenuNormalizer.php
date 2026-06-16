<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Structural demo menu fixes after default content import (no copy / i18n).
 */
final class DemoMenuNormalizer {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Moves footer links into ps_theme shell menus (business / about columns).
   */
  public function normalize(): void {
    $this->normalizeFooterColumns();
    \Drupal::service('plugin.manager.menu.link')->rebuild();
  }

  /**
   * Reorganises footer menu links into ps_theme column menus.
   */
  private function normalizeFooterColumns(): void {
    $storage = $this->entityTypeManager->getStorage('menu_link_content');

    $business_heading = 'a1000003-0000-4000-8000-000000000303';
    $about_heading = 'a1000003-0000-4000-8000-000000000301';
    $business_children = [
      'a1000003-0000-4000-8000-000000000331',
      'a1000003-0000-4000-8000-000000000321',
      'a1000003-0000-4000-8000-000000000322',
      'a1000003-0000-4000-8000-000000000323',
      'a1000003-0000-4000-8000-000000000324',
      'a1000003-0000-4000-8000-000000000325',
    ];
    $about_children = [
      'a1000003-0000-4000-8000-000000000332',
      'a1000003-0000-4000-8000-000000000333',
      'a1000003-0000-4000-8000-000000000313',
      'a1000003-0000-4000-8000-000000000314',
      'a1000003-0000-4000-8000-000000000315',
    ];
    $disable = [
      'a1000003-0000-4000-8000-000000000302',
      'a1000003-0000-4000-8000-000000000311',
      'a1000003-0000-4000-8000-000000000312',
    ];

    $this->moveHeading($storage, $business_heading, 'ps_footer_business', 0);
    $this->moveHeading($storage, $about_heading, 'ps_footer_about', 0);

    foreach ($business_children as $uuid) {
      $this->moveChild($storage, $uuid, 'ps_footer_business', $business_heading);
    }
    foreach ($about_children as $uuid) {
      $this->moveChild($storage, $uuid, 'ps_footer_about', $about_heading);
    }
    foreach ($disable as $uuid) {
      $this->disableLink($storage, $uuid);
    }
  }

  /**
   * Moves a footer column heading into a ps_theme shell menu.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
   * @param string $uuid
   *   Menu link UUID.
   * @param string $menu
   *   Target menu machine name.
   * @param int $weight
   *   Link weight.
   */
  private function moveHeading($storage, string $uuid, string $menu, int $weight): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('menu_name', $menu);
      $entity->set('enabled', TRUE);
      $entity->set('weight', $weight);
      $entity->save();
    }
  }

  /**
   * Moves a footer child link under a column heading.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
   * @param string $uuid
   *   Menu link UUID.
   * @param string $menu
   *   Target menu machine name.
   * @param string $parent_uuid
   *   Parent heading UUID.
   */
  private function moveChild($storage, string $uuid, string $menu, string $parent_uuid): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('menu_name', $menu);
      $entity->set('parent', 'menu_link_content:' . $parent_uuid);
      $entity->set('enabled', TRUE);
      $entity->save();
    }
  }

  /**
   * Disables a legacy footer menu link.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
   * @param string $uuid
   *   Menu link UUID.
   */
  private function disableLink($storage, string $uuid): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('enabled', FALSE);
      $entity->save();
    }
  }

}
