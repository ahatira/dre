<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Builds compare bar and panel render arrays for the search page.
 */
final class ComparePanelBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly CompareOfferSummaryBuilder $offerSummaryBuilder,
    private readonly ComparePathResolver $comparePathResolver,
    private readonly CsrfTokenGenerator $csrfToken,
  ) {}

  /**
   * Builds the full compare widget (bar + panel) for search pages.
   *
   * @return array<string, mixed>
   */
  public function buildWidget(): array {
    $count = $this->compareManager->getCompareCount();
    $items = $this->buildPanelItems();

    return [
      '#theme' => 'ps_compare_widget',
      '#count' => $count,
      '#max_items' => $this->compareManager->getMaxItems(),
      '#min_items' => $this->compareManager->getMinItems(),
      '#can_compare' => $this->compareManager->canOpenComparisonPage(),
      '#compare_url' => $this->comparePathResolver->getPublicPath(),
      '#items' => $items,
      '#attached' => [
        'library' => ['ps_compare/compare-panel'],
        'drupalSettings' => [
          'psCompare' => [
            'csrfToken' => $this->csrfToken->get('ps_compare.toggle'),
            'countEndpoint' => Url::fromRoute('ps_compare.count')->toString(),
            'stateEndpoint' => Url::fromRoute('ps_compare.state')->toString(),
            'panelEndpoint' => Url::fromRoute('ps_compare.panel')->toString(),
            'panelListEndpoint' => Url::fromRoute('ps_compare.panel_list')->toString(),
            'modalEndpoint' => Url::fromRoute('ps_compare.modal')->toString(),
            'shareModalEndpoint' => Url::fromRoute('ps_compare.share_modal')->toString(),
            'compareUrl' => $this->comparePathResolver->getPublicPath(),
            'maxItems' => $this->compareManager->getMaxItems(),
            'minItems' => $this->compareManager->getMinItems(),
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['session', 'user'],
        'tags' => ['ps_compare:list', 'ps_compare:count'],
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Builds panel payload for JSON responses.
   *
   * @return array<string, mixed>
   */
  public function buildPanelPayload(): array {
    return [
      'count' => $this->compareManager->getCompareCount(),
      'minItems' => $this->compareManager->getMinItems(),
      'maxItems' => $this->compareManager->getMaxItems(),
      'canCompare' => $this->compareManager->canOpenComparisonPage(),
      'compareUrl' => $this->comparePathResolver->getPublicPath(),
      'items' => $this->buildPanelItems(),
    ];
  }

  /**
   * Builds a render array for the panel item list.
   *
   * @return array<string, mixed>
   */
  public function buildPanelListRenderArray(): array {
    $items = $this->buildPanelItems();
    $count = count($items);
    $maxItems = $this->compareManager->getMaxItems();

    return [
      '#theme' => 'ps_compare_panel_list',
      '#items' => $items,
      '#count' => $count,
      '#max_items' => $maxItems,
      '#min_items' => $this->compareManager->getMinItems(),
      '#remaining_slots' => max(0, $maxItems - $count),
    ];
  }

  /**
   * @return list<array<string, mixed>>
   */
  public function buildPanelItems(): array {
    $items = [];
    foreach ($this->compareManager->getCompareList('node') as $entity) {
      if (!$entity instanceof NodeInterface) {
        continue;
      }

      $summary = $this->offerSummaryBuilder->build($entity);
      if ($summary === NULL) {
        continue;
      }

      $entityId = (int) $entity->id();
      $items[] = array_merge($summary, [
        'toggle_url' => Url::fromRoute('ps_compare.toggle', [
          'entity_type_id' => $entity->getEntityTypeId(),
          'entity_id' => $entityId,
        ])->toString(),
        'is_compared' => TRUE,
      ]);
    }

    return $items;
  }

}
