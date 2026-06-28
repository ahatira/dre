<?php

declare(strict_types=1);

namespace Drupal\ps_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Legacy admin route redirects after settings move to ps_form.
 */
final class LegacySettingsRedirectController extends ControllerBase {

  /**
   * Redirects former global settings URL to ps_form contact settings.
   */
  public function contactSettings(): RedirectResponse {
    return $this->redirect('ps_form.contact_settings', [], [], 301);
  }

}
