<?php

declare(strict_types=1);

namespace Drupal\ps_compare;

/**
 * Render contexts for the comparison table.
 */
final class CompareRenderContext {

  public const PAGE = 'page';

  public const MODAL = 'modal';

  public const EMAIL = 'email';

  /**
   * @return list<string>
   */
  public static function all(): array {
    return [self::PAGE, self::MODAL, self::EMAIL];
  }

}
