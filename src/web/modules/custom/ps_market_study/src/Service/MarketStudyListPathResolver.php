<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Service;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Resolves public market study listing paths per language.
 */
final class MarketStudyListPathResolver {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  public function getPublicPath(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $language = $this->languageManager->getLanguage($langcode);
    if ($language === NULL) {
      return '/research';
    }
    return Url::fromUserInput('/research', ['language' => $language])->toString();
  }

}
