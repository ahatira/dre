<?php

declare(strict_types=1);

namespace Drupal\bnp_admin\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Extension\ModuleExtensionList;
use Psr\Log\LoggerInterface;

/**
 * Imports BNP RBAC role YAML after Property Search modules are enabled.
 *
 * Replaces drush config:import --partial on bnp_admin/config/rbac.
 */
final class RbacRoleImporter {

  /**
   * Role config objects shipped in config/rbac/.
   */
  private const ROLE_CONFIGS = [
    'user.role.administrator',
    'user.role.site_admin',
    'user.role.content_admin',
    'user.role.content_editor',
    'user.role.seo_admin',
    'user.role.translate_admin',
    'user.role.translate_editor',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Applies role definitions from bnp_admin/config/rbac/.
   *
   * @return int
   *   Number of role configs imported.
   */
  public function import(): int {
    $rbacPath = $this->moduleExtensionList->getPath('bnp_admin') . '/config/rbac';
    if (!is_dir($rbacPath)) {
      throw new \RuntimeException(sprintf('RBAC directory not found: %s', $rbacPath));
    }

    $fileStorage = new FileStorage($rbacPath);
    $imported = 0;

    foreach (self::ROLE_CONFIGS as $configName) {
      $data = $fileStorage->read($configName);
      if (!is_array($data)) {
        throw new \RuntimeException(sprintf('Missing RBAC config: %s', $configName));
      }

      $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
      $imported++;
    }

    if ($imported > 0) {
      $this->logger->notice('bnp_admin: imported @count RBAC role configs from config/rbac.', [
        '@count' => $imported,
      ]);
    }

    return $imported;
  }

}
