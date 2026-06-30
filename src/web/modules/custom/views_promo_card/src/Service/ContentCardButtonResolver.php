<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\ps_content\Service\ContentCtaLinkBuilder;

/**
 * Resolves content-card button props via ContentCtaLinkBuilder.
 */
final class ContentCardButtonResolver {

  public function __construct(
    private readonly ContentCtaLinkBuilder $ctaLinkBuilder,
  ) {}

  /**
   * Resolves a stored button item for SDC rendering.
   *
   * @param array<string, mixed> $item
   *   Stored button configuration (admin JSON).
   * @param string|null $langcode
   *   Optional URL language code.
   *
   * @return array<string, mixed>
   *   Resolved button props for content-card.twig.
   */
  public function resolveItem(array $item, ?string $langcode = NULL): array {
    $label = trim((string) ($item['label'] ?? ''));
    $mode = (string) ($item['mode'] ?? 'page');
    $url = trim((string) ($item['url'] ?? ''));
    $modal_id = trim((string) ($item['modal_id'] ?? ''));

    $builder_input = match ($mode) {
      'modal' => [
        'link_type' => 'modal',
        'modal_id' => $modal_id,
      ],
      'offcanvas' => [
        'link_type' => 'offcanvas',
        'button_url' => $url,
      ],
      default => [
        'link_type' => 'url',
        'button_url' => $url,
      ],
    };

    $resolved = $this->ctaLinkBuilder->resolve($builder_input, $langcode);
    $variant = (string) ($item['variant'] ?? 'primary');
    $target = (string) ($item['target'] ?? '_self');
    $resolved_url = $resolved['url'];
    if ($resolved_url === '' && $mode === 'page' && $url !== '') {
      $resolved_url = $url;
    }

    return [
      'label' => $label,
      'url' => $resolved_url,
      'variant' => $variant === 'secondary' ? 'secondary' : 'primary',
      'outline' => !empty($item['outline']),
      'link_type' => $resolved['link_type'],
      'modal_id' => $resolved['modal_id'],
      'dialog_attributes' => $resolved['dialog_attributes'],
      'target' => $target === '_blank' ? '_blank' : '_self',
    ];
  }

}
