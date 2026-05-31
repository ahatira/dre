<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferReferenceManagerInterface;
use Drupal\ps_offer\Service\OfferValidationManagerInterface;

final class OfferHooks {

  public function __construct(
    private readonly OfferValidationManagerInterface $offerValidationManager,
    private readonly OfferReferenceManagerInterface $offerReferenceManager,
  ) {}

  #[Hook('node_presave')]
  public function nodePresave(NodeInterface $node): void {
    $this->syncInheritedMediaFieldsForTranslation($node);
    $this->syncDiagnosticsFields($node);
    $this->offerReferenceManager->applyReferenceMode($node);
    $this->offerValidationManager->apply($node);
  }

  /**
   * Keeps dedicated DPE/GES fields synchronized with imported diagnostics.
   */
  private function syncDiagnosticsFields(NodeInterface $node): void {
    if ($node->bundle() !== 'offer') {
      return;
    }
    if (!$node->hasField('field_diagnostics')) {
      return;
    }
    if (!$node->hasField('field_diagnostics_dpe') || !$node->hasField('field_diagnostics_ges')) {
      return;
    }

    $dpe = [];
    $ges = [];
    foreach ($node->get('field_diagnostics')->getValue() as $item) {
      $type = strtolower((string) ($item['diagnostic_type'] ?? ''));
      if ($type === 'dpe' && $dpe === []) {
        $item['diagnostic_type'] = 'dpe';
        $dpe = [$item];
      }
      elseif ($type === 'ges' && $ges === []) {
        $item['diagnostic_type'] = 'ges';
        $ges = [$item];
      }
    }

    $node->set('field_diagnostics_dpe', $dpe);
    $node->set('field_diagnostics_ges', $ges);
  }

  /**
   * Ensures non-translatable media fields are populated on translation saves.
   */
  private function syncInheritedMediaFieldsForTranslation(NodeInterface $node): void {
    if ($node->bundle() !== 'offer' || $node->isNew()) {
      return;
    }

    $default_langcode = $node->getUntranslated()->language()->getId();
    $current_langcode = $node->language()->getId();
    if ($current_langcode === $default_langcode) {
      return;
    }

    $source = $node->getUntranslated();
    foreach (['field_media_gallery', 'field_media_brochure'] as $field_name) {
      if (!$node->hasField($field_name) || !$source->hasField($field_name)) {
        continue;
      }
      if (!$node->get($field_name)->isEmpty()) {
        continue;
      }

      $source_values = $source->get($field_name)->getValue();
      if (!empty($source_values)) {
        $node->set($field_name, $source_values);
      }
    }
  }

  #[Hook('entity_insert')]
  public function entityInsert($entity): void {
    if ($entity instanceof NodeInterface && $entity->bundle() === 'offer') {
      // Skip projection rebuild during migration to avoid overwriting imported data
      if (\Drupal::state()->get('ps_offer.skip_projection', FALSE)) {
        return;
      }
      $this->rebuildOfferSurfaces($entity);
    }
  }

  #[Hook('entity_update')]
  public function entityUpdate($entity): void {
    if ($entity instanceof NodeInterface && $entity->bundle() === 'offer') {
      // Skip projection rebuild during migration to avoid overwriting imported data
      if (\Drupal::state()->get('ps_offer.skip_projection', FALSE)) {
        return;
      }
      // Only rebuild if field_divisions has changed
      if ($entity->hasField('field_divisions') && isset($entity->original)) {
        $old_divisions = array_column($entity->original->get('field_divisions')->getValue(), 'target_id');
        $new_divisions = array_column($entity->get('field_divisions')->getValue(), 'target_id');
        
        if ($old_divisions !== $new_divisions) {
          $this->rebuildOfferSurfaces($entity);
        }
      }
    }
  }

