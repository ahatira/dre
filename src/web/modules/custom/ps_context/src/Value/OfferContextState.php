<?php

declare(strict_types=1);

namespace Drupal\ps_context\Value;

/**
 * Resolved visibility state for an offer driven by ps_context rules.
 */
final class OfferContextState {

  /**
   * @param array<string, bool> $tabs
   *   Tab machine name => visible.
   * @param array<string, bool> $fields
   *   Field machine name => visible.
   * @param array<string, string> $defaults
   *   Field machine name => default value from set_default actions.
   */
  public function __construct(
    private readonly array $tabs = [],
    private readonly array $fields = [],
    private readonly array $defaults = [],
  ) {}

  /**
   * Whether a dynamic tab is visible.
   *
   * Tabs not tracked by any matrix rule default to visible.
   */
  public function isTabVisible(string $tab): bool {
    return $this->tabs[$tab] ?? TRUE;
  }

  /**
   * Whether a dynamic field wrapper is visible.
   *
   * Fields not tracked by any matrix rule default to visible.
   */
  public function isFieldVisible(string $field): bool {
    return $this->fields[$field] ?? TRUE;
  }

  /**
   * Whether the offer is capacity-driven per the matrix (surface hidden, capacity shown).
   */
  public function isCapacityDriven(): bool {
    return !$this->isTabVisible('group_surface') && $this->isTabVisible('group_capacity');
  }

  /**
   * @return array<string, bool>
   */
  public function getTabs(): array {
    return $this->tabs;
  }

  /**
   * @return array<string, bool>
   */
  public function getFields(): array {
    return $this->fields;
  }

  /**
   * @return array<string, string>
   */
  public function getDefaults(): array {
    return $this->defaults;
  }

}
