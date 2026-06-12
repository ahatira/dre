<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Provisions PS Form webforms from module install configuration.
 */
final class PsFormWebformProvisioner {

  /**
   * Webform machine names shipped with ps_form.
   */
  public const REQUIRED_WEBFORMS = [
    'contact',
    'offer_contact',
    'schedule_visit',
    'search_alert',
  ];

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Creates missing webforms from config/install definitions.
   *
   * @return array<int, string>
   *   Machine names of webforms created during this run.
   */
  public function provisionMissing(): array {
    if (!$this->moduleHandler->moduleExists('webform')) {
      return [];
    }

    $storage = $this->entityTypeManager->getStorage('webform');
    $created = [];

    foreach ($this->getInstallDefinitions() as $id => $definition) {
      if ($storage->load($id) !== NULL) {
        continue;
      }

      $webform = $storage->create($definition);
      $webform->save();
      $created[] = $id;
    }

    return $created;
  }

  /**
   * Returns required webform IDs that are not present in the site.
   *
   * @return array<int, string>
   *   Missing webform machine names.
   */
  public function getMissingWebformIds(): array {
    if (!$this->moduleHandler->moduleExists('webform')) {
      return self::REQUIRED_WEBFORMS;
    }

    $storage = $this->entityTypeManager->getStorage('webform');
    $missing = [];

    foreach (self::REQUIRED_WEBFORMS as $id) {
      if ($storage->load($id) === NULL) {
        $missing[] = $id;
      }
    }

    return $missing;
  }

  /**
   * Loads webform install definitions from config/install YAML files.
   *
   * @return array<string, array<string, mixed>>
   *   Webform data keyed by machine name.
   */
  private function getInstallDefinitions(): array {
    $install_path = $this->moduleExtensionList->getPath('ps_form') . '/config/install';
    $pattern = $install_path . '/webform.webform.*.yml';
    $definitions = [];

    foreach (glob($pattern) ?: [] as $file) {
      $data = Yaml::parseFile($file);
      if (!is_array($data) || empty($data['id']) || !is_string($data['id'])) {
        continue;
      }

      $definitions[$data['id']] = $data;
    }

    return $definitions;
  }

}