  /**
   * Triggers surface projection for an offer.
   */
  private function rebuildOfferSurfaces(NodeInterface $offer): void {
    if (!$offer->hasField('field_divisions')) {
      return;
    }

    $surface_projection = \Drupal::service('Drupal\\ps_surface\\Service\\SurfaceProjectionManager');
    $surface_projection->rebuildForOffer((int) $offer->id());
  }

  #[Hook('form_node_offer_form_alter')]
  public function formNodeOfferFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form['#validate'][] = [static::class, 'validateGallery'];
    $form['#attached']['library'][] = 'ps_diagnostic/diagnostic_admin';
    $this->ensureReferenceModeElement($form);
    $this->relaxRequiredFieldsOnTranslationForm($form, $form_state);
  }

  #[Hook('form_node_offer_edit_form_alter')]
  public function formNodeOfferEditFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form['#validate'][] = [static::class, 'validateGallery'];
    $form['#attached']['library'][] = 'ps_diagnostic/diagnostic_admin';
    $this->ensureReferenceModeElement($form);
    $this->relaxRequiredFieldsOnTranslationForm($form, $form_state);
  }

  /**
   * Avoid blocking translation save on inherited non-translatable required fields.
   */
  private function relaxRequiredFieldsOnTranslationForm(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!method_exists($form_object, 'getEntity')) {
      return;
    }

    $entity = $form_object->getEntity();
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    $default_langcode = $entity->getUntranslated()->language()->getId();
    $current_langcode = $entity->language()->getId();
    if ($current_langcode === $default_langcode) {
      return;
    }

    foreach (['field_media_gallery', 'field_media_brochure'] as $field_name) {
      if (!isset($form[$field_name])) {
        continue;
      }
      $this->unsetRequiredRecursively($form[$field_name]);
    }
  }

  /**
   * Removes required constraints recursively from a render array subtree.
   */
  private function unsetRequiredRecursively(array &$element): void {
    if (isset($element['#required'])) {
      $element['#required'] = FALSE;
    }

    foreach ($element as $key => &$child) {
      if (is_string($key) && str_starts_with($key, '#')) {
        continue;
      }
      if (is_array($child)) {
        $this->unsetRequiredRecursively($child);
      }
    }
  }

  private function ensureReferenceModeElement(array &$form): void {
    if (isset($form['field_reference_auto'])) {
      unset($form['field_reference_auto']);
    }

    $form['field_reference_auto'] = [
      '#type' => 'container',
      '#access' => FALSE,
    ];
    $form['field_reference_auto'][0]['value'] = [
      '#type' => 'hidden',
      '#value' => 1,
    ];
  }

  /**
   * Validation handler: ensures at least 1 media item in Gallery before publishing.
   */
  public static function validateGallery(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (method_exists($form_object, 'getEntity')) {
      $entity = $form_object->getEntity();
      if ($entity instanceof NodeInterface && $entity->bundle() === 'offer') {
        $default_langcode = $entity->getUntranslated()->language()->getId();
        $current_langcode = $entity->language()->getId();
        if ($current_langcode !== $default_langcode) {
          // On translation forms, non-translatable fields are inherited.
          // Do not block translation save because gallery input is empty.
          return;
        }
      }
    }

    $values = $form_state->getValues();

    $status_value = $values['status']['value'] ?? $values['status'] ?? 0;
    if ((int) $status_value !== 1) {
      return;
    }

    // Entity Browser stores selected media in a hidden target_id string.
    $target_ids = trim((string) ($values['field_media_gallery']['target_id'] ?? ''));
    if ($target_ids !== '') {
      return;
    }

    // Fallback for standard entity reference widgets that submit delta arrays.
    $gallery_items = $values['field_media_gallery'] ?? [];
    if (is_array($gallery_items)) {
      $gallery_items = array_filter($gallery_items, static function ($item) {
        return is_array($item) && !empty($item['target_id']);
      });
    }

    if (empty($gallery_items)) {
      $form_state->setErrorByName(
        'field_media_gallery',
        t('Gallery must contain at least one media item before publishing the offer.')
      );
    }
  }

}
