<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageSectionDisplayMode;
use Drupal\ps_market_study\Service\MarketStudyListPathResolver;
use Drupal\ps_news\Service\NewsListPathResolver;

/**
 * Builds S-D Section Library templates (header / body / footer via LB layout).
 */
final class HomepageSectionLibraryTemplateBuilder {

  /**
   * Stable UUID prefix for S-D section library components.
   *
   * @var array<string, string>
   */
  private const COMPONENT_UUIDS = [
    'ps_homepage_search_hero_block' => 'b2000002-0000-4000-8000-000000000001',
    'ps_homepage_section_header_block:2' => 'b2000004-0000-4000-8000-000000000201',
    'ps_content_services_grid_block' => 'b2000004-0000-4000-8000-000000000202',
    'ps_homepage_section_header_block:3' => 'b2000004-0000-4000-8000-000000000301',
    'ps_content_outils_accordion_block' => 'b2000004-0000-4000-8000-000000000302',
    'ps_homepage_section_header_block:4' => 'b2000004-0000-4000-8000-000000000401',
    'ps_offer_offers_carousel_block' => 'b2000004-0000-4000-8000-000000000402',
    'ps_homepage_section_footer_block:4' => 'b2000004-0000-4000-8000-000000000403',
    'ps_homepage_section_header_block:5' => 'b2000004-0000-4000-8000-000000000501',
    'ps_search_search_shortcuts_block' => 'b2000004-0000-4000-8000-000000000502',
    'ps_homepage_section_header_block:6' => 'b2000004-0000-4000-8000-000000000601',
    'ps_content_experts_accompagnement_block' => 'b2000004-0000-4000-8000-000000000602',
    'ps_homepage_section_header_block:7' => 'b2000004-0000-4000-8000-000000000701',
    'ps_news_news_block' => 'b2000004-0000-4000-8000-000000000702',
    'ps_homepage_section_footer_block:7' => 'b2000004-0000-4000-8000-000000000703',
    'ps_homepage_section_header_block:8' => 'b2000004-0000-4000-8000-000000000801',
    'ps_market_study_market_studies_block' => 'b2000004-0000-4000-8000-000000000802',
    'ps_homepage_section_footer_block:8' => 'b2000004-0000-4000-8000-000000000803',
    'ps_homepage_section_header_block:9' => 'b2000004-0000-4000-8000-000000000901',
    'ps_faq_faq_block' => 'b2000004-0000-4000-8000-000000000902',
    'ps_homepage_section_footer_block:9' => 'b2000004-0000-4000-8000-000000000903',
  ];

  /**
   * @var list<array<string, mixed>>
   */
  public const DEFINITIONS = [
    [
      'number' => 1,
      'band' => 'Hero',
      'maquette' => 'What are you looking for?',
      'layout' => 'layout_onecol',
      'plugin_id' => 'ps_homepage_search_hero_block',
      'shell' => FALSE,
    ],
    [
      'number' => 2,
      'band' => 'Services',
      'maquette' => 'BNP Paribas Real Estate assists you with your commercial real estate project',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_content_services_grid_block',
      'body_provider' => 'ps_content',
      'shell' => TRUE,
      'modifier' => 'services',
      'section_class' => '',
      'background' => 'white',
      'header_subtitle' => TRUE,
      'footer' => FALSE,
    ],
    [
      'number' => 3,
      'band' => 'Outils',
      'maquette' => 'The best way to approach your real estate project',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_content_outils_accordion_block',
      'body_provider' => 'ps_content',
      'shell' => TRUE,
      'modifier' => 'tools',
      'section_class' => '',
      'header_subtitle' => TRUE,
      'footer' => FALSE,
    ],
    [
      'number' => 4,
      'band' => 'Annonces',
      'maquette' => 'Real estate for sale or rent',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_offer_offers_carousel_block',
      'body_provider' => 'ps_offer',
      'shell' => TRUE,
      'modifier' => 'offers-carousel',
      'section_class' => 'ps-homepage-offers-carousel',
      'header_subtitle' => TRUE,
      'footer' => TRUE,
      'footer_url_key' => 'auto_search',
    ],
    [
      'number' => 5,
      'band' => 'Sélection recherches',
      'maquette' => "Notre sélection de recherches en immobilier d'entreprise",
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_search_search_shortcuts_block',
      'body_provider' => 'ps_search',
      'shell' => TRUE,
      'modifier' => 'shortcuts',
      'section_class' => 'ps-homepage-shortcuts',
      'header_subtitle' => TRUE,
      'footer' => FALSE,
    ],
    [
      'number' => 6,
      'band' => 'Experts',
      'maquette' => 'Nos experts vous accompagnent',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_content_experts_accompagnement_block',
      'body_provider' => 'ps_content',
      'shell' => TRUE,
      'modifier' => 'expert',
      'section_class' => 'ps-homepage-expert',
      'header_subtitle' => TRUE,
      'header_align' => 'center',
      'footer' => FALSE,
    ],
    [
      'number' => 7,
      'band' => 'Actualités',
      'maquette' => 'Commercial real estate news',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_news_news_block',
      'body_provider' => 'ps_news',
      'shell' => TRUE,
      'modifier' => 'news',
      'section_class' => 'ps-homepage-news',
      'header_subtitle' => TRUE,
      'footer' => TRUE,
      'footer_url_key' => 'auto_news',
    ],
    [
      'number' => 8,
      'band' => 'Études',
      'maquette' => 'Les études de marché BNP Paribas Real Estate',
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_market_study_market_studies_block',
      'body_provider' => 'ps_market_study',
      'shell' => TRUE,
      'modifier' => 'market-studies',
      'section_class' => 'ps-homepage-market-studies',
      'header_subtitle' => TRUE,
      'footer' => TRUE,
      'footer_url_key' => 'auto_studies',
    ],
    [
      'number' => 9,
      'band' => 'FAQ',
      'maquette' => "La FAQ de l'immobilier d'entreprise",
      'layout' => 'ps_homepage_section',
      'plugin_id' => 'ps_faq_faq_block',
      'body_provider' => 'ps_faq',
      'shell' => TRUE,
      'modifier' => 'faq',
      'section_class' => 'ps-homepage-faq',
      'header_subtitle' => FALSE,
      'footer' => TRUE,
      'footer_url_key' => 'see_more_url',
    ],
  ];

