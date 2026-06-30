<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Drupal\ps_search\Service\SearchPresetQueryBuilder;

/**
 * Resolves content card CTA links (URL, modal, offcanvas, search preset).
 */
final class ContentCtaLinkBuilder {

  public function __construct(
    private readonly SearchPresetQueryBuilder $presetQueryBuilder,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ContactDisplayModeManager $contactDisplayMode,
  ) {}

  /**
   * @param array<string, mixed> $item
   *
   * @return array{
   *   link_type: string,
   *   url: string,
   *   modal_id: string,
   *   offcanvas: bool,
   *   dialog_attributes: array<string, string>
   *   }
   */
  public function resolve(array $item, ?string $langcode = NULL): array {
    $linkType = (string) ($item['link_type'] ?? 'url');

    if ($linkType === 'offcanvas') {
      return $this->resolveContactLink($this->resolveOffcanvasUrl($item, $langcode));
    }

    return match ($linkType) {
      'modal' => [
        'link_type' => 'modal',
        'url' => '',
        'modal_id' => ltrim((string) ($item['modal_id'] ?? ''), '#'),
        'offcanvas' => FALSE,
        'dialog_attributes' => [],
      ],
      'search_preset' => [
        'link_type' => 'search_preset',
        'url' => $this->presetQueryBuilder->buildUrl(
          isset($item['preset_operation']) ? (string) $item['preset_operation'] : NULL,
          isset($item['preset_asset']) ? (string) $item['preset_asset'] : NULL,
          isset($item['preset_locality']) ? (string) $item['preset_locality'] : NULL,
          $langcode,
        ),
        'modal_id' => '',
        'offcanvas' => FALSE,
        'dialog_attributes' => [],
      ],
      default => [
        'link_type' => 'url',
        'url' => $this->resolveLocalizedUrl($item, $langcode),
        'modal_id' => '',
        'offcanvas' => FALSE,
        'dialog_attributes' => [],
      ],
    };
  }

  /**
   * Maps global contact display mode to icon-card link props.
   *
   * @return array{
   *   link_type: string,
   *   url: string,
   *   modal_id: string,
   *   offcanvas: bool,
   *   dialog_attributes: array<string, string>
   *   }
   */
  private function resolveContactLink(string $url): array {
    $mode = $this->contactDisplayMode->getMode();

    if ($mode === ContactDisplayModeManager::MODE_PAGE) {
      return [
        'link_type' => 'url',
        'url' => $url,
        'modal_id' => '',
        'offcanvas' => FALSE,
        'dialog_attributes' => [],
      ];
    }

    $dialogAttributes = $this->contactDisplayMode->buildLinkAttributes();

    return [
      'link_type' => $mode === ContactDisplayModeManager::MODE_MODAL ? 'ajax_modal' : 'offcanvas',
      'url' => $url,
      'modal_id' => '',
      'offcanvas' => $mode === ContactDisplayModeManager::MODE_OFFCANVAS,
      'dialog_attributes' => $dialogAttributes,
    ];
  }

  /**
   * @param array<string, mixed> $item
   */
  private function resolveOffcanvasUrl(array $item, ?string $langcode): string {
    $path = trim((string) ($item['button_url'] ?? ''));
    if ($path !== '') {
      return $this->resolveLocalizedUrl($item, $langcode);
    }

    return Url::fromRoute('ps_form.webform_contact')->toString();
  }

  /**
   * @param array<string, mixed> $item
   */
  private function resolveLocalizedUrl(array $item, ?string $langcode): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $url = $item['button_url'] ?? $item['url'] ?? '';
    $url = trim((string) $url);
    if ($url === '') {
      return '';
    }

    if ($this->isExternalUrl($url)) {
      return Url::fromUri($url)->toString();
    }

    return Url::fromUserInput($url, [
      'language' => $this->languageManager->getLanguage($langcode),
    ])->toString();
  }

  /**
   * Whether the URL is an absolute external URI (not an internal path).
   */
  private function isExternalUrl(string $url): bool {
    return (bool) preg_match('/^(https?|mailto|tel):/i', $url);
  }

}
