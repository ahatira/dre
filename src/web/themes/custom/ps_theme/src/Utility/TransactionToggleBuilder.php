<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds transaction toggle items for search (Louer / Acheter).
 */
final class TransactionToggleBuilder {

  use StringTranslationTrait;

  /**
   * @return array<string, mixed>
   *   Props for ps_theme:transaction-toggle.
   */
  public static function build(): array {
    $instance = new self();
    return ['items' => $instance->buildItems()];
  }

  /**
   * @return list<array<string, mixed>>
   */
  private function buildItems(): array {
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = \Drupal::service(RequestStack::class)->getCurrentRequest();
    $current = strtoupper((string) $request->query->get('operation_type', ''));
    $queryBase = $request->query->all();

    $definitions = [
      ['code' => 'LOC', 'label' => $this->dictionaryLabel('operation_type', 'LOC') ?: (string) $this->t('Rent')],
      ['code' => 'VEN', 'label' => $this->dictionaryLabel('operation_type', 'VEN') ?: (string) $this->t('Buy')],
    ];

    $items = [];
    foreach ($definitions as $definition) {
      $query = $queryBase;
      $query['operation_type'] = $definition['code'];
      $items[] = [
        'label' => $definition['label'],
        'url' => Url::fromUserInput('/recherche', ['query' => $query])->toString(),
        'active' => $current === $definition['code'],
        'code' => $definition['code'],
      ];
    }

    return $items;
  }

  private function dictionaryLabel(string $type, string $code): string {
    $label = \Drupal::service('ps_dictionary.resolver')->resolveLabel($type, $code);
    return $label ?: $code;
  }

}
