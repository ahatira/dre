<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\address\Plugin\Field\FieldFormatter\AddressPlainFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Formats surface total and locality on one line.
 *
 * @FieldFormatter(
 *   id = "ps_offer_location_summary",
 *   label = @Translation("Offer location summary"),
 *   field_types = {
 *     "address"
 *   }
 * )
 */
final class OfferLocationSummaryFormatter extends AddressPlainFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    $parts = [];

    $surface = $this->formatSurfaceTotal($entity);
    if ($surface !== '') {
      $parts[] = $surface;
    }

    foreach ($items as $item) {
      $postal = trim((string) ($item->postal_code ?? ''));
      $locality = trim((string) ($item->locality ?? ''));
      $location = trim($postal . ' ' . $locality);
      if ($location !== '') {
        $parts[] = $location;
      }
    }

    if ($parts === []) {
      return [];
    }

    return [
      0 => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => implode(' • ', $parts),
        '#attributes' => ['class' => ['ps-offer-location-summary']],
      ],
    ];
  }

  /**
   * Formats the TOTAL surface value for the summary line.
   */
  private function formatSurfaceTotal(object $entity): string {
    if (!$entity->hasField('field_surfaces') || $entity->get('field_surfaces')->isEmpty()) {
      return '';
    }

    foreach ($entity->get('field_surfaces') as $item) {
      if ((string) ($item->qualification ?? '') !== 'TOTAL') {
        continue;
      }
      $value = $item->value ?? NULL;
      if ($value === NULL || (float) $value <= 0) {
        return '';
      }
      $unit = strtolower((string) ($item->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
      return number_format((float) $value, 1, ',', ' ') . ' ' . $unit;
    }

    return '';
  }

}
