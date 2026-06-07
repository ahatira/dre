<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Aligns imported demo menu links with the current ps_theme shell.
 */
final class DemoMenuNormalizer {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Normalizes footer business/about and legal menu labels after content import.
   */
  public function normalize(): void {
    $this->normalizeFooterColumns();
    $this->normalizeFooterLegalLabels();
    \Drupal::service('plugin.manager.menu.link')->rebuild();
  }

  /**
   * Moves footer links from legacy ps_footer_main into business / about menus.
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

    $this->updateHeading($storage, $business_heading, 'ps_footer_business', [
      'en' => 'Business websites',
      'fr' => 'Sites métier',
    ], 0);
    $this->updateHeading($storage, $about_heading, 'ps_footer_about', [
      'en' => 'About BNP Paribas Real Estate',
      'fr' => 'À propos de BNP Paribas Real Estate',
    ], 0);

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
   * Aligns footer legal menu labels with Stellar.
   */
  private function normalizeFooterLegalLabels(): void {
    $storage = $this->entityTypeManager->getStorage('menu_link_content');
    $updates = [
      'a1000005-0000-4000-8000-000000000501' => ['en' => 'Data protection', 'fr' => 'Données personnelles'],
      'a1000005-0000-4000-8000-000000000502' => ['en' => 'Cookie policy', 'fr' => 'Politique cookies'],
      'a1000005-0000-4000-8000-000000000503' => ['en' => 'Disclaimer', 'fr' => 'Avertissement'],
      'a1000005-0000-4000-8000-000000000504' => [
        'en' => 'Suppliers: BNP Paribas is committed to its partners and suppliers',
        'fr' => 'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs',
      ],
      'a1000005-0000-4000-8000-000000000505' => ['en' => 'Sitemap', 'fr' => 'Plan du site'],
      'a1000005-0000-4000-8000-000000000506' => ['en' => 'Complaints Customer Service', 'fr' => 'Réclamations Service Client'],
      'a1000005-0000-4000-8000-000000000507' => ['en' => 'Canal de denuncias éticas', 'fr' => 'Canal de dénonciation éthique'],
    ];

    foreach ($updates as $uuid => $titles) {
      $entities = $storage->loadByProperties(['uuid' => $uuid]);
      foreach ($entities as $entity) {
        foreach ($titles as $langcode => $title) {
          if ($entity->hasTranslation($langcode)) {
            $entity->getTranslation($langcode)->set('title', $title);
          }
          else {
            $entity->set('title', $title);
          }
        }
        $entity->save();
      }
    }
  }

  /**
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
   */
  private function updateHeading($storage, string $uuid, string $menu, array $titles, int $weight): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('menu_name', $menu);
      $entity->set('enabled', TRUE);
      $entity->set('weight', $weight);
      foreach ($titles as $langcode => $title) {
        if ($entity->hasTranslation($langcode)) {
          $entity->getTranslation($langcode)->set('title', $title);
        }
      }
      $entity->save();
    }
  }

  /**
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
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
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   Menu link storage.
   */
  private function disableLink($storage, string $uuid): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('enabled', FALSE);
      $entity->save();
    }
  }

}
