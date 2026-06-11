<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Search\Header\HeaderSearchPanelBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Property search quick panel for the site header actions region.
 */
#[Block(
  id: 'ps_search_header_search',
  admin_label: new TranslatableMarkup('Header search (property)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class HeaderSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HeaderSearchPanelBuilder $panelBuilder,
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
      $container->get('ps_search.header_search_panel_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return $this->panelBuilder->buildPanelContent();
  }

}
