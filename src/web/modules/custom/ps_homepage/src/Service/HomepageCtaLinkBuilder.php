<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\ps_search\Service\SearchPresetQueryBuilder;

/**
 * Resolves homepage card CTA links (URL, modal, offcanvas, search preset).
 */
final class HomepageCtaLinkBuilder {

  public function __construct(
    private readonly SearchPresetQueryBuilder $presetQueryBuilder,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param array<string, mixed> $item
   *   Item configuration with link_type and related fields.
   *
   * @return array{
   *   link_type: string,
   *   url: string,
   *   modal_id: string,
   *   offcanvas: bool
   * }
   */
  public function resolve(array $item, ?string $langcode = NULL): array {
    $linkType = (string) ($item['link_type'] ?? 'url');

    return match ($linkType) {
      'offcanvas' => [
        'link_type' => 'offcanvas',
        'url' => Url::fromRoute('ps_homepage.contact_offcanvas')->toString(),
        'modal_id' => '',
        'offcanvas' => TRUE,
      ],
      'modal' => [
        'link_type' => 'modal',
        'url' => '',
        'modal_id' => ltrim((string) ($item['modal_id'] ?? ''), '#'),
        'offcanvas' => FALSE,
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
      ],
      default => [
        'link_type' => 'url',
        'url' => $this->resolveLocalizedUrl($item, $langcode),
        'modal_id' => '',
        'offcanvas' => FALSE,
      ],
    };
  }

  /**
   * @param array<string, mixed> $item
   */
  private function resolveLocalizedUrl(array $item, ?string $langcode): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $url = $item['button_url_' . $langcode] ?? $item['url_' . $langcode] ?? $item['button_url_en'] ?? $item['url_en'] ?? '';
    $url = trim((string) $url);
    if ($url === '') {
      return '';
    }

    return Url::fromUserInput($url)->toString();
  }

}
