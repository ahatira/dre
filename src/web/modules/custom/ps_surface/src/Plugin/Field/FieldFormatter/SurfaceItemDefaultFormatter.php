<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'ps_surface_item_default' formatter.
 *
 * @FieldFormatter(
 *   id = "ps_surface_item_default",
 *   label = @Translation("PS surface item"),
 *   field_types = {
 *     "ps_surface_item"
 *   }
 * )
 */
final class SurfaceItemDefaultFormatter extends FormatterBase {

  private const CANONICAL_ORDER = ['TOTAL', 'DISPO', 'ETREF'];

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $rows = [];

    foreach ($items as $item) {
      $value = $item->value !== NULL ? (string) $item->value : '';
      $qualification = (string) ($item->qualification ?? '');
      $unit_code = (string) ($item->unit_code ?? '');
      $source_scope = (string) ($item->source_scope ?? '');
      $source_ref = (string) ($item->source_ref ?? '');

      $parts = [];
      if ($qualification !== '') {
        $parts[] = $qualification;
      }
      if ($value !== '') {
        $parts[] = $value;
      }
      if ($unit_code !== '') {
        $parts[] = $unit_code;
      }

      $text = implode(' ', $parts);

      if ($source_scope !== '' || $source_ref !== '') {
        $suffix = trim($source_scope . ($source_ref !== '' ? ':' . $source_ref : ''));
        if ($suffix !== '') {
          $text .= ' [' . $suffix . ']';
        }
      }

      $rows[] = [
        'qualification' => $qualification,
        'text' => $text,
      ];
    }

    usort($rows, static function (array $a, array $b): int {
      $indexA = array_search($a['qualification'], self::CANONICAL_ORDER, TRUE);
      $indexB = array_search($b['qualification'], self::CANONICAL_ORDER, TRUE);

      $rankA = $indexA === FALSE ? 999 : $indexA;
      $rankB = $indexB === FALSE ? 999 : $indexB;

      if ($rankA === $rankB) {
        return strcmp($a['qualification'], $b['qualification']);
      }

      return $rankA <=> $rankB;
    });

    $elements = [];
    foreach ($rows as $delta => $row) {
      $elements[$delta] = [
        '#plain_text' => $row['text'],
      ];
    }

    return $elements;
  }

}
