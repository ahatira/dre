<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\webform\WebformInterface;

/**
 * Builds contact-family webform shells for page, modal, and offcanvas modes.
 */
final class ContactWebformShellBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly ContactDisplayModeManager $displayModeManager,
  ) {}

  /**
   * Builds a contact-family webform shell for the active display mode.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function build(string $webformId, ?string $displayMode = NULL): array {
    if (!$this->isAllowedWebform($webformId)) {
      return $this->unavailable($this->t('Unknown form.'));
    }

    $webform = $this->loadWebform($webformId);
    if ($webform === NULL) {
      return $this->unavailable($this->t('The requested form is not available yet.'));
    }

    $mode = $displayMode ?? $this->displayModeManager->getMode();
    $theme = match ($mode) {
      ContactDisplayModeManager::MODE_MODAL => 'ps_form_modal',
      ContactDisplayModeManager::MODE_PAGE => 'ps_form_page',
      default => 'ps_form_offcanvas',
    };

    return [
      '#theme' => $theme,
      '#webform' => $this->entityTypeManager->getViewBuilder('webform')->view($webform, 'default'),
      '#webform_id' => $webformId,
      '#panel_id' => $this->resolvePanelId($webformId),
      '#title' => $this->contactNeedRouter->getPageTitle($webformId),
      '#attached' => [
        'library' => [
          'ps_theme/form',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
        'tags' => array_merge($webform->getCacheTags(), ['config:ps_form.settings']),
        'contexts' => ['url.query_args', 'languages:language_interface', 'user'],
      ],
    ];
  }

  /**
   * Checks whether a webform id belongs to the contact family.
   */
  private function isAllowedWebform(string $webformId): bool {
    return $this->contactNeedRouter->isContactFamilyWebform($webformId);
  }

  /**
   *
   */
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
