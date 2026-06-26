<?php

declare(strict_types=1);

namespace Drupal\ps_media\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\media\MediaInterface;
use Drupal\ps_media\Service\MediaImportGovernance;

/**
 * Media entity hooks for import protection and BO governance defaults.
 */
final class MediaEntityHooks {

  /**
   * CRM offer media bundles that participate in import governance.
   *
   * @var string[]
   */
  private const GOVERNED_BUNDLES = [
    'image',
    'visite_guided',
  ];

  public function __construct(
    private readonly MediaImportGovernance $importGovernance,
  ) {}

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!$this->isMediaEntityForm($form_id)) {
      return;
    }

    $entity = $this->resolveMediaFormEntity($form, $form_state);
    if (!$entity instanceof MediaInterface || !$this->isGovernedBundle($entity)) {
      return;
    }

    if (!isset($form['field_internal_lock'])) {
      return;
    }

    if ($entity->isNew() && str_contains($form_id, '_add_form')) {
      $default = $this->importGovernance->shouldLockOnBoCreate() ? 1 : 0;
      if (isset($form['field_internal_lock']['widget']['value'])) {
        $form['field_internal_lock']['widget']['value']['#default_value'] = $default;
      }
    }

    $form['field_internal_lock']['#description'] = t(
      'Enable this lock to protect manual edits from automated CRM imports. When locked, this media item keeps its curated values during CRM synchronization.',
    );
    $form['field_internal_lock']['#prefix'] = '<div class="field-protection-wrapper">';
    $form['field_internal_lock']['#suffix'] = '</div>';
  }

  /**
   * Whether the form ID targets a media entity add/edit form.
   */
  private function isMediaEntityForm(string $form_id): bool {
    return str_starts_with($form_id, 'media_')
      && (str_ends_with($form_id, '_add_form') || str_ends_with($form_id, '_edit_form'));
  }

  /**
   * Resolves the media entity being edited from a media form.
   */
  private function resolveMediaFormEntity(array $form, FormStateInterface $form_state): ?EntityInterface {
    $form_object = $form_state->getFormObject();
    if ($form_object !== NULL && method_exists($form_object, 'getEntity')) {
      $entity = $form_object->getEntity();
      if ($entity instanceof MediaInterface) {
        return $entity;
      }
    }

    $entity = $form['#entity'] ?? NULL;
    return $entity instanceof MediaInterface ? $entity : NULL;
  }

  /**
   * Whether the bundle is covered by media import governance.
   */
  private function isGovernedBundle(MediaInterface $media): bool {
    return in_array($media->bundle(), self::GOVERNED_BUNDLES, TRUE);
  }

}
