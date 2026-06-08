<?php

declare(strict_types=1);

namespace Drupal\ps_search\PathProcessor;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_search\Service\SearchPathResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Maps translated search page slugs to the internal Views route path.
 *
 * Inbound:  /fr/recherche-immobiliere → /find-property (internal machine path)
 * Outbound: /find-property → /fr/recherche-immobiliere (language-specific public slug)
 */
final class SearchBasePathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  public function __construct(
    private readonly SearchPathResolver $searchPathResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request): string {
    if (!$this->searchPathResolver->isSearchPath($path)) {
      return $path;
    }

    return $this->searchPathResolver->getInternalPath();
  }

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if ($path !== $this->searchPathResolver->getInternalPath()) {
      return $path;
    }

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    return $this->searchPathResolver->getPublicPath($langcode);
  }

}
