<?php

declare(strict_types=1);

namespace Drupal\ps_faq\Service;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Builds FAQ accordion render arrays for block display.
 */
final class FaqAccordionBuilder {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param list<array{weight?: int, nid?: int}> $faqItems
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>
   * }
   */
  public function build(array $faqItems): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    $nids = [];
    foreach ($faqItems as $item) {
      if (!is_array($item)) {
        continue;
      }
      $nid = (int) ($item['nid'] ?? 0);
      if ($nid > 0) {
        $nids[] = $nid;
      }
    }

    if ($nids === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
      ];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $accordionItems = [];
    foreach ($nids as $nid) {
      $node = $nodes[$nid] ?? NULL;
      if (!$node instanceof NodeInterface || !$node->isPublished()) {
        continue;
      }

      if ($node->hasTranslation($langcode)) {
        $node = $node->getTranslation($langcode);
      }

      $question = trim((string) $node->get('field_question')->value);
      if ($question === '') {
        continue;
      }

      $answerField = $node->get('field_answer');
      $answerHtml = $answerField->isEmpty() ? '' : (string) $answerField->processed;

      $accordionItems[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnp:accordion_item',
        '#slots' => [
          'title' => $question,
          'content' => [
            '#markup' => $answerHtml,
          ],
        ],
        '#props' => [
          'opened' => FALSE,
          'item_id' => 'ps-faq-' . $nid,
          'heading_level' => 3,
        ],
      ];
    }

    if ($accordionItems === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
      ];
    }

    $body = [
      'accordion_wrapper' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-faq__accordion']],
        'accordion' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnp:accordion',
          '#props' => [
            'keep_open' => FALSE,
            'accordion_id' => Html::getUniqueId('ps-faq'),
          ],
          '#slots' => [
            'content' => $accordionItems,
          ],
        ],
      ],
    ];

    return [
      'body' => $body,
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_merge(
          ['config:block.block'],
          array_map(static fn (int $nid): string => 'node:' . $nid, $nids),
        ),
      ],
      'attached' => [
        'library' => ['ps_faq/faq_accordion'],
      ],
    ];
  }

}
