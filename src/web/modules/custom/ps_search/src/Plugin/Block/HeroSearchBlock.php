<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Search\Hero\HeroSearchBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage search hero with transaction toggle and location entry.
 */
#[Block(
  id: 'ps_search_hero_search',
  admin_label: new TranslatableMarkup('Search hero (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class HeroSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      'title' => (string) $this->t('Find your next property'),
      'subtitle' => (string) $this->t('Offices, retail and logistics across France.'),
      'background_image' => '',
      'background_alt' => '',
    ]);
  }

}
