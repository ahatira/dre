<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Builds offer detail section headings (icon + translatable label).
 */
final class OfferSectionHeadingBuilder implements CacheableDependencyInterface {

  public function __construct(
    private readonly OfferSectionRegistry $sectionRegistry,
  ) {}

  /**
   * Builds a section heading render array (h2 by default).
   *
   * @param string $section_id
   *   Section plugin ID.
   * @param array<string, mixed> $options
   *   Options: tag (default h2), title_classes, icon_settings.
   *
   * @return array<string, mixed>
   *   Render array for the heading.
   */
  public function buildTitle(string $section_id, array $options = []): array {
    $tag = (string) ($options['tag'] ?? 'h2');
    $title_classes = ['ps-offer-section__title'];
    if (!empty($options['title_classes'])) {
      $title_classes = array_merge($title_classes, (array) $options['title_classes']);
    }

    return [
      '#type' => 'html_tag',
      '#tag' => $tag,
      '#attributes' => ['class' => array_values(array_unique($title_classes))],
      'content' => $this->buildTitleContent($section_id, $options),
    ];
  }

  /**
   * Builds the inner title content (icon + text) for field preprocess hooks.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function buildTitleContent(string $section_id, array $options = []): array {
    $label = $this->sectionRegistry->getLabel($section_id);
    if ($label === '') {
      return [];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section__title-content']],
    ];

    $icon = $this->buildIcon($section_id, $options);
    if ($icon !== []) {
      $build['icon'] = $icon;
    }

    $build['text'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $label,
      '#attributes' => ['class' => ['ps-offer-section__title-text']],
    ];

    $build['#cache']['tags'] = $this->getCacheTags();

    return $build;
  }

  /**
   * Builds the section icon render array.
   *
   * @return array<string, mixed>
   *   Render array, or empty when no icon is configured.
   */
  public function buildIcon(string $section_id, array $options = []): array {
    $icon_id = $this->sectionRegistry->getIconId($section_id);
    if ($icon_id === '') {
      return [];
    }

    $parts = IconIdUtility::splitIconId($icon_id);
    if ($parts === NULL) {
      return [];
    }

    $icon_settings = ($options['icon_settings'] ?? []) + [
      'size' => '24px',
      'alt' => '',
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section__title-icon']],
      'icon' => IconDefinition::getRenderable($parts['pack'] . ':' . $parts['id'], $icon_settings),
      '#cache' => [
        'tags' => $this->getCacheTags(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return $this->sectionRegistry->getCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

}