  public function __construct(
    private readonly BlockManagerInterface $blockManager,
    private readonly UuidInterface $uuid,
    private readonly HomepageBlockDefaultsLoader $defaultsLoader,
    private readonly NewsListPathResolver $newsListPathResolver,
    private readonly MarketStudyListPathResolver $marketStudyListPathResolver,
  ) {}

  /**
   * Builds one homepage section (§1–§9) for LB layouts.
   *
   * @param array<string, mixed>|null $legacyMonolithicConfig
   *   Legacy monolithic block configuration when migrating from layout_onecol.
   */
  public function buildHomepageSection(int $number, string $langcode, ?array $legacyMonolithicConfig = NULL): ?Section {
    $definition = $this->definitionForNumber($number);
    if ($definition === NULL) {
      return NULL;
    }

    return $this->buildSection($definition, $langcode, $legacyMonolithicConfig);
  }

  /**
   * Builds the full 9-section homepage layout (S-D shell §2–§9).
   *
   * @return list<\Drupal\layout_builder\Section>
   */
  public function buildHomepageLayout(string $langcode = 'en'): array {
    $sections = [];
    foreach (self::DEFINITIONS as $definition) {
      $section = $this->buildSection($definition, $langcode);
      if ($section instanceof Section) {
        $sections[] = $section;
      }
    }
    return $sections;
  }

  /**
   * @return list<array{section: \Drupal\layout_builder\Section, label: string}>
   */
  public function buildTemplateSections(string $langcode = 'en'): array {
    $templates = [];

    foreach (self::DEFINITIONS as $definition) {
      $section = $this->buildSection($definition, $langcode);
      if ($section === NULL) {
        continue;
      }

      $templates[] = [
        'section' => $section,
        'label' => $this->templateLabel($definition),
      ];
    }

    return $templates;
  }

  /**
   * @param array<string, mixed> $definition
   */
  public function templateLabel(array $definition): string {
    $number = (int) ($definition['number'] ?? 0);
    $band = (string) ($definition['band'] ?? '');
    return sprintf('Homepage SD §%d — %s', $number, $band);
  }

  /**
   * @param array<string, mixed> $definition
   * @param array<string, mixed>|null $legacyMonolithicConfig
   */
  private function buildSection(array $definition, string $langcode, ?array $legacyMonolithicConfig = NULL): ?Section {
    if (empty($definition['shell'])) {
      return $this->buildMonolithicSection($definition, $langcode, $legacyMonolithicConfig);
    }

    $layoutId = (string) ($definition['layout'] ?? 'ps_homepage_section');
    $layoutSettings = [
      'label' => (string) ($definition['band'] ?? ''),
      'background' => (string) ($definition['background'] ?? 'default'),
      'container' => 'container',
      'spacing' => 'lg',
      'modifier' => (string) ($definition['modifier'] ?? ''),
      'section_class' => (string) ($definition['section_class'] ?? ''),
    ];

    $section = new Section($layoutId, $layoutSettings);
    $pluginId = (string) ($definition['plugin_id'] ?? '');
    $number = (int) ($definition['number'] ?? 0);
    $defaults = $legacyMonolithicConfig ?? $this->defaultsLoader->forPlugin($pluginId, $langcode);

    $headerConfig = $this->headerConfiguration($defaults, $definition);
    if ($headerConfig !== []) {
      $section->appendComponent(new SectionComponent(
        $this->componentUuid('ps_homepage_section_header_block:' . $number),
        'header',
        $headerConfig,
      ));
    }

    $bodyDefaults = $legacyMonolithicConfig !== NULL
      ? $this->stripShellConfiguration($defaults)
      : $defaults;
    $bodyConfig = $this->bodyConfiguration(
      $pluginId,
      $bodyDefaults,
      (string) ($definition['body_provider'] ?? 'ps_homepage'),
    );
    $section->appendComponent(new SectionComponent(
      $this->componentUuid($pluginId),
      'body',
      $bodyConfig,
    ));

    $footerConfig = $this->footerConfiguration($defaults, $definition, $langcode);
    if ($footerConfig !== []) {
      $section->appendComponent(new SectionComponent(
        $this->componentUuid('ps_homepage_section_footer_block:' . $number),
        'footer',
        $footerConfig,
      ));
    }

    return $section;
  }

