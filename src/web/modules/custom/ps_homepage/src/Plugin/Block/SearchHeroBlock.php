<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageSearchHeroEditorial;
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
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly SearchHeroBlockFormBuilder $formBuilder,
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
      $container->get('file_url_generator'),
      new SearchHeroBlockFormBuilder(),
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
    $editorial = HomepageSearchHeroEditorial::resolve($this->configuration, $langcode);

    $delegateUrl = Url::fromUserInput($editorial['delegate_url'] ?: '/contact')->toString();

    return $this->heroSearchBuilder->build([
      'title' => $editorial['title'],
      'background_image' => $this->resolveBackgroundUrl('background_image', HomepageContent::heroBackgroundUrl()),
      'background_alt' => $editorial['background_alt'],
      'promo_title' => $editorial['promo_title'],
      'promo_offers_line' => $editorial['promo_offers_line'],
      'promo_description' => $editorial['promo_description'],
      'promo_cta_label' => $editorial['promo_cta_label'],
      'promo_cta_url' => $editorial['promo_cta_url'] ?: '/find-property',
      'promo_background_image' => $this->resolveBackgroundUrl('promo_background_image', HomepageContent::heroPromoBackgroundUrl()),
      'promo_background_alt' => $editorial['promo_background_alt'],
      'labels' => $editorial + [
        'delegate_url' => $delegateUrl,
      ],
      'delegate_url' => $delegateUrl,
    ]);
  }

  /**
   * Resolves a managed file URL or falls back to the demo default.
   */
  private function resolveBackgroundUrl(string $configKey, string $fallback): string {
    $fid = (int) ($this->configuration[$configKey] ?? 0);
    if ($fid <= 0) {
      return $fallback;
    }

    $file = File::load($fid);
    if ($file === NULL) {
      return $fallback;
    }

    return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
  }

}
