<?php

declare(strict_types=1);

/**
 * @file
 * Safe integration probe for snapshot post-import governance wiring.
 *
 * Validates snapshot extraction and offer policy decisions without mutating
 * existing site content. For full pipeline validation, run a controlled CRM
 * import against a dedicated fixture database.
 *
 * Usage (from repo src/):
 *   vendor/bin/drush @ps.fr scr web/modules/custom/ps_migrate/tests/snapshot_post_import_integration.evaluate.php
 */

use Drupal\Core\File\FileSystemInterface;
use Drupal\node\Entity\Node;

$fileSystem = \Drupal::service('file_system');
$directory = 'public://crm';
$fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
$uri = $directory . '/snapshot_integration_probe_' . time() . '.xml';
$path = $fileSystem->realpath($uri);
if ($path === FALSE) {
  throw new \RuntimeException('Unable to resolve CRM staging path.');
}

$keptBusinessId = 'SNAP-PROBE-KEEP';
$missingBusinessId = 'SNAP-PROBE-MISSING';

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OFFERS_LIST>
  <OFFER>
    <BUSINESS_ID>{$keptBusinessId}</BUSINESS_ID>
    <TYPE_CODE>OFF</TYPE_CODE>
    <MEDIA_LIST>
      <MEDIA>
        <TYPE_CODE>EXT</TYPE_CODE>
        <ORDER>1</ORDER>
        <URL>https://example.com/photo.jpg</URL>
      </MEDIA>
      <MEDIA>
        <TYPE_CODE>VIS</TYPE_CODE>
        <ORDER>2</ORDER>
        <URL>https://example.com/tour</URL>
      </MEDIA>
    </MEDIA_LIST>
  </OFFER>
</OFFERS_LIST>
XML;
file_put_contents($path, $xml);

/** @var \Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder $builder */
$builder = \Drupal::service('ps_migrate.crm_xml_snapshot_builder');
$offerSnapshot = $builder->buildOfferBusinessIds([$uri]);
$mediaExtSnapshot = $builder->buildMediaExtCompositeKeys([$uri]);
$mediaVisSnapshot = $builder->buildMediaVisCompositeKeys([$uri]);

/** @var \Drupal\ps_core\Service\ImportGovernanceRegistry $registry */
$registry = \Drupal::service('ps_core.import_governance_registry');
$offerPolicy = $registry->getSnapshotPostImportPolicyForMigration('ps_offer_from_xml');
$mediaPolicy = $registry->getSnapshotPostImportPolicyForMigration('ps_media_from_xml');
$agentPolicy = $registry->getSnapshotPostImportPolicyForMigration('ps_agent_from_xml');

if ($offerPolicy === NULL || $mediaPolicy === NULL) {
  throw new \RuntimeException('Expected offer and media snapshot policies are missing.');
}

$probeOffer = Node::create([
  'type' => 'offer',
  'title' => 'Snapshot probe (not saved)',
  'field_business_id' => $missingBusinessId,
  'status' => 1,
]);

$checks = [
  'offer_snapshot' => isset($offerSnapshot[$keptBusinessId]) && !isset($offerSnapshot[$missingBusinessId]),
  'media_ext_snapshot' => isset($mediaExtSnapshot[$keptBusinessId . ':1']),
  'media_vis_snapshot' => isset($mediaVisSnapshot[$keptBusinessId . ':2']),
  'offer_policy_unpublish_missing' => $offerPolicy->shouldDeactivateMissingEntity($probeOffer, FALSE),
  'offer_policy_keep_present' => !$offerPolicy->shouldDeactivateMissingEntity($probeOffer, TRUE),
  'media_policy_registered' => in_array('ps_media_virtual_tour_from_xml', $mediaPolicy->getSupportedMigrationIds(), TRUE),
  'agent_policy_optional' => $agentPolicy === NULL || $agentPolicy->getSupportedMigrationIds() !== [],
];

@unlink($path);

if (in_array(FALSE, $checks, TRUE)) {
  print 'FAIL: ' . json_encode($checks, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . PHP_EOL;
  exit(1);
}

print 'PASS: snapshot builder and governance policies are wired for offer/media imports.' . PHP_EOL;
print json_encode($checks, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . PHP_EOL;
