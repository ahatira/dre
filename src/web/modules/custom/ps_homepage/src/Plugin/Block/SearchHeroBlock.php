<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Service\HomepageOfferCountProvider;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_content\Service\ContentMediaResolver;
use Drupal\ps_homepage\Utility\HomepageSearchHeroEditorial;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\Search\Hero\HeroSearchBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage search hero — BNPPRE 2-column layout with fully configurable copy.
 */
#[Block(
  id: 'ps_homepage_search_hero_block',
  admin_label: new TranslatableMarkup('Search hero (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class SearchHeroBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HeroSearchBuilder $heroSearchBuilder,
    private readonly ContentMediaResolver $mediaResolver,
    private readonly HomepageOfferCountProvider $offerCountProvider,
    private readonly SearchHeroBlockFormBuilder $formBuilder,
    private readonly SearchPathResolverInterface $searchPathResolver,
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
      $container->get('ps_search.hero_search_builder'),
      $container->get('ps_homepage.media_resolver'),
      $container->get('ps_homepage.offer_count_provider'),
      new SearchHeroBlockFormBuilder(),
      $container->get('ps_search.search_path_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return HomepageSearchHeroEditorial::defaultBlockConfiguration() + parent::defaultConfiguration();
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
    $offerCount = NULL;
    if (!empty($this->configuration['promo_offers_use_dynamic'])) {
      $offerCount = $this->offerCountProvider->countPublishedOffers();
    }

    $editorial = HomepageSearchHeroEditorial::resolve($this->configuration, $offerCount);
    $defaultBackground = $this->heroSearchBuilder->defaultHeroBackgroundUrl();
    $backgroundMedia = $this->mediaResolver->resolve($this->configuration['background_image'] ?? NULL, $langcode);
    $promoMedia = $this->mediaResolver->resolve($this->configuration['promo_background_image'] ?? NULL, $langcode);

    return $this->heroSearchBuilder->build([
      'title' => $editorial['title'],
      'background_image' => $backgroundMedia->url ?? $defaultBackground,
      'background_alt' => $backgroundMedia->alt !== '' ? $backgroundMedia->alt : $editorial['title'],
      'promo_title' => $editorial['promo_title'],
      'promo_offers_line' => $editorial['promo_offers_line'],
      'promo_description' => $editorial['promo_description'],
      'promo_cta_label' => $editorial['promo_cta_label'],
      'promo_cta_url' => $this->searchPathResolver->resolveStoredPublicSearchPath(
        $editorial['promo_cta_url'] ?: $this->searchPathResolver->getPublicPath($langcode),
        $langcode,
      ),
      'promo_background_image' => $promoMedia->url ?? ($backgroundMedia->url ?? $defaultBackground),
      'promo_background_alt' => $promoMedia->alt !== '' ? $promoMedia->alt : $editorial['promo_title'],
      'labels' => $editorial + [
        'delegate_url' => Url::fromUserInput($editorial['delegate_url'] ?: '/contact')->toString(),
      ],
    ]);
  }

}
