<?php

declare(strict_types=1);

namespace Drupal\bnp_admin;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 * Applies the BNP Admin baseline configuration on install.
 */
final class BnpAdminConfigurator {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly ThemeExtensionList $themeExtensionList,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Applies core and contrib baseline configuration.
   */
  public function applyBaseline(): void {
    $settings = $this->configFactory->get('bnp_admin.settings');

    $this->applyCoreSettings($settings);
    $this->applyGinBranding($settings);
    $this->applyGinLoginBranding($settings);
    $this->applySecurityBaseline();
  }

  /**
   * Applies security baseline overrides after contrib modules are enabled.
   */
  public function applySecurityBaseline(): void {
    $modulePath = $this->moduleExtensionList->getPath('bnp_admin');
    $storage = new FileStorage($modulePath . '/config/baseline/security');

    foreach ($storage->listAll() as $configName) {
      if (str_starts_with($configName, 'password_policy.password_policy.')
        || str_starts_with($configName, 'http_response_headers.response_header.')) {
        continue;
      }

      $data = $storage->read($configName);
      $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
    }

    $policyStorage = $this->entityTypeManager->getStorage('password_policy');
    foreach ($storage->listAll('password_policy.password_policy.') as $configName) {
      $data = $storage->read($configName);
      $policy = $policyStorage->load($data['id']);
      if ($policy === NULL) {
        $policy = $policyStorage->create($data);
      }
      else {
        foreach ($data as $key => $value) {
          if ($key !== 'uuid') {
            $policy->set($key, $value);
          }
        }
      }
      $policy->save();
    }

    $headerStorage = $this->entityTypeManager->getStorage('response_header');
    foreach ($storage->listAll('http_response_headers.response_header.') as $configName) {
      $data = $storage->read($configName);
      $header = $headerStorage->load($data['id']);
      if ($header === NULL) {
        $header = $headerStorage->create($data);
      }
      else {
        foreach ($data as $key => $value) {
          if ($key !== 'uuid') {
            $header->set($key, $value);
          }
        }
      }
      $header->save();
    }
  }

  /**
   * Re-applies baseline role permissions from config/install definitions.
   */
  public function applyRoleBaseline(): void {
    $modulePath = $this->moduleExtensionList->getPath('bnp_admin');
    $storage = new FileStorage($modulePath . '/config/install');
    $roleStorage = $this->entityTypeManager->getStorage('user_role');

    foreach ($storage->listAll('user.role.') as $configName) {
      $data = $storage->read($configName);
      if (empty($data['id'])) {
        continue;
      }

      $role = $roleStorage->load($data['id']);
      if ($role === NULL) {
        $role = $roleStorage->create([
          'id' => $data['id'],
          'label' => $data['label'] ?? $data['id'],
        ]);
      }

      $role->set('weight', $data['weight'] ?? 0);
      $role->set('is_admin', $data['is_admin'] ?? FALSE);

      foreach ($role->getPermissions() as $permission) {
        $role->revokePermission($permission);
      }
      foreach ($data['permissions'] ?? [] as $permission) {
        $role->grantPermission($permission);
      }

      $role->save();
    }
  }

  /**
   * Applies portable core administration defaults.
   */
  private function applyCoreSettings(ImmutableConfig $settings): void {
    if ($settings->get('core.user_register_admin_only')) {
      $this->configFactory->getEditable('user.settings')
        ->set('register', 'admin_only')
        ->save(TRUE);
    }

    if ($settings->get('core.node_use_admin_theme')) {
      $this->configFactory->getEditable('node.settings')
        ->set('use_admin_theme', TRUE)
        ->save(TRUE);
    }

    if ($settings->get('core.admin_theme') && $this->themeExtensionList->getPath($settings->get('core.admin_theme'))) {
      $this->configFactory->getEditable('system.theme')
        ->set('admin', $settings->get('core.admin_theme'))
        ->save(TRUE);
    }
  }

  /**
   * Applies Gin theme branding from module assets (portable paths).
   */
  private function applyGinBranding(ImmutableConfig $settings): void {
    if (!$settings->get('gin.enabled')) {
      return;
    }

    $modulePath = $this->moduleExtensionList->getPath('bnp_admin');
    $ginSettings = $this->configFactory->getEditable('gin.settings');

    $ginSettings
      ->set('logo', [
        'path' => $modulePath . '/images/logo.svg',
        'use_default' => FALSE,
      ])
      ->set('favicon', [
        'mimetype' => 'image/vnd.microsoft.icon',
        'path' => $modulePath . '/images/favicon.ico',
        'use_default' => FALSE,
      ])
      ->set('preset_accent_color', $settings->get('gin.preset_accent_color') ?? 'green')
      ->set('classic_toolbar', $settings->get('gin.classic_toolbar') ?? 'new')
      ->set('show_description_toggle', (bool) $settings->get('gin.show_description_toggle'))
      ->save(TRUE);
  }

  /**
   * Applies Gin Login branding from module assets (portable paths).
   */
  private function applyGinLoginBranding(ImmutableConfig $settings): void {
    if (!$settings->get('gin_login.enabled')) {
      return;
    }

    $modulePath = $this->moduleExtensionList->getPath('bnp_admin');
    $this->configFactory->getEditable('gin_login.settings')
      ->set('logo', [
        'use_default' => FALSE,
        'path' => $modulePath . '/images/logo-bnp.svg',
      ])
      ->set('brand_image', [
        'use_default' => (bool) $settings->get('gin_login.brand_image_use_default'),
      ])
      ->save(TRUE);
  }

}
