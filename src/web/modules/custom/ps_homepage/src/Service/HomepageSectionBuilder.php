<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

/**
 * Builds standardized homepage section render arrays (SDC shell).
 */
final class HomepageSectionBuilder {

  private const COMPONENT_SECTION = 'ps_theme:homepage-section';

  private const COMPONENT_HEADER = 'ps_theme:homepage-section-header';

  private const COMPONENT_FOOTER = 'ps_theme:homepage-section-footer';

  /**
   * Builds a homepage section component with optional header, body and footer.
   *
   * @param array<string, mixed> $options
   *   Section options: background, container, spacing, modifier, header, body,
   *   footer, cache, attached.
   *
   * @return array<string, mixed>
   *   Render array for the homepage section SDC.
   */
  public function build(array $options): array {
    $body = $options['body'] ?? NULL;
    if (!is_array($body)) {
      return ['#markup' => ''];
    }

    $slots = [
      'body' => $body,
    ];

    $header = $options['header'] ?? NULL;
    if (is_array($header) && (($header['title'] ?? '') !== '' || ($header['subtitle'] ?? '') !== '')) {
      $slots['header'] = $this->buildHeader($header);
    }

    $footer = $options['footer'] ?? NULL;
    if (is_array($footer)) {
      $ctaLabel = (string) ($footer['cta_label'] ?? $footer['label'] ?? '');
      $ctaUrl = (string) ($footer['cta_url'] ?? $footer['url'] ?? '');
      if ($ctaLabel !== '' && $ctaUrl !== '') {
        $slots['footer'] = $this->buildFooter([
          'cta_label' => $ctaLabel,
          'cta_url' => $ctaUrl,
          'cta_style' => (string) ($footer['cta_style'] ?? 'outline'),
        ]);
      }
    }

    $props = [
      'background' => (string) ($options['background'] ?? 'default'),
      'container' => (string) ($options['container'] ?? 'container'),
      'spacing' => (string) ($options['spacing'] ?? 'lg'),
      'modifier' => (string) ($options['modifier'] ?? ''),
    ];
    $sectionClass = trim((string) ($options['section_class'] ?? ''));
    if ($sectionClass !== '') {
      $props['section_class'] = $sectionClass;
    }

    $build = [
      '#type' => 'component',
      '#component' => self::COMPONENT_SECTION,
      '#props' => $props,
      '#slots' => $slots,
    ];

    if (!empty($options['attached']) && is_array($options['attached'])) {
      $build['#attached'] = $options['attached'];
    }

    if (!empty($options['cache']) && is_array($options['cache'])) {
      $build['#cache'] = $options['cache'];
    }

    return $build;
  }

  /**
   * Builds the homepage section header component render array.
   *
   * @param array<string, mixed> $props
   *   Header props: title, subtitle, align, accent.
   *
   * @return array<string, mixed>
   *   Header component render array.
   */
  public function buildHeader(array $props): array {
    return [
      '#type' => 'component',
      '#component' => self::COMPONENT_HEADER,
      '#props' => [
        'title' => (string) ($props['title'] ?? ''),
        'subtitle' => (string) ($props['subtitle'] ?? ''),
        'align' => (string) ($props['align'] ?? 'center'),
        'accent' => (string) ($props['accent'] ?? 'bar'),
      ],
    ];
  }

  /**
   * Builds the homepage section footer component render array.
   *
   * @param array<string, mixed> $props
   *   Footer props: cta_label, cta_url, cta_style.
   *
   * @return array<string, mixed>
   *   Footer component render array.
   */
  public function buildFooter(array $props): array {
    return [
      '#type' => 'component',
      '#component' => self::COMPONENT_FOOTER,
      '#props' => [
        'cta_label' => (string) ($props['cta_label'] ?? ''),
        'cta_url' => (string) ($props['cta_url'] ?? ''),
        'cta_style' => (string) ($props['cta_style'] ?? 'outline'),
      ],
    ];
  }

}
