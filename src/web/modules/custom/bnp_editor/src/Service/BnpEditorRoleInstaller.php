<?php

declare(strict_types=1);

namespace Drupal\bnp_editor\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Grants BNP Editor text format permissions to baseline roles.
 */
final class BnpEditorRoleInstaller {

  /**
   * Text format permission suffixes mapped to machine names.
   */
  private const FORMAT_PERMISSIONS = [
    'full_html' => 'use text format full_html',
    'basic_html' => 'use text format basic_html',
    'restricted_html' => 'use text format restricted_html',
    'plain_text' => 'use text format plain_text',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Applies default text format permissions to known BNP/PS roles.
   */
  public function applyDefaultPermissions(): void {
    $roleStorage = $this->entityTypeManager->getStorage('user_role');

    foreach ($this->buildRolePermissionMap() as $roleId => $permissions) {
      $role = $roleStorage->load($roleId);
      if ($role === NULL) {
        continue;
      }

      foreach ($permissions as $permission) {
        if ($role->hasPermission($permission)) {
          continue;
        }
        $role->grantPermission($permission);
      }

      $role->save();
    }
  }

  /**
   * Builds the role → permission map for portable BNP baseline + PS roles.
   *
   * @return array<string, list<string>>
   *   Role machine names keyed to permission machine names.
   */
  public function buildRolePermissionMap(): array {
    $allFormats = array_values(self::FORMAT_PERMISSIONS);
    $editorFormats = [
      self::FORMAT_PERMISSIONS['full_html'],
      self::FORMAT_PERMISSIONS['basic_html'],
      self::FORMAT_PERMISSIONS['restricted_html'],
    ];
    $contributorFormats = [
      self::FORMAT_PERMISSIONS['basic_html'],
      self::FORMAT_PERMISSIONS['restricted_html'],
    ];

    $map = [
      'administrator' => array_merge(['administer bnp editor'], $allFormats),
      'site_admin' => array_merge(['administer bnp editor'], $editorFormats),
      'content_admin' => $editorFormats,
      'content_editor' => $contributorFormats,
      'translate_admin' => $contributorFormats,
      'translate_editor' => $contributorFormats,
      'seo_admin' => [
        self::FORMAT_PERMISSIONS['restricted_html'],
      ],
      'authenticated' => [
        self::FORMAT_PERMISSIONS['restricted_html'],
      ],
    ];

    return $map;
  }

}
