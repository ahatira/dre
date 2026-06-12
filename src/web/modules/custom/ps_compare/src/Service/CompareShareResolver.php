<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Builds share URLs and resolves offers from business references.
 */
final class CompareShareResolver {

  public function __construct(
    private readonly ComparePathResolver $pathResolver,
    private readonly CompareManagerInterface $compareManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Normalizes raw query values into unique, non-empty reference strings.
   *
   * @param array<int|string, mixed> $raw
   *
   * @return list<string>
   */
  public function normalizeReferences(array $raw): array {
    $references = [];
    foreach ($raw as $value) {
      if (is_array($value)) {
        foreach ($this->normalizeReferences($value) as $nested) {
          $references[$nested] = $nested;
        }
        continue;
      }

      $reference = trim((string) $value);
      if ($reference !== '') {
        $references[$reference] = $reference;
      }
    }

    return array_values($references);
  }

  /**
   * @param \Drupal\node\NodeInterface[] $offers
   *
   * @return list<string>
   */
  public function extractReferencesFromOffers(array $offers): array {
    $references = [];
    foreach ($offers as $offer) {
      if (!$offer->hasField('field_reference') || $offer->get('field_reference')->isEmpty()) {
        continue;
      }
      $reference = trim((string) $offer->get('field_reference')->value);
      if ($reference !== '') {
        $references[] = $reference;
      }
    }

    return $references;
  }

  /**
   * @param \Drupal\node\NodeInterface[] $offers
   */
  public function buildUrlFromOffers(array $offers, bool $absolute = TRUE): string {
    return $this->buildUrlFromReferences($this->extractReferencesFromOffers($offers), $absolute);
  }

  /**
   * Builds an absolute or relative compare URL with comma-separated references.
   *
   * @param list<string> $references
   */
  public function buildUrlFromReferences(array $references, bool $absolute = TRUE): string {
    $references = $this->normalizeReferences($references);
    if ($references === []) {
      return Url::fromRoute('ps_compare.page', [], ['absolute' => $absolute])->toString();
    }

    $options = [
      'absolute' => $absolute,
      'query' => ['refs' => implode(',', $references)],
    ];

    return Url::fromRoute('ps_compare.page', [], $options)->toString();
  }

  /**
   * Reads offer references from compare share query parameters.
   *
   * Supports ?refs=REF1,REF2 and legacy ?ref[0]=REF1&ref[1]=REF2 formats.
   *
   * @return list<string>
   */
  public function extractReferencesFromRequest(\Symfony\Component\HttpFoundation\Request $request): array {
    $refsParam = $request->query->get('refs');
    if (is_string($refsParam) && trim($refsParam) !== '') {
      return $this->normalizeReferences(explode(',', $refsParam));
    }

    $refParam = $request->query->get('ref');
    if (is_array($refParam)) {
      return $this->normalizeReferences($refParam);
    }
    if (is_string($refParam) && trim($refParam) !== '') {
      return $this->normalizeReferences([$refParam]);
    }

    return [];
  }

  /**
   * Loads published offers matching the given references, preserving input order.
   *
   * @param list<string> $references
   *
   * @return \Drupal\node\NodeInterface[]
   */
  public function loadOffersByReferences(array $references): array {
    $references = $this->normalizeReferences($references);
    if ($references === []) {
      return [];
    }

    $maxItems = $this->compareManager->getMaxItems();
    $references = array_slice($references, 0, $maxItems);

    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'offer')
      ->condition('status', 1)
      ->condition('field_reference.value', $references, 'IN')
      ->execute();

    if ($nids === []) {
      return [];
    }

    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = $storage->loadMultiple($nids);
    $byReference = [];
    foreach ($nodes as $node) {
      if (!$node->hasField('field_reference') || $node->get('field_reference')->isEmpty()) {
        continue;
      }
      $reference = trim((string) $node->get('field_reference')->value);
      if ($reference !== '' && !isset($byReference[$reference])) {
        $byReference[$reference] = $node;
      }
    }

    $ordered = [];
    foreach ($references as $reference) {
      if (isset($byReference[$reference])) {
        $ordered[] = $byReference[$reference];
      }
    }

    return $ordered;
  }

}
