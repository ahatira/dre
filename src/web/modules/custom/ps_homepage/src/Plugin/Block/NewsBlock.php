<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_homepage\Service\NewsListPathResolver;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageLocalizedFieldResolver;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage news section (§7) — heading + embedded view + footer CTA.
 */
#[Block(
  id: 'ps_homepage_news_block',
  admin_label: new TranslatableMarkup('News (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class NewsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly NewsListPathResolver $newsListPathResolver,
    private readonly NewsBlockFormBuilder $formBuilder,
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
      $container->get('ps_homepage.news_list_path_resolver'),
      new NewsBlockFormBuilder(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title_en' => 'News & insights',
      'title_fr' => 'Actualités & analyses',
      'subtitle_en' => 'Market trends and expert perspectives',
      'subtitle_fr' => 'Tendances marché et regards d’experts',
      'see_more_label_en' => 'View all news',
      'see_more_label_fr' => 'Voir toutes les actualités',
      'items_count' => 3,
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
    $footerLabel = HomepageLocalizedFieldResolver::resolve($this->configuration, 'see_more_label', $langcode);
    $footerUrl = $this->newsListPathResolver->getPublicPath($langcode);
    $itemsCount = $this->normalizeItemsCount((int) ($this->configuration['items_count'] ?? 3));

    $view = Views::getView('ps_homepage_news');
    if ($view === NULL) {
      return ['#markup' => ''];
    }

    $view->setDisplay('homepage_news_teaser');
    $view->setItemsPerPage($itemsCount);
    $view->preExecute();
    $view->execute();
    if ($view->result === []) {
      return ['#markup' => ''];
    }

    $viewRender = $view->buildRenderable('homepage_news_teaser');
    if ($viewRender === []) {
      return ['#markup' => ''];
    }

    $viewRender['#attributes']['class'][] = 'ps-homepage-news__view';

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'ps-homepage-news',
          'container',
          'py-5',
          'ps-homepage-news--cols-' . $itemsCount,
        ],
        'data-cols' => (string) $itemsCount,
      ],
      '#attached' => [
        'library' => ['ps_homepage/homepage_news'],
      ],
      'heading' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-heading',
        '#props' => $heading,
      ],
      'view' => $viewRender,
      'footer' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-footer-cta',
        '#props' => [
          'label' => $footerLabel,
          'url' => $footerUrl,
        ],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:views.view.ps_homepage_news', 'node_list:article'],
      ],
    ];
  }

  private function normalizeItemsCount(int $count): int {
    return match (TRUE) {
      $count >= 6 => 6,
      $count >= 4 => 4,
      default => 3,
    };
  }

}
