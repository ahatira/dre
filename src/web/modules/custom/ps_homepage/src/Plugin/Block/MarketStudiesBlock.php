<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageLocalizedFieldResolver;
use Drupal\ps_homepage\Utility\HomepageMediaResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage market studies section (§8).
 */
#[Block(
  id: 'ps_homepage_market_studies_block',
  admin_label: new TranslatableMarkup('Market studies (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class MarketStudiesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HomepageMediaResolver $mediaResolver,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly MarketStudiesBlockFormBuilder $formBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_homepage.media_resolver'),
      $container->get('extension.path.resolver'),
      new MarketStudiesBlockFormBuilder(
        $container->get('date.formatter'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title_en' => 'Market studies',
      'title_fr' => 'Études de marché',
      'subtitle_en' => 'Insights to guide your real estate decisions',
      'subtitle_fr' => 'Des analyses pour éclairer vos décisions immobilières',
      'see_more_label_en' => 'View all studies',
      'see_more_label_fr' => 'Voir toutes les études',
      'see_more_url_en' => '/research',
      'see_more_url_fr' => '/recherche',
      'items' => MarketStudiesBlockFormBuilder::defaultItems(),
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    return $form + $this->formBuilder->buildForm($this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->formBuilder->submitForm($this->configuration, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $langcode = HomepageContent::langcode();
    $heading = HomepageLocalizedFieldResolver::resolveHeading($this->configuration, $langcode);
    $footer = HomepageLocalizedFieldResolver::resolveFooterCta($this->configuration, $langcode);

    $columns = [];
    foreach ($this->configuration['items'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title_' . $langcode] ?? $item['title_en'] ?? ''));
      $imageUrl = $this->mediaResolver->resolveUrl($item['image'] ?? NULL)
        ?? $this->defaultThemeImageUrl();
      $url = trim((string) ($item['url_' . $langcode] ?? $item['url_en'] ?? ''));

      if ($title === '' || $url === '') {
        continue;
      }

      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-12', 'col-lg-6']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:market-study-card',
          '#props' => [
            'image' => $imageUrl,
            'image_alt' => (string) ($item['image_alt'] ?? $title),
            'category' => trim((string) ($item['category_' . $langcode] ?? $item['category_en'] ?? '')),
            'title' => $title,
            'date' => $this->formBuilder->formatDate((string) ($item['date'] ?? ''), $langcode),
            'url' => Url::fromUserInput($url)->toString(),
          ],
        ],
      ];
    }

    if ($columns === []) {
      return ['#markup' => ''];
    }

    $footerUrl = $footer['url'] !== '' ? Url::fromUserInput($footer['url'])->toString() : '';

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-market-studies', 'container', 'py-5']],
      'heading' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-heading',
        '#props' => $heading,
      ],
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'g-4']],
      ] + $columns,
      'footer' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-footer-cta',
        '#props' => [
          'label' => $footer['label'],
          'url' => $footerUrl,
        ],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

  private function defaultThemeImageUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/assets/images/hero/hero-profile.png')->toString();
  }

}
