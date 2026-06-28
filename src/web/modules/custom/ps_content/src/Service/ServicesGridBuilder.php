<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Builds the services grid body render array (§2 Services).
 */
final class ServicesGridBuilder {

  public function __construct(
    private readonly ContentCtaLinkBuilder $ctaLinkBuilder,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>
   * }
   */
  public function build(array $configuration): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $items = $configuration['items'] ?? [];

    $columns = [];
    foreach ($items as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['card_title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $iconParts = IconIdUtility::resolveParts($item['icon'] ?? '', 'bnp_custom', 'offices');
      $cta = $this->ctaLinkBuilder->resolve($item, $langcode);

      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-services__item']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:icon-card',
          '#props' => [
            'icon_pack' => $iconParts['pack'],
            'icon_id' => $iconParts['id'],
            'title' => $title,
            'body' => trim((string) ($item['body'] ?? '')),
            'button_label' => trim((string) ($item['button_label'] ?? '')),
            'button_url' => $cta['url'],
            'button_style' => (string) ($item['button_style'] ?? 'outline'),
            'link_type' => $cta['link_type'],
            'modal_id' => $cta['modal_id'],
            'dialog_attributes' => $cta['dialog_attributes'],
          ],
        ],
      ];
    }

    if ($columns === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
      ];
    }

    // Nest the row so Layout Builder does not hoist #attributes onto block.html.twig
    // (which breaks Bootstrap: cols must be direct children of .row).
    return [
      'body' => [
        'grid' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-services__grid']],
        ] + $columns,
        '#cache' => [
          'contexts' => ['languages:language_interface'],
          'tags' => ['config:block.block'],
        ],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

}
