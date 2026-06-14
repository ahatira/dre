<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Url;

/**
 * Default bnppre.fr §3 illustration paths (SVG, one per accordion item).
 */
final class ToolsDefaultAssets {

  /**
   * @var list<string>
   */
  private const ILLUSTRATION_PATHS = [
    'assets/images/tools/illustration-coworking-test.svg',
    'assets/images/tools/illustration-surface-calculator.svg',
    'assets/images/tools/illustration-guides.svg',
  ];

  /**
   * @var list<string>
   */
  private const ILLUSTRATION_ALTS = [
    'Classic offices or coworking decision tool',
    'Office space calculator',
    'Guides and practical resources',
  ];

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  public function imageUrl(int $index): string {
    $relative = self::ILLUSTRATION_PATHS[$index] ?? self::ILLUSTRATION_PATHS[0];
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');

    return Url::fromUri('base:' . $themePath . '/' . $relative)->toString();
  }

  public function imageAlt(int $index): string {
    return self::ILLUSTRATION_ALTS[$index] ?? self::ILLUSTRATION_ALTS[0];
  }

  public function imageCredit(int $index): string {
    return '';
  }

}
