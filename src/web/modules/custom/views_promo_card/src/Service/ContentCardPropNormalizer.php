<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Normalizes content-card SDC props before render (buttons, booleans).
 */
final class ContentCardPropNormalizer {

  public function __construct(
    private readonly ContentCardButtonResolver $buttonResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Whether the given pattern ID is the unified content card.
   */
  public function supports(string $pattern_id): bool {
    return str_ends_with($pattern_id, 'content-card');
  }

  /**
   * Normalizes flat SDC props after UI Patterns resolution.
   *
   * @param array<string, mixed> $props
   *   Component props from ComponentElementBuilder.
   *
   * @return array<string, mixed>
   *   Props ready for content-card.twig.
   */
  public function normalizeProps(array $props): array {
    $props['icon_inline'] = $this->normalizeBoolean($props['icon_inline'] ?? FALSE);

    $buttons = $props['buttons'] ?? [];
    if (is_string($buttons)) {
      $decoded = json_decode($buttons, TRUE);
      $buttons = is_array($decoded) ? $decoded : [];
    }
    if (!is_array($buttons)) {
      $buttons = [];
    }

    $layout = (string) ($buttons['layout'] ?? 'stack');
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $items = [];
    foreach ($buttons['items'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $resolved = $this->buttonResolver->resolveItem($item, $langcode);
      if ($resolved['label'] !== '') {
        $items[] = $resolved;
      }
    }

    $props['buttons'] = [
      'layout' => $layout !== '' ? $layout : 'stack',
      'items' => $items,
    ];

    return $props;
  }

  /**
   * Coerces checkbox / select storage values to boolean.
   */
  private function normalizeBoolean(mixed $value): bool {
    if (is_bool($value)) {
      return $value;
    }
    if (is_int($value) || is_float($value)) {
      return (int) $value === 1;
    }
    $string = strtolower(trim((string) $value));
    return in_array($string, ['1', 'true', 'yes', 'on'], TRUE);
  }

}
