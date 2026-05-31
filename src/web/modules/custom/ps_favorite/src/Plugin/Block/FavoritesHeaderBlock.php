<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_favorite\Service\FavoriteLazyBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Block(
  id: 'ps_favorite_header_block',
  admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Favorites header block'),
  category: new \Drupal\Core\StringTranslation\TranslatableMarkup('Property Search'),
)]
final class FavoritesHeaderBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly FavoriteLazyBuilder $favoriteLazyBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_favorite.lazy_builder'),
    );
  }

  public function build(): array {
    return [
      '#theme' => 'ps_favorite_header_block',
      '#offcanvas_url' => '/favorites/offcanvas',
      '#dialog_options' => '{"width":420,"dialogClass":"ps-favorite-offcanvas"}',
      '#count' => $this->favoriteLazyBuilder->buildHeaderCount(),
      '#attached' => [
        'library' => ['ps_favorite/favorites'],
        'drupalSettings' => [
          'psFavorite' => [
            'countEndpoint' => '/favorites/count',
            'countRefreshMs' => 0,
          ],
        ],
      ],
    ];
  }

}
