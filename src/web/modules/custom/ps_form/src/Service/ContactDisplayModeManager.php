<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Resolves the site-wide contact form display mode (page, modal, offcanvas).
 */
final class ContactDisplayModeManager {

  use StringTranslationTrait;

  public const MODE_PAGE = 'page';

  public const MODE_MODAL = 'modal';

  public const MODE_OFFCANVAS = 'offcanvas';

  private const DEFAULT_MODE = self::MODE_OFFCANVAS;

  private const DEFAULT_OFFCANVAS_CLASS = 'ps-contact-offcanvas';

  private const DEFAULT_MODAL_OPTIONS = [
    'width' => 800,
    'dialogClass' => 'ps-contact-modal modal-dialog-centered modal-lg',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ContactNeedRouter $contactNeedRouter,
  ) {}

  /**
   * Returns the configured display mode.
   */
  public function getMode(): string {
    $mode = (string) ($this->settings()->get('contact_display_mode') ?? self::DEFAULT_MODE);
    return in_array($mode, $this->getModeOptions(), TRUE) ? $mode : self::DEFAULT_MODE;
  }

  /**
   * Whether contact forms open via Drupal AJAX dialog.
   */
  public function isAjaxMode(): bool {
    return $this->getMode() !== self::MODE_PAGE;
  }

  /**
   * Whether contact forms render as full pages.
   */
  public function isPageMode(): bool {
    return $this->getMode() === self::MODE_PAGE;
  }

  /**
   * Theme hook suffix for the active display mode.
   */
  public function getThemeHook(): string {
    return match ($this->getMode()) {
      self::MODE_MODAL => 'ps_form_modal',
      self::MODE_PAGE => 'ps_form_page',
      default => 'ps_form_offcanvas',
    };
  }

  /**
   * Settings exposed to front-end JavaScript.
   *
   * @return array<string, mixed>
   */
  public function getJsSettings(): array {
    return [
      'mode' => $this->getMode(),
      'offcanvasClass' => self::DEFAULT_OFFCANVAS_CLASS,
      'modalOptions' => $this->getModalDialogOptions(),
    ];
  }

  /**
   * Builds HTML attributes for a contact-family form link.
   *
   * @return array<string, string>
   */
  public function buildLinkAttributes(?string $title = NULL): array {
    if ($this->isPageMode()) {
      return [];
    }

    $attributes = [
      'class' => 'use-ajax',
    ];

    if ($this->getMode() === self::MODE_MODAL) {
      $attributes['data-dialog-type'] = 'modal';
      $attributes['data-dialog-options'] = json_encode(
        $this->getModalDialogOptions(),
        JSON_THROW_ON_ERROR,
      );
      return $attributes;
    }

    $dialog = [
      'dialogClass' => self::DEFAULT_OFFCANVAS_CLASS,
    ];
    if ($title !== NULL && $title !== '') {
      $dialog['title'] = $title;
    }

    $attributes['data-dialog-type'] = 'dialog';
    $attributes['data-dialog-renderer'] = 'off_canvas';
    $attributes['data-dialog-options'] = json_encode($dialog, JSON_THROW_ON_ERROR);

    return $attributes;
  }

  /**
   * Applies display-mode attributes to a menu link item render array.
   *
   * @param array<string, mixed> $item
   *   Menu item render array (by reference).
   */
  public function applyToMenuLink(array &$item): void {
    $path = $this->extractMenuLinkPath($item);
    if (!$this->isContactFormPath($path)) {
      return;
    }

    if (!isset($item['attributes']) || !is_object($item['attributes'])) {
      return;
    }

    $attributes = $item['attributes'];

    if ($this->isPageMode()) {
      $attributes->removeClass('use-ajax');
      $attributes->removeAttribute('data-dialog-type');
      $attributes->removeAttribute('data-dialog-renderer');
      $attributes->removeAttribute('data-dialog-options');
      return;
    }

    if (!$attributes->hasClass('use-ajax')) {
      $attributes->addClass('use-ajax');
    }

    foreach ($this->buildLinkAttributes() as $attribute => $value) {
      if ($attribute === 'class') {
        continue;
      }
      $attributes->setAttribute($attribute, $value);
    }
  }

  /**
   * @return list<string>
   */
  public function getModeOptions(): array {
    return [
      self::MODE_PAGE,
      self::MODE_MODAL,
      self::MODE_OFFCANVAS,
    ];
  }

  /**
   * @return array<string, string>
   */
  public function getModeLabels(): array {
    return [
      self::MODE_PAGE => (string) $this->t('Full page'),
      self::MODE_MODAL => (string) $this->t('Modal'),
      self::MODE_OFFCANVAS => (string) $this->t('Offcanvas'),
    ];
  }

  /**
   * @return array<string, mixed>
   */
  public function getModalDialogOptions(): array {
    $raw = (string) ($this->settings()->get('contact_modal_dialog_options') ?? '');
    if ($raw === '') {
      return self::DEFAULT_MODAL_OPTIONS;
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException) {
      return self::DEFAULT_MODAL_OPTIONS;
    }

    return is_array($decoded) ? $decoded + self::DEFAULT_MODAL_OPTIONS : self::DEFAULT_MODAL_OPTIONS;
  }

  /**
   * Default JSON string for the admin form.
   */
  public function getDefaultModalDialogOptionsJson(): string {
    return json_encode(self::DEFAULT_MODAL_OPTIONS, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
  }

  /**
   * Checks whether an internal path targets a contact-family webform route.
   */
  public function isContactFormPath(string $path): bool {
    $normalized = '/' . trim($path, '/');
    $contactPaths = array_values($this->contactNeedRouter->getWebformPathMap());

    if (in_array($normalized, $contactPaths, TRUE)) {
      return TRUE;
    }

    foreach ($contactPaths as $contactPath) {
      if (str_ends_with($normalized, $contactPath)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   *
   */
  private function settings(): ImmutableConfig {
    return $this->configFactory->get('ps_form.settings');
  }

  /**
   * @param array<string, mixed> $item
   */
  private function extractMenuLinkPath(array $item): string {
    if (!isset($item['url']) || !method_exists($item['url'], 'getInternalPath')) {
      return '';
    }

    return (string) $item['url']->getInternalPath();
  }

}
