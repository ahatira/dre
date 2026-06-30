<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form\Helper;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;

/**
 * Extracts file ids from managed_file form values (including AJAX rebuilds).
 */
final class ManagedFileFormValueHelper {

  /**
   * Resolves a managed_file fid after AJAX upload rebuilds.
   *
   * @param list<string> $parents
   *   Form value parents.
   * @param int $configuredFid
   *   Previously saved file id.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The active form state.
   */
  public static function resolveNestedManagedFileFid(array $parents, int $configuredFid, FormStateInterface $form_state): int {
    $input = $form_state->getUserInput();
    if (is_array($input)) {
      $inputValue = NestedArray::getValue($input, $parents);
      if (is_array($inputValue) && array_key_exists('fids', $inputValue)) {
        return self::extractManagedFileFid($inputValue);
      }
    }

    $value = NestedArray::getValue($form_state->getValues(), $parents);
    $extracted = self::extractManagedFileFid($value);
    if ($extracted > 0) {
      return $extracted;
    }

    if (is_array($value)) {
      return 0;
    }

    return $configuredFid;
  }

  /**
   * Extracts a file id from a managed_file form value.
   */
  public static function extractManagedFileFid(mixed $value): int {
    if (!is_array($value)) {
      return 0;
    }

    if (isset($value[0]) && (int) $value[0] > 0) {
      return (int) $value[0];
    }

    if (array_key_exists('fids', $value)) {
      $fids = trim((string) $value['fids']);
      if ($fids === '') {
        return 0;
      }

      return (int) strtok($fids, ' ');
    }

    return 0;
  }

}
