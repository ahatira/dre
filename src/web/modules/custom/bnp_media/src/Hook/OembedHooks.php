<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Hook;

use Drupal\bnp_media\Service\OembedThemeSuggestionBuilder;
use Drupal\bnp_media\Service\ProviderLibraryAttacher;
use Drupal\Core\Hook\Attribute\Hook;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * OEmbed preprocessing and theme suggestion hooks.
 */
final class OembedHooks {

  public function __construct(
    private readonly ProviderLibraryAttacher $providerLibraryAttacher,
    private readonly OembedThemeSuggestionBuilder $oembedThemeSuggestionBuilder,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Implements hook_preprocess_media_oembed_iframe().
   */
  #[Hook('preprocess_media_oembed_iframe')]
  public function preprocessMediaOembedIframe(array &$variables): void {
    $provider = '';

    if (!empty($variables['resource']) && method_exists($variables['resource'], 'getProvider')) {
      $resource_provider = $variables['resource']->getProvider();
      if ($resource_provider !== NULL && method_exists($resource_provider, 'getName')) {
        $provider = strtolower((string) $resource_provider->getName());
      }
    }

    if ($provider === '') {
      $provider = strtolower(
        (string) ($this->requestStack->getCurrentRequest()?->query->get('provider') ?? '')
      );
    }

    $variables['provider'] = $provider;
    $this->providerLibraryAttacher->attach($variables, $provider);
  }

  /**
   * Implements hook_theme_suggestions_media_oembed_iframe_alter().
   */
  #[Hook('theme_suggestions_media_oembed_iframe_alter')]
  public function themeSuggestionsMediaOembedIframeAlter(array &$suggestions, array $variables): void {
    $provider = $variables['provider']
      ?? (string) ($this->requestStack->getCurrentRequest()?->query->get('provider') ?? '');

    $extra_suggestions = $this->oembedThemeSuggestionBuilder->buildSuggestions((string) $provider);

    foreach ($extra_suggestions as $suggestion) {
      $suggestions[] = $suggestion;
    }
  }

}