  /**
   * @param array<string, mixed> $definition
   * @param array<string, mixed>|null $legacyMonolithicConfig
   */
  private function buildMonolithicSection(array $definition, string $langcode, ?array $legacyMonolithicConfig = NULL): Section {
    $pluginId = (string) ($definition['plugin_id'] ?? '');
    $plugin = $this->blockManager->createInstance($pluginId, []);
    $configuration = $legacyMonolithicConfig ?? $this->defaultsLoader->forPlugin($pluginId, $langcode);
    $configuration += $plugin->defaultConfiguration();
    $configuration['id'] = $pluginId;
    $configuration['provider'] = 'ps_homepage';
    $configuration['label'] = '';
    $configuration['label_display'] = FALSE;

    $section = new Section('layout_onecol', ['label' => (string) ($definition['band'] ?? 'Hero')]);
    $section->appendComponent(new SectionComponent(
      $this->componentUuid($pluginId),
      'content',
      $configuration,
    ));

    return $section;
  }

  /**
   * @param array<string, mixed> $defaults
   * @param array<string, mixed> $definition
   *
   * @return array<string, mixed>
   */
  private function headerConfiguration(array $defaults, array $definition): array {
    $title = HomepageBlockConfiguration::string($defaults, 'title');
    if ($title === '') {
      return [];
    }

    $subtitle = !empty($definition['header_subtitle'])
      ? HomepageBlockConfiguration::string($defaults, 'subtitle')
      : '';

    return [
      'id' => 'ps_homepage_section_header_block',
      'provider' => 'ps_homepage',
      'label' => '',
      'label_display' => FALSE,
      'title' => $title,
      'subtitle' => $subtitle,
      'align' => (string) ($definition['header_align'] ?? 'center'),
      'accent' => 'bar',
    ];
  }

  /**
   * @param array<string, mixed> $defaults
   *
   * @return array<string, mixed>
   */
  private function bodyConfiguration(string $pluginId, array $defaults, string $provider = 'ps_homepage'): array {
    $plugin = $this->blockManager->createInstance($pluginId, []);
    $configuration = HomepageSectionDisplayMode::markBodyOnly($defaults);
    $configuration += $plugin->defaultConfiguration();
    $configuration['id'] = $pluginId;
    $configuration['provider'] = $provider;
    $configuration['label'] = '';
    $configuration['label_display'] = FALSE;
    return $configuration;
  }

  /**
   * @param array<string, mixed> $defaults
   * @param array<string, mixed> $definition
   *
   * @return array<string, mixed>
   */
  private function footerConfiguration(array $defaults, array $definition, string $langcode): array {
    if (empty($definition['footer'])) {
      return [];
    }

    $label = HomepageBlockConfiguration::string($defaults, 'see_more_label');
    if ($label === '') {
      return [];
    }

    $urlKey = (string) ($definition['footer_url_key'] ?? 'see_more_url');
    $url = match ($urlKey) {
      'auto_search' => '/find-property',
      'auto_news' => $this->newsListPathResolver->getPublicPath($langcode),
      'auto_studies' => $this->marketStudyListPathResolver->getPublicPath($langcode),
      default => HomepageBlockConfiguration::string($defaults, 'see_more_url'),
    };

    if ($url === '') {
      return [];
    }

    return [
      'id' => 'ps_homepage_section_footer_block',
      'provider' => 'ps_homepage',
      'label' => '',
      'label_display' => FALSE,
      'cta_label' => $label,
      'cta_url' => $url,
      'cta_style' => 'outline',
    ];
  }

  private function componentUuid(string $key): string {
    return self::COMPONENT_UUIDS[$key] ?? $this->uuid->generate();
  }

  /**
   * @return array<string, mixed>|null
   */
  private function definitionForNumber(int $number): ?array {
    foreach (self::DEFINITIONS as $definition) {
      if ((int) ($definition['number'] ?? 0) === $number) {
        return $definition;
      }
    }
    return NULL;
  }

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array<string, mixed>
   */
  private function stripShellConfiguration(array $configuration): array {
    foreach (['title', 'subtitle', 'see_more_label', 'see_more_url'] as $key) {
      unset($configuration[$key]);
    }
    return $configuration;
  }

}
