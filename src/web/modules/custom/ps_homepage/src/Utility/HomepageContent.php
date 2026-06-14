<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Homepage runtime helpers.
 */
final class HomepageContent {

  public static function langcode(?LanguageManagerInterface $languageManager = NULL): string {
    $languageManager ??= \Drupal::languageManager();
    return $languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
  }

}
