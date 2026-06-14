<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Builds the expert journey body render array (§6 Experts).
 */
final class ExpertsAccompagnementBuilder {

  public function __construct(
    private readonly ContentMediaResolver $mediaResolver,
    private readonly ExpertJourneyDefaultAssets $defaultAssets,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  public function build(array $configuration): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    $steps = [];
    $cacheTags = [];
    $index = 0;
    foreach ($configuration['steps'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $label = trim((string) ($item['step_label'] ?? ''));
      if ($label === '') {
        continue;
      }

      $media = $this->mediaResolver->resolve($item['image'] ?? NULL, $langcode);
      $imageUrl = $media->url ?? $this->defaultAssets->imageUrl($index);
      $imageAlt = $media->alt !== '' ? $media->alt : $this->defaultAssets->imageAlt($index);
      $imageCredit = $media->credit !== '' ? $media->credit : $this->defaultAssets->imageCredit($index);
      $cacheTags = array_merge($cacheTags, $media->cacheTags);

      $steps[] = [
        'label' => $label,
        'title' => trim((string) ($item['step_title'] ?? '')),
        'body' => trim((string) ($item['step_body'] ?? '')),
        'image' => $imageUrl,
        'image_alt' => $imageAlt,
        'image_credit' => $imageCredit,
      ];
      $index++;
    }

    if ($steps === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
      ];
    }

    return [
      'body' => [
        'content' => [
          '#type' => 'component',
          '#component' => 'ps_theme:expert-steps',
          '#props' => [
            'steps' => $steps,
          ],
        ],
      ],
      'attached' => [
        'library' => ['ps_content/media_credit'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique(array_merge(['config:block.block'], $cacheTags))),
      ],
    ];
  }

}
