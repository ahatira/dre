<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Reads ps_search.engine_settings feature flags and options.
 */
final class SearchEngineSettingsReader {

  private const CONFIG = 'ps_search.engine_settings';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Whether SearchContext should drive query execution.
   */
  public function isSearchContextEnabled(): bool {
    $features = $this->configFactory->get(self::CONFIG)->get('features') ?? [];
    return (bool) ($features['use_search_context'] ?? FALSE);
  }

  /**
   * Whether legacy LocationSearchFilter remains active when context is enabled.
   */
  public function isLegacyLocationFilterEnabled(): bool {
    $features = $this->configFactory->get(self::CONFIG)->get('features') ?? [];
    return (bool) ($features['legacy_location_filter'] ?? TRUE);
  }

}
