<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;

$repoRoot = dirname(__DIR__);
$configFactory = \Drupal::configFactory();

$targets = [
  'search_api.index.ps_offer_search' => $repoRoot . '/web/modules/custom/ps_search/config/optional/search_api.index.ps_offer_search.yml',
  'views.view.ps_offer_search' => $repoRoot . '/web/modules/custom/ps_search/config/optional/views.view.ps_offer_search.yml',
  'ps_dictionary.entry.property_type_act' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_act.yml',
  'ps_dictionary.entry.property_type_bur' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_bur.yml',
  'ps_dictionary.entry.property_type_com' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_com.yml',
  'ps_dictionary.entry.property_type_cow' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_cow.yml',
  'ps_dictionary.entry.property_type_log' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_log.yml',
  'ps_dictionary.entry.property_type_res' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_res.yml',
  'ps_dictionary.entry.property_type_ter' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.property_type_ter.yml',
  'ps_dictionary.entry.transaction_type_loc' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.transaction_type_loc.yml',
  'ps_dictionary.entry.transaction_type_ven' => $repoRoot . '/web/modules/custom/ps_dictionary/config/install/ps_dictionary.entry.transaction_type_ven.yml',
];

foreach ($targets as $configName => $filePath) {
  if (!is_file($filePath)) {
    throw new RuntimeException(sprintf('Missing file for %s: %s', $configName, $filePath));
  }

  $contents = file_get_contents($filePath);
  if ($contents === false) {
    throw new RuntimeException(sprintf('Unable to read %s', $filePath));
  }

  /** @var array<string,mixed> $data */
  $data = Yaml::decode($contents);
  $configFactory->getEditable($configName)->setData($data)->save();
  echo sprintf("[ok] imported %s from optional config\n", $configName);
}
