<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Resolved homepage media reference (URL, alt, credit, cache metadata).
 */
final readonly class HomepageMediaReference {

  /**
   * @param list<string> $cacheTags
   */
  public function __construct(
    public ?string $url,
    public string $alt,
    public string $credit,
    public array $cacheTags = [],
  ) {}

  public static function empty(): self {
    return new self(NULL, '', '', []);
  }

}
