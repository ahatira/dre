<?php

declare(strict_types=1);

namespace Drupal\ps_compare\PathProcessor;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_compare\Service\ComparePathResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Maps translated compare page slugs to the internal route path.
 */
final class CompareBasePathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  public function __construct(
    private readonly ComparePathResolver $comparePathResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   *
   */
  public function processInbound($path, Request $request): string {
    if (!$this->comparePathResolver->isComparePath($path)) {
      return $path;
    }

    return $this->comparePathResolver->getInternalPath();
  }

  /**
   *
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if ($path !== $this->comparePathResolver->getInternalPath()) {
      return $path;
    }

    $langcode = $this->resolveOutboundLangcode($options);
    return $this->comparePathResolver->getPublicPath($langcode);
  }

  /**
   *
   */
  private function resolveOutboundLangcode(array $options): string {
    if (isset($options['language']) && $options['language'] instanceof LanguageInterface) {
      return $options['language']->getId();
    }

    return $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
  }

}
