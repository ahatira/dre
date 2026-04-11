<?php

declare(strict_types=1);

namespace Drupal\ps\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for PropertySearch admin UI.
 */
class PsAdminHooks {

  /**
   * Implements hook_page_attachments().
   *
   * Attaches the ps/admin library to admin pages and when toolbar is present.
   * Works for both regular page loads and AJAX requests.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    // Attach admin library on admin routes or when user has toolbar access.
    $admin_context = \Drupal::service('router.admin_context');
    $current_user = \Drupal::currentUser();

    if ($admin_context->isAdminRoute() || $current_user->hasPermission('access toolbar')) {
      $attachments['#attached']['library'][] = 'ps/admin';
    }
  }

  /**
   * Implements hook_form_alter().
   *
   * Attaches the ps/admin library to field UI forms for AJAX/modal contexts.
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, $form_state, string $form_id): void {
    // Attach library to field UI forms (Add field, Edit field storage, etc.).
    if (str_starts_with($form_id, 'field_ui_') || str_starts_with($form_id, 'field_storage_add_form')) {
      $form['#attached']['library'][] = 'ps/admin';
    }
  }

}
