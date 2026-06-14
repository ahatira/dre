<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Component\Utility\Html;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Builds the tools accordion body render array (§3 Outils).
 */
final class OutilsAccordionBuilder {

  public function __construct(
    private readonly ContentMediaResolver $mediaResolver,
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
    $items = $this->normalizeItems($configuration, $langcode);

    $accordionItems = [];
    $illustrationSlides = [];
    $cacheTags = ['config:block.block'];
    $activeIllustrationIndex = NULL;

    foreach ($items as $index => $item) {
      $question = trim((string) ($item['question'] ?? ''));
      if ($question === '') {
        continue;
      }

      $answer = trim((string) ($item['answer'] ?? ''));
      $linkLabel = trim((string) ($item['link_label'] ?? ''));
      $linkUrl = trim((string) ($item['link_url'] ?? ''));

      $content = [];
      if ($answer !== '') {
        $content['answer'] = [
          '#markup' => check_markup($answer, 'basic_html'),
        ];
      }
      if ($linkLabel !== '' && $linkUrl !== '') {
        $content['link'] = [
          '#type' => 'link',
          '#title' => $linkLabel,
          '#url' => Url::fromUserInput($linkUrl),
          '#attributes' => ['class' => ['ps-homepage-tools__item-link']],
        ];
      }

      $itemId = 'ps-homepage-tools-' . $index;
      if (!empty($item['illustration'])) {
        $media = $this->mediaResolver->resolve($item['illustration'], $langcode);
        if ($media->url !== NULL) {
          $illustrationSlides[] = [
            'item_id' => $itemId,
            'url' => $media->url,
            'alt' => $media->alt,
            'credit' => $media->credit,
          ];
          $cacheTags = array_merge($cacheTags, $media->cacheTags);
          if (!empty($item['opened_by_default'])) {
            $activeIllustrationIndex = $itemId;
          }
          elseif ($activeIllustrationIndex === NULL) {
            $activeIllustrationIndex = $itemId;
          }
        }
      }

      $accordionItems[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnp:accordion_item',
        '#slots' => [
          'title' => $question,
          'content' => $content,
        ],
        '#props' => [
          'opened' => !empty($item['opened_by_default']),
          'item_id' => $itemId,
          'heading_level' => 3,
        ],
      ];
    }

    if ($accordionItems === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
      ];
    }

    $activeSlide = NULL;
    foreach ($illustrationSlides as $slide) {
      if ($slide['item_id'] === $activeIllustrationIndex) {
        $activeSlide = $slide;
        break;
      }
    }
    $activeSlide ??= $illustrationSlides[0] ?? NULL;

    $layoutClasses = ['ps-homepage-tools__layout', 'd-flex', 'flex-column', 'flex-lg-row', 'gap-4'];
    if ($activeSlide !== NULL) {
      $layoutClasses[] = 'ps-homepage-tools__layout--with-image';
    }

    $layout = [
      '#type' => 'container',
      '#attributes' => ['class' => $layoutClasses],
      'accordion' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-tools__accordion']],
        'widget' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnp:accordion',
          '#props' => [
            'keep_open' => FALSE,
            'accordion_id' => Html::getUniqueId('ps-homepage-tools'),
          ],
          '#slots' => [
            'content' => $accordionItems,
          ],
        ],
      ],
    ];

    if ($activeSlide !== NULL) {
      $layout['illustration'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-homepage-tools__illustration'],
          'data-tools-illustration-panel' => '',
        ],
        'image' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnp:media-credit',
          '#props' => [
            'image_url' => $activeSlide['url'],
            'image_alt' => $activeSlide['alt'],
            'credit' => $activeSlide['credit'],
            'image_class' => 'img-fluid',
            'loading' => 'lazy',
          ],
        ],
      ];
    }

    $attached = [
      'library' => ['ps_content/outils_accordion'],
    ];
    if ($illustrationSlides !== []) {
      $attached['drupalSettings']['ps_content']['outilsAccordion'] = [
        'slides' => $illustrationSlides,
      ];
    }

    return [
      'body' => ['layout' => $layout],
      'attached' => $attached,
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique($cacheTags)),
      ],
    ];
  }

  /**
   * Normalizes items and migrates legacy block-level illustration.
   *
   * @return list<array<string, mixed>>
   */
  private function normalizeItems(array $configuration, string $langcode): array {
    $items = is_array($configuration['items'] ?? NULL) ? $configuration['items'] : [];
    $legacyIllustration = $configuration['illustration'] ?? NULL;

    if ($legacyIllustration !== NULL && $legacyIllustration !== '') {
      $hasItemIllustration = FALSE;
      foreach ($items as $item) {
        if (is_array($item) && !empty($item['illustration'])) {
          $hasItemIllustration = TRUE;
          break;
        }
      }
      if (!$hasItemIllustration && isset($items[0]) && is_array($items[0])) {
        $items[0]['illustration'] = $legacyIllustration;
      }
    }

    return array_values($items);
  }

}
