<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Service\HomepageOfferTeaserBuilder;
use Drupal\ps_homepage\Service\HomepageSectionBuilder;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_search\Service\SearchPathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage offers carousel section (§4).
 */
#[Block(
  id: 'ps_homepage_offers_carousel_block',
  admin_label: new TranslatableMarkup('Offers carousel (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class OffersCarouselBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageOfferTeaserBuilder $offerTeaserBuilder,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly HomepageSectionBuilder $sectionBuilder,
    private readonly OffersCarouselBlockFormBuilder $formBuilder,
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
      $container->get('entity_type.manager'),
      $container->get('ps_homepage.offer_teaser_builder'),
      $container->get('ps_search.search_path_resolver'),
      $container->get('ps_homepage.section_builder'),
      new OffersCarouselBlockFormBuilder(
        $container->get('entity_type.manager'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'max_visible' => 4,
      'show_favorite' => TRUE,
      'show_compare' => TRUE,
      'autoplay' => FALSE,
      'offers' => [],
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
    $heading = HomepageBlockConfiguration::heading($this->configuration);
    $footerLabel = HomepageBlockConfiguration::string($this->configuration, 'see_more_label');
    $footerUrl = Url::fromUserInput(
      $this->searchPathResolver->getPublicPath($langcode),
    )->toString();

    $nids = [];
    foreach ($this->configuration['offers'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $nid = (int) ($item['nid'] ?? 0);
      if ($nid > 0) {
        $nids[] = $nid;
      }
    }

    if ($nids === []) {
      $nids = array_values($this->entityTypeManager->getStorage('node')->getQuery()
        ->accessCheck(TRUE)
        ->condition('type', 'offer')
        ->condition('status', NodeInterface::PUBLISHED)
        ->sort('changed', 'DESC')
        ->range(0, 6)
        ->execute());
    }

    if ($nids === []) {
      return ['#markup' => ''];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $maxVisible = max(3, min(6, (int) ($this->configuration['max_visible'] ?? 4)));
    $cardOptions = [
      'show_favorite' => !empty($this->configuration['show_favorite']),
      'show_compare' => !empty($this->configuration['show_compare']),
    ];

    $items = [];
    $cacheTags = ['config:block.block'];
    foreach ($nids as $nid) {
      $node = $nodes[$nid] ?? NULL;
      if (!$node instanceof NodeInterface || !$node->isPublished()) {
        continue;
      }
      if ($node->hasTranslation($langcode)) {
        $node = $node->getTranslation($langcode);
      }

      $items[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-offers-carousel__item']],
        'card' => $this->offerTeaserBuilder->build($node, $cardOptions),
      ];
      $cacheTags = array_merge($cacheTags, $node->getCacheTags());
    }

    if ($items === []) {
      return ['#markup' => ''];
    }

    return $this->sectionBuilder->build([
      'modifier' => 'offers-carousel',
      'section_class' => 'ps-homepage-offers-carousel ps-homepage-offers-carousel--visible-' . $maxVisible,
      'header' => $heading,
      'body' => [
        'viewport' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-offers-carousel__viewport']],
          'prev' => [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#value' => '‹',
            '#attributes' => [
              'type' => 'button',
              'class' => ['ps-homepage-offers-carousel__control', 'ps-homepage-offers-carousel__control--prev'],
              'data-carousel-prev' => 'true',
              'aria-label' => (string) $this->t('Previous offers'),
            ],
          ],
          'track' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-homepage-offers-carousel__track']],
          ] + $items,
          'next' => [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#value' => '›',
            '#attributes' => [
              'type' => 'button',
              'class' => ['ps-homepage-offers-carousel__control', 'ps-homepage-offers-carousel__control--next'],
              'data-carousel-next' => 'true',
              'aria-label' => (string) $this->t('Next offers'),
            ],
          ],
        ],
      ],
      'footer' => [
        'cta_label' => $footerLabel,
        'cta_url' => $footerUrl,
      ],
      'attached' => [
        'library' => ['ps_homepage/homepage_offers_carousel'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique($cacheTags)),
      ],
    ]);
  }

}
