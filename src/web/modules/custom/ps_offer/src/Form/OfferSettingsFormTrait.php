<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Shared helpers for partial edits of ps_offer.settings.
 */
trait OfferSettingsFormTrait {

  protected function offerSettingsConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.settings');
  }

  /**
   * Persists a subset of ps_offer.settings keys from form values.
   *
   * @param list<string> $keys
   *   Config keys to save (same names as form value keys).
   */
  protected function saveOfferSettingsKeys(array $keys, FormStateInterface $form_state): void {
    $editable = $this->configFactory->getEditable('ps_offer.settings');
    foreach ($keys as $key) {
      $editable->set($key, trim((string) $form_state->getValue($key)));
    }
    $editable->save();
  }

  protected function saveOfferDefaultImage(FormStateInterface $form_state): void {
    $editable = $this->configFactory->getEditable('ps_offer.settings');
    $imageValues = $form_state->getValue('default_image_fid') ?: [];
    $fid = (int) ($imageValues[0] ?? 0);
    if ($fid > 0) {
      $file = File::load($fid);
      if ($file !== NULL) {
        $file->setPermanent();
        $file->save();
      }
    }
    $editable->set('default_image_fid', $fid);
    $editable->set('default_image_alt', trim((string) $form_state->getValue('default_image_alt')));
    $editable->save();
  }

  protected function resolveManagedFileFid(string $field_name, int $configured_fid, FormStateInterface $form_state): int {
    $value = $form_state->getValue($field_name);
    if (is_array($value)) {
      if (isset($value[0]) && (int) $value[0] > 0) {
        return (int) $value[0];
      }

      if (array_key_exists('fids', $value)) {
        $parsed = (int) $value['fids'];
        return $parsed > 0 ? $parsed : 0;
      }

      if ($form_state->hasValue($field_name)) {
        return 0;
      }
    }

    $input = $form_state->getUserInput();
    if (is_array($input) && isset($input[$field_name]) && is_array($input[$field_name])) {
      $fids = trim((string) ($input[$field_name]['fids'] ?? ''));
      if ($fids !== '') {
        $first = (int) strtok($fids, ' ');
        return $first > 0 ? $first : 0;
      }

      return 0;
    }

    return $configured_fid;
  }

}
