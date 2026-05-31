<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_offer\Entity\OfferReferenceAliasSetInterface;
use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

final class OfferReferenceAliasResolver implements OfferReferenceAliasResolverInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function resolve(OfferReferencePatternInterface $pattern, array $segment, string $sourceField, string $sourceValue): ?string {
    if ($sourceField === '' || $sourceValue === '') {
      return NULL;
    }

    foreach ($this->loadApplicableAliasSets($pattern, $segment) as $set) {
      foreach ($set->getEntries() as $entry) {
        if (($entry['source_field'] ?? '') === $sourceField && ($entry['source_value'] ?? '') === $sourceValue) {
          $reference_code = trim((string) ($entry['reference_code'] ?? ''));
          if ($reference_code !== '') {
            return $reference_code;
          }
        }
      }
    }

    return NULL;
  }

  /**
   * @return \Drupal\ps_offer\Entity\OfferReferenceAliasSetInterface[]
   */
  private function loadApplicableAliasSets(OfferReferencePatternInterface $pattern, array $segment): array {
    $requested_ids = array_values(array_filter(is_array($segment['alias_set_ids'] ?? NULL) ? $segment['alias_set_ids'] : [], static fn ($value): bool => is_string($value) && $value !== ''));
    $storage = $this->entityTypeManager->getStorage('ps_offer_reference_alias_set');
    $loaded = $requested_ids !== [] ? $storage->loadMultiple($requested_ids) : $storage->loadMultiple();

    $sets = array_filter($loaded, static function (OfferReferenceAliasSetInterface $set) use ($pattern): bool {
      if (!$set->status()) {
        return FALSE;
      }

      $pattern_ids = $set->getAppliesToPatternIds();
      return $pattern_ids === [] || in_array($pattern->id(), $pattern_ids, TRUE);
    });

    usort($sets, static fn (OfferReferenceAliasSetInterface $left, OfferReferenceAliasSetInterface $right): int => $left->getWeight() <=> $right->getWeight());
    return $sets;
  }

}