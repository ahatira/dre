<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Stub;

/**
 * Minimal field item stub for entity field access in unit tests.
 */
final class TestFieldItemStub {

  public function __construct(public string $value) {}

  /**
   * Whether the field value is empty.
   */
  public function isEmpty(): bool {
    return $this->value === '';
  }

}
