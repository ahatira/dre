<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_search\Search\Hero\HeroSearchBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Stellar homepage search hero (SDC + transaction toggle).
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return $this->heroSearchBuilder->build([
      'title' => HomepageContent::heroTitle(),
      'subtitle' => HomepageContent::heroSubtitle(),
      'background_image' => HomepageContent::heroBackgroundUrl(),
      'background_alt' => '',
    ]);
  }

}
