<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds drupalSettings payload for the client-side SearchContext store.
 */
final class SearchContextJsSettingsBuilder {

  public function __construct(
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly SearchContextSerializerInterface $contextSerializer,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Builds drupalSettings keys for the client SearchContext store.
   *
   * @return array<string, mixed>
   *   Keys merged into drupalSettings.psSearch.
   */
  public function build(): array {
    if (!$this->engineSettings->isSearchContextEnabled()) {
      return [
        'useSearchContext' => FALSE,
      ];
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return [
        'useSearchContext' => FALSE,
      ];
    }

    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    if (!$context instanceof SearchContext) {
      $context = $this->contextSerializer->fromRequest($request);
    }

    $engineConfig = $this->configFactory->get('ps_search.engine_settings');

    return [
      'useSearchContext' => TRUE,
      'searchContext' => $this->contextSerializer->toArray($context),
      'seoPath' => $this->contextSerializer->buildSeoPath($context, $context->langcode),
      'locationRequired' => (bool) $engineConfig->get('location_required'),
    ];
  }

}
