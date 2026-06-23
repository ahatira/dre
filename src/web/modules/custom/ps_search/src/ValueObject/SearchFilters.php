<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Normalized business filters for search v2.
 */
final readonly class SearchFilters {

  /**
   * @param array<string, mixed> $moreCriteria
   */
  public function __construct(
    public ?string $operationType,
    public ?string $assetType,
    public ?RangeFilter $surface,
    public ?RangeFilter $budget,
    public ?RangeFilter $capacity,
    public array $moreCriteria,
  ) {}

  /**
   * Whether any business filter is active.
   */
  public function hasAnyFilter(): bool {
    return $this->operationType !== NULL
      || $this->assetType !== NULL
      || ($this->surface !== NULL && !$this->surface->isEmpty())
      || ($this->budget !== NULL && !$this->budget->isEmpty())
      || ($this->capacity !== NULL && !$this->capacity->isEmpty())
      || $this->moreCriteria !== [];
  }

}
