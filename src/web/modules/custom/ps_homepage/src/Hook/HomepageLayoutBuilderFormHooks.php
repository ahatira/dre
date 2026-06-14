<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Layout Builder block form alters for homepage media picker UX.
 */
final class HomepageLayoutBuilderFormHooks {

  /**
   * Attaches libraries required for Media Library modals in the LB tray.
   */
  #[Hook('form_layout_builder_update_block_alter')]
  #[Hook('form_layout_builder_add_block_alter')]
  public function alterConfigureBlockForm(array &$form, FormStateInterface $form_state): void {
    $this->attachHomepageBlockFormAssets($form);
  }

  /**
   * Ensures Media Library assets load on LB block configure routes.
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    $route = \Drupal::routeMatch()->getRouteName();
    if (!in_array($route, ['layout_builder.update_block', 'layout_builder.add_block'], TRUE)) {
      return;
    }

    $attachments['#attached']['library'][] = 'ps_homepage/homepage_block_form';
    $attachments['#attached']['library'][] = 'media_library/widget';
    $attachments['#attached']['library'][] = 'media_library/ui';
    $attachments['#attached']['library'][] = 'core/drupal.dialog.ajax';
  }

  /**
   * @param array<string, mixed> $form
   */
  private function attachHomepageBlockFormAssets(array &$form): void {
    if (!isset($form['settings']) || !is_array($form['settings'])) {
      return;
    }

    // All homepage LB block forms expose this language notice.
    if (!isset($form['settings']['editing_language'])) {
      return;
    }

    $form['#attached']['library'][] = 'ps_homepage/homepage_block_form';
    $form['#attached']['library'][] = 'media_library/widget';
    $form['#attached']['library'][] = 'media_library/ui';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
  }

}
