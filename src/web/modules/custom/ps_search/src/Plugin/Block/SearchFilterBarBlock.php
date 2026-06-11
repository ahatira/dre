<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Search\Filter\FilterBarBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * BNPPRE-style horizontal search filter bar with popins.
 */
#[Block(
  id: 'ps_search_filter_bar',
  admin_label: new TranslatableMarkup('Search filter bar'),
  category: new TranslatableMarkup('Property Search'),
)]
final class SearchFilterBarBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly FilterBarBuilder $filterBarBuilder,
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
      $container->get('ps_search.filter_bar_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return $this->filterBarBuilder->build();
  }

}
