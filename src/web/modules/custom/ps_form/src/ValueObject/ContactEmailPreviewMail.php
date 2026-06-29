<?php

declare(strict_types=1);

namespace Drupal\ps_form\ValueObject;

/**
 * Minimal mail object for admin confirmation email preview rendering.
 *
 * Satisfies symfony_mailer email_wrap theme suggestions and preprocess.
 */
final class ContactEmailPreviewMail {

  public function __construct(
    private readonly string $subject,
    private readonly string $tag,
  ) {}

  /**
   * Returns the preview email subject.
   */
  public function getSubject(): string {
    return $this->subject;
  }

  /**
   * Returns a dot-separated tag part or the full tag.
   */
  public function getTag(?int $part = NULL): string {
    if ($part === NULL) {
      return $this->tag;
    }

    return explode('.', $this->tag)[$part] ?? '';
  }

  /**
   * Builds theme hook suggestions from the dot-separated tag.
   *
   * @return list<string>
   *   Theme hook suggestion candidates.
   */
  public function getSuggestions(string $initial, string $join): array {
    $parts = explode('.', $this->tag);
    $part = $initial !== '' ? $initial : (string) array_shift($parts);
    $suggestions = [$part];

    while ($parts !== []) {
      $part .= $join . array_shift($parts);
      $suggestions[] = $part;
    }

    return $suggestions;
  }

}
