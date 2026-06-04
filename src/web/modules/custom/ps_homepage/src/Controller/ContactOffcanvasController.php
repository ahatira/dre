<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Contact form in a right off-canvas panel (Webform-ready).
 */
final class ContactOffcanvasController extends ControllerBase {

  /**
   * Builds the contact off-canvas content.
   */
  public function offcanvas(): array {
    if ($this->moduleHandler()->moduleExists('webform')) {
      $webform = $this->entityTypeManager()->getStorage('webform')->load('contact');
      if ($webform) {
        return $this->entityTypeManager()->getViewBuilder('webform')->view($webform, 'default');
      }
    }

    return [
      '#theme' => 'ps_homepage_contact_offcanvas_placeholder',
      '#message' => $this->t('Install Webform and create a "contact" webform to display the contact form here.'),
    ];
  }

  /**
   * Page title callback.
   */
  public function title(): TranslatableMarkup {
    return $this->t('Contact us');
  }

}
