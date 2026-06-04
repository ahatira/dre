<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_theme\Utility\OfferCardPropsBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage featured offer cards (teaser grid).
 */
#[Block(
  id: 'ps_homepage_featured_offers_block',
  admin_label: new TranslatableMarkup('Featured offers (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class FeaturedOffersBlock extends BlockBase implements ContainerFactoryPluginInterface {

  private const LIMIT = 3;

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'offer')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('changed', 'DESC')
      ->range(0, self::LIMIT)
      ->execute();

    if ($nids === []) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['text-muted', 'mb-0']],
        '#value' => (string) $this->t('No featured properties yet.'),
      ];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $columns = [];
    foreach ($nodes as $node) {
      if (!$node instanceof NodeInterface) {
        continue;
      }
      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-md-6', 'col-lg-4']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:offer-card',
          '#props' => OfferCardPropsBuilder::build($node),
        ],
      ];
    }

    $grid = [
      '#type' => 'container',
      '#attributes' => ['class' => ['row', 'g-4']],
    ];
    foreach ($columns as $index => $column) {
      $grid[$index] = $column;
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-featured', 'container']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['ps-homepage-featured__title', 'h3', 'mb-4']],
        '#value' => HomepageContent::featuredTitle(),
      ],
      'grid' => $grid,
    ];
  }

}
