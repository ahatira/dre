<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Editorial field keys and defaults for the homepage search hero block.
 */
final class HomepageSearchHeroEditorial {

  /**
   * Text fields stored as {key}_en / {key}_fr in block configuration.
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
   * URL fields stored as {key}_en / {key}_fr.
   *
   * @var list<string>
   */
  public const URL_KEYS = [
    'delegate_url',
    'promo_cta_url',
  ];

  /**
   * Default EN copy seeded at install (formerly ps_demo.homepage hero).
   *
   * @var array<string, array{en: string, fr: string}>
   */
  private const INSTALL_DEFAULTS = [
    'title' => [
      'en' => 'What are you looking for?',
      'fr' => 'Que recherchez-vous ?',
    ],
    'transaction_type_label' => [
      'en' => 'Transaction type',
      'fr' => 'Type de transaction',
    ],
    'op_buy_label' => ['en' => 'Buy', 'fr' => 'Acheter'],
    'op_rent_label' => ['en' => 'Rent', 'fr' => 'Louer'],
    'op_flexible_label' => ['en' => "I'm flexible", 'fr' => 'Indifférent'],
    'location_label' => ['en' => 'Location(s)', 'fr' => 'Localisation(s)'],
    'location_placeholder' => [
      'en' => 'City, district or zip code',
      'fr' => 'Ville, quartier ou code postal',
    ],
    'property_type_label' => ['en' => 'Property type', 'fr' => 'Type de bien'],
    'property_type_placeholder' => [
      'en' => 'Type of property',
      'fr' => 'Type de bien',
    ],
    'surface_min_label' => [
      'en' => 'Minimum surface (m²)',
      'fr' => 'Surface minimum (m²)',
    ],
    'surface_min_placeholder' => ['en' => '200 m²', 'fr' => '200 m²'],
    'price_max_label' => ['en' => 'Maximum price', 'fr' => 'Prix maximum'],
    'price_max_placeholder' => ['en' => '10 000', 'fr' => '10 000'],
    'optional_label' => ['en' => 'Optional', 'fr' => 'Facultatif'],
    'search_button_label' => ['en' => 'Search', 'fr' => 'Rechercher'],
    'delegate_prompt' => [
      'en' => 'Would you like to delegate your search?',
      'fr' => 'Souhaitez-vous déléguer votre recherche ?',
    ],
    'delegate_tooltip' => [
      'en' => 'To find your next business premises, contact our BNP Paribas Real Estate experts and delegate your search.',
      'fr' => 'Pour trouver votre prochain local professionnel, contactez nos experts BNP Paribas Real Estate et déléguez votre recherche.',
    ],
    'delegate_button_label' => [
      'en' => 'Delegate my search',
      'fr' => 'Déléguer ma recherche',
    ],
    'delegate_url' => ['en' => '/contact', 'fr' => '/contact'],
    'promo_title' => [
      'en' => 'Commercial real estate: find your future business premises',
      'fr' => "Immobilier d'entreprise : trouvez vos futurs locaux professionnels",
    ],
    'promo_offers_template' => [
      'en' => '@count offers currently available',
      'fr' => '@count offres actuellement disponibles',
    ],
    'promo_offers_line' => [
      'en' => '5990 offers currently available',
      'fr' => '5990 offres actuellement disponibles',
    ],
    'promo_description' => [
      'en' => "Offices, coworking spaces, logistics warehouses, business premises or retail premises: BNP Paribas Real Estate's commercial real estate offer covers all the assets on the market to better meet your expectations.",
      'fr' => "Bureaux, espaces de coworking, entrepôts logistiques, locaux d'activité ou commerces : l'offre immobilière d'entreprise de BNP Paribas Real Estate couvre l'ensemble des actifs du marché pour mieux répondre à vos attentes.",
    ],
    'promo_cta_label' => [
      'en' => 'Discover our service offers',
      'fr' => 'Découvrir nos offres de services',
    ],
    'promo_cta_url' => [
      'en' => '/find-property',
      'fr' => '/recherche-immobiliere',
    ],
  ];

