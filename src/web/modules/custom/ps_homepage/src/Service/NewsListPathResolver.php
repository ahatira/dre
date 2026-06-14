<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Resolves the public news listing path for the active language.
 */
final class NewsListPathResolver {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  public function getPublicPath(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $language = $this->languageManager->getLanguage($langcode);
    if ($language === NULL) {
      return '/news';
    }

    return Url::fromUserInput('/news', ['language' => $language])->toString();
  }

}
