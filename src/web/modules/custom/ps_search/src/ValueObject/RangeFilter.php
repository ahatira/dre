<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Inclusive numeric range filter (min and/or max).
 */
final readonly class RangeFilter {

  public function __construct(
    public ?float $min = NULL,
    public ?float $max = NULL,
  ) {}

  /**
   * Whether at least one bound is set.
   */
  public function isEmpty(): bool {
    return $this->min === NULL && $this->max === NULL;
  }

}