  /**
   * Returns default block plugin configuration.
   *
   * @return array<string, mixed>
   *   Default block plugin configuration.
   */
  public static function defaultBlockConfiguration(): array {
    $config = [
      'background_image' => NULL,
      'promo_background_image' => NULL,
      'background_alt' => '',
      'promo_background_alt' => '',
      'promo_offers_use_dynamic' => TRUE,
    ];

    foreach (self::TEXT_KEYS as $key) {
      $config[$key . '_en'] = self::INSTALL_DEFAULTS[$key]['en'] ?? '';
      $config[$key . '_fr'] = self::INSTALL_DEFAULTS[$key]['fr'] ?? '';
    }
    foreach (self::URL_KEYS as $key) {
      $config[$key . '_en'] = self::INSTALL_DEFAULTS[$key]['en'] ?? '';
      $config[$key . '_fr'] = self::INSTALL_DEFAULTS[$key]['fr'] ?? '';
    }

    return $config;
  }

  /**
   * Resolves localized editorial values for rendering.
   *
   * @param array<string, mixed> $config
   *   Block plugin configuration.
   * @param string $langcode
   *   Interface language code.
   * @param int|null $offerCount
   *   Dynamic offer count when enabled.
   *
   * @return array<string, string>
   */
  public static function resolve(array $config, string $langcode, ?int $offerCount = NULL): array {
    $resolved = [];

    foreach (self::TEXT_KEYS as $key) {
      if ($key === 'promo_offers_line' || $key === 'promo_offers_template') {
        continue;
      }
      $resolved[$key] = self::localizedValue($config, $key, $langcode);
    }

    foreach (self::URL_KEYS as $key) {
      $resolved[$key] = self::localizedValue($config, $key, $langcode);
    }

    $resolved['promo_offers_line'] = self::resolveOffersLine($config, $langcode, $offerCount);
    $resolved['promo_description'] = self::formatDescription($resolved['promo_description']);

    $resolved['background_alt'] = trim((string) ($config['background_alt'] ?? ''));
    if ($resolved['background_alt'] === '') {
      $resolved['background_alt'] = $resolved['title'];
    }

    $resolved['promo_background_alt'] = trim((string) ($config['promo_background_alt'] ?? ''));
    if ($resolved['promo_background_alt'] === '') {
      $resolved['promo_background_alt'] = $resolved['promo_title'];
    }

    return $resolved;
  }

  /**
   * Resolves the promo offers line from dynamic or manual config.
   *
   * @param array<string, mixed> $config
   *   Block plugin configuration.
   * @param string $langcode
   *   Interface language code.
   * @param int|null $offerCount
   *   Dynamic offer count when enabled.
   *
   * @return string
   *   Localized offers line.
   */
  private static function resolveOffersLine(array $config, string $langcode, ?int $offerCount): string {
    $useDynamic = (bool) ($config['promo_offers_use_dynamic'] ?? TRUE);
    if (!$useDynamic) {
      return self::localizedValue($config, 'promo_offers_line', $langcode);
    }

    $template = self::localizedValue($config, 'promo_offers_template', $langcode);
    if ($template === '') {
      $template = self::INSTALL_DEFAULTS['promo_offers_template'][$langcode]
        ?? self::INSTALL_DEFAULTS['promo_offers_template']['en'];
    }

    if ($offerCount === NULL) {
      return $template;
    }

    return str_replace('@count', number_format($offerCount, 0, '', ' '), $template);
  }

  /**
   * Resolves a localized config value for the active language.
   *
   * @param array<string, mixed> $config
   *   Block plugin configuration.
   * @param string $key
   *   Field key without language suffix.
   * @param string $langcode
   *   Interface language code.
   *
   * @return string
   *   Localized value.
   */
  private static function localizedValue(array $config, string $key, string $langcode): string {
    $field = $key . '_' . $langcode;
    $value = trim((string) ($config[$field] ?? ''));
    if ($value !== '') {
      return $value;
    }

    return trim((string) ($config[$key . '_en'] ?? ''));
  }

  /**
   * Formats promo description with the basic_html filter.
   */
  private static function formatDescription(string $value): string {
    if ($value === '') {
      return '';
    }

    return (string) check_markup($value, 'basic_html');
  }

}
