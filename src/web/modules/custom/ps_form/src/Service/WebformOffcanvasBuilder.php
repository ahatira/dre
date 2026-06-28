<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\webform\WebformInterface;

/**
 * Builds offcanvas render arrays for contact-family webforms.
 */
final class WebformOffcanvasBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContactNeedRouter $contactNeedRouter,
  ) {}

  /**
   * Builds an offcanvas panel for a contact-family webform.
   *
   * @return array<string, mixed>
   *   Render array for AJAX offcanvas loading.
   */
  public function build(string $webformId): array {
    if (!$this->isAllowedWebform($webformId)) {
      return $this->unavailable($this->t('Unknown form.'));
    }

    $webform = $this->loadWebform($webformId);
    if ($webform === NULL) {
      return $this->unavailable($this->t('The requested form is not available yet.'));
    }

    return [
      '#theme' => 'ps_form_offcanvas',
      '#webform' => $this->entityTypeManager->getViewBuilder('webform')->view($webform, 'default'),
      '#webform_id' => $webformId,
      '#panel_id' => $this->resolvePanelId($webformId),
      '#attached' => [
        'library' => [
          'ps_theme/form',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
        'tags' => $webform->getCacheTags(),
        'contexts' => ['url.query_args', 'languages:language_interface', 'user'],
      ],
    ];
  }

  /**
   * Checks whether a webform id belongs to the contact family.
   */
  private function isAllowedWebform(string $webformId): bool {
    $allowed = array_merge(
      [ContactNeedRouter::HUB_WEBFORM_ID],
      $this->contactNeedRouter->getDirectWebformIds(),
    );

    return in_array($webformId, $allowed, TRUE);
  }

  private function loadWebform(string $webformId): ?WebformInterface {
    $entity = $this->entityTypeManager->getStorage('webform')->load($webformId);
    return $entity instanceof WebformInterface ? $entity : NULL;
  }

  /**
   * Resolves the DOM id used by contact wizard CSS hooks.
   */
  private function resolvePanelId(string $webformId): string {
    if ($webformId === ContactNeedRouter::HUB_WEBFORM_ID) {
      return 'contact-panel';
    }

    return str_replace('_', '-', $webformId) . '-panel';
  }

  /**
   * Builds a placeholder when the webform cannot be loaded.
   *
   * @return array<string, mixed>
   *   Placeholder render array.
   */
  private function unavailable(string|TranslatableMarkup $message): array {
    return [
      '#theme' => 'ps_homepage_contact_offcanvas_placeholder',
      '#message' => $message,
    ];
  }

}
