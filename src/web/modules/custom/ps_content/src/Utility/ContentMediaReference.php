<?php

declare(strict_types=1);

namespace Drupal\ps_content\Utility;

/**
 * Resolved media reference for content blocks (URL, alt, credit, cache).
 */
final readonly class ContentMediaReference {

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
