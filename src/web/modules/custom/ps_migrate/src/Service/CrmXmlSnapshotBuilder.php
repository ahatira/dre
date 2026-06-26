<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Builds entity ID snapshots from CRM offer XML files.
 */
final class CrmXmlSnapshotBuilder {

  public function __construct(
    private readonly CrmOfferXmlDocumentLoader $documentLoader,
  ) {}

  /**
   * Returns active offer business IDs from the CRM XML snapshot.
   *
   * @param string[] $files
   *   Source XML URIs or paths.
   *
   * @return array<string, true>
   *   Business IDs keyed for fast lookup.
   */
  public function buildOfferBusinessIds(array $files): array {
    $snapshot = [];

    foreach ($files as $file) {
      foreach ($this->documentLoader->selectItems($file, CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::OFFER) ?? '') as $item) {
        if (!$item instanceof \SimpleXMLElement) {
          continue;
        }
        $businessId = trim((string) ($item->BUSINESS_ID ?? ''));
        if ($businessId !== '') {
          $snapshot[$businessId] = TRUE;
        }
      }
    }

    return $snapshot;
  }

  /**
   * Returns active agent UIDs from the CRM XML snapshot.
   *
   * @param string[] $files
   *   Source XML URIs or paths.
   *
   * @return array<string, true>
   *   Agent UIDs keyed for fast lookup.
   */
  public function buildAgentUids(array $files): array {
    $selector = CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::AGENT);
    if ($selector === NULL) {
      return [];
    }

    $snapshot = [];

    foreach ($files as $file) {
      foreach ($this->documentLoader->selectItems($file, $selector) as $item) {
        if (!$item instanceof \SimpleXMLElement) {
          continue;
        }
        $uid = trim((string) ($item->UID ?? ''));
        if ($uid !== '') {
          $snapshot[$uid] = TRUE;
        }
      }
    }

    return $snapshot;
  }

  /**
   * Returns active image media composite keys from the CRM XML snapshot.
   *
   * @param string[] $files
   *   Source XML URIs or paths.
   *
   * @return array<string, true>
   *   Keys formatted as "{business_id}:{order}".
   */
  public function buildMediaExtCompositeKeys(array $files): array {
    return $this->buildMediaCompositeKeys($files, CrmOfferXmlMode::MEDIA_EXT);
  }

  /**
   * Returns active virtual tour media composite keys from the CRM XML snapshot.
   *
   * @param string[] $files
   *   Source XML URIs or paths.
   *
   * @return array<string, true>
   *   Keys formatted as "{business_id}:{order}".
   */
  public function buildMediaVisCompositeKeys(array $files): array {
    return $this->buildMediaCompositeKeys($files, CrmOfferXmlMode::MEDIA_VIS);
  }

  /**
   * Builds media snapshot keys for a CRM XML media extraction mode.
   *
   * @param string[] $files
   *   Source XML URIs or paths.
   * @param string $mode
   *   One of CrmOfferXmlMode::MEDIA_EXT or CrmOfferXmlMode::MEDIA_VIS.
   *
   * @return array<string, true>
   *   Composite keys keyed for fast lookup.
   */
  private function buildMediaCompositeKeys(array $files, string $mode): array {
    $selector = CrmOfferXmlMode::itemSelector($mode);
    if ($selector === NULL) {
      return [];
    }

    $snapshot = [];

    foreach ($files as $file) {
      foreach ($this->documentLoader->selectItems($file, $selector) as $item) {
        if (!$item instanceof \SimpleXMLElement) {
          continue;
        }

        $businessId = trim((string) ($item->xpath('../../BUSINESS_ID')[0] ?? ''));
        $order = (int) ($item->ORDER ?? 0);
        if ($businessId === '' || $order <= 0) {
          continue;
        }

        $snapshot[$businessId . ':' . $order] = TRUE;
      }
    }

    return $snapshot;
  }

}
