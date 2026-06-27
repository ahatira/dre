<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Extension\ModuleExtensionList;

/**
 * Applies PS Form anti-spam baseline on top of contrib defaults.
 */
final class PsFormSpamConfigurator {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleExtensionList $moduleExtensionList,
  ) {}

  /**
   * Applies spam module settings and captcha points from baseline YAML.
   */
  public function applyBaseline(): void {
    $modulePath = $this->moduleExtensionList->getPath('ps_form');
    $storage = new FileStorage($modulePath . '/config/baseline/spam');

    foreach ($storage->listAll() as $configName) {
      $data = $storage->read($configName);
      $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
    }

    $this->applyWebformThirdPartySettings();
  }

  /**
   * Enables global Webform third-party spam protections.
   */
  public function applyWebformThirdPartySettings(): void {
    $settings = $this->configFactory->getEditable('webform.settings');
    $thirdParty = $settings->get('third_party_settings') ?? [];

    $thirdParty['honeypot'] = [
      'honeypot' => TRUE,
      'time_restriction' => TRUE,
    ];
    $thirdParty['antibot'] = [
      'antibot' => TRUE,
    ];
    $thirdParty['captcha'] = [
      'replace_administration_mode' => TRUE,
    ];

    $settings->set('third_party_settings', $thirdParty)->save(TRUE);
  }

}
