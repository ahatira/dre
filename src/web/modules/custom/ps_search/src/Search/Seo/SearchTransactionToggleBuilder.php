<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Seo;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds transaction toggle items for header/hero search entry points.
 */
final class SearchTransactionToggleBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly SearchPathResolverInterface $searchPathResolver,
    private readonly DictionaryResolver $dictionaryResolver,
  ) {}

  /**
   * @return array<string, mixed>
   *   Props for ps_theme:transaction-toggle.
   */
  public function buildProps(): array {
    return ['items' => $this->buildItems()];
  }

  /**
   * @return list<array<string, mixed>>
   */
  public function buildItems(): array {
    $request = $this->requestStack->getCurrentRequest();
    $current = strtoupper((string) ($request?->query->get('operation_type') ?? 'LOC'));
    $queryBase = $request?->query->all() ?? [];

    $definitions = [
      ['code' => 'LOC', 'label' => $this->resolveLabel('operation_type', 'LOC') ?: (string) $this->t('Rent')],
      ['code' => 'VEN', 'label' => $this->resolveLabel('operation_type', 'VEN') ?: (string) $this->t('Buy')],
    ];

    $searchPath = $this->searchPathResolver->getPublicPath();
    $items = [];
    foreach ($definitions as $definition) {
      $query = $queryBase;
      $query['operation_type'] = $definition['code'];
      $items[] = [
        'label' => $definition['label'],
        'url' => Url::fromUserInput($searchPath, ['query' => $query])->toString(),
        'active' => $current === $definition['code'],
        'code' => $definition['code'],
      ];
    }

    return $items;
  }

  private function resolveLabel(string $type, string $code): string {
    $label = $this->dictionaryResolver->resolveLabel($type, $code);
    return $label ?: $code;
  }

}
