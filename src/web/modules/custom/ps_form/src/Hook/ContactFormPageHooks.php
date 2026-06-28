<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\AdminContext;
use Drupal\ps_form\Service\ContactNeedRouter;

/**
 * Attaches contact deep-link settings on public front-end pages.
 */
final class ContactFormPageHooks {

  public function __construct(
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly AdminContext $adminContext,
  ) {}

  /**
   * Attaches contact deep-link library outside admin routes.
   *
   * @param array<string, mixed> $attachments
   *   Page attachments render array.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if ($this->adminContext->isAdminRoute()) {
      return;
    }

    $needPaths = [];
    $needTitles = [];
    foreach ($this->contactNeedRouter->getDirectDefinitions() as $need => $definition) {
      $needPaths[$need] = $definition['path'];
      $needTitles[$need] = (string) $this->contactNeedRouter->getPageTitle($definition['webform']);
    }

    $attachments['#attached']['library'][] = 'ps_form/contact_deeplink';
    $attachments['#attached']['library'][] = 'ps_theme/form';
    $attachments['#attached']['drupalSettings']['psForm'] = [
      'webformPaths' => $this->contactNeedRouter->getWebformPathMap(),
      'needPaths' => $needPaths,
      'needTitles' => $needTitles,
      'hubPath' => (string) $this->contactNeedRouter->getPathForWebform(ContactNeedRouter::HUB_WEBFORM_ID),
      'hubTitle' => (string) $this->contactNeedRouter->getPageTitle(ContactNeedRouter::HUB_WEBFORM_ID),
      'rentNeed' => ContactNeedRouter::RENT_NEED,
      'offcanvasClass' => 'ps-contact-offcanvas',
    ];
  }

}
