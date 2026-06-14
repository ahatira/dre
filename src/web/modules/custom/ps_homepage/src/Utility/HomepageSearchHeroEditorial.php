<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Editorial field keys and resolution for the homepage search hero block.
 */
final class HomepageSearchHeroEditorial {

  /**
   * Text fields stored in monolingual block configuration per layout translation.
   *
   * @var list<string>
   */
  public const TEXT_KEYS = [
    'title',
    'transaction_type_label',
    'op_buy_label',
    'op_rent_label',
    'op_flexible_label',
    'location_label',
    'location_placeholder',
    'property_type_label',
    'property_type_placeholder',
    'surface_min_label',
    'surface_min_placeholder',
    'price_max_label',
    'price_max_placeholder',
    'optional_label',
    'search_button_label',
    'delegate_prompt',
    'delegate_tooltip',
    'delegate_button_label',
    'promo_title',
    'promo_offers_line',
    'promo_description',
    'promo_cta_label',
    'promo_offers_template',
  ];

  /**
   * URL fields stored in monolingual block configuration.
   *
   * @var list<string>
   */
  public const URL_KEYS = [
    'delegate_url',
    'promo_cta_url',
  ];

  /**
   * Returns minimal default block plugin configuration.
   *
   * @return array<string, mixed>
   */
  public static function defaultBlockConfiguration(): array {
    return [
      'background_image' => NULL,
      'promo_background_image' => NULL,
      'promo_offers_use_dynamic' => TRUE,
    ];
  }

  /**
   * Resolves editorial values for rendering from monolingual configuration.
   *
   * @param array<string, mixed> $config
   * @param int|null $offerCount
   *   Dynamic offer count when enabled.
   *
   * @return array<string, string>
   */
  public static function resolve(array $config, ?int $offerCount = NULL): array {
    $resolved = [];

    foreach (self::TEXT_KEYS as $key) {
      if ($key === 'promo_offers_line' || $key === 'promo_offers_template') {
        continue;
      }
      $resolved[$key] = HomepageBlockConfiguration::string($config, $key);
    }

    foreach (self::URL_KEYS as $key) {
      $resolved[$key] = HomepageBlockConfiguration::string($config, $key);
    }

    $resolved['promo_offers_line'] = self::resolveOffersLine($config, $offerCount);
    $resolved['promo_description'] = self::formatDescription($resolved['promo_description']);

    return $resolved;
  }

  /**
   * @param array<string, mixed> $config
   */
  private static function resolveOffersLine(array $config, ?int $offerCount): string {
    $useDynamic = (bool) ($config['promo_offers_use_dynamic'] ?? TRUE);
    if (!$useDynamic) {
      return HomepageBlockConfiguration::string($config, 'promo_offers_line');
    }

    $template = HomepageBlockConfiguration::string($config, 'promo_offers_template');
    if ($template === '') {
      return '';
    }

    if ($offerCount === NULL) {
      return $template;
    }

    return str_replace('@count', number_format($offerCount, 0, '', ' '), $template);
  }

  private static function formatDescription(string $value): string {
    if ($value === '') {
      return '';
    }

    return (string) check_markup($value, 'basic_html');
  }

}
