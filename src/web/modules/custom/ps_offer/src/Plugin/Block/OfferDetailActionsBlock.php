<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer detail primary actions (surface table anchor + brochure).
 */
#[Block(
  id: 'ps_offer_detail_actions',
  admin_label: new TranslatableMarkup('Offer detail actions'),
  category: new TranslatableMarkup('Property Search'),
)]
final class OfferDetailActionsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RouteMatchInterface $routeMatch,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $node = $this->routeMatch->getParameter('node');
    if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
      return [];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-actions']],
      'surface_link' => [
        '#type' => 'link',
        '#title' => $this->t('Access to the surface area table'),
        '#url' => Url::fromUserInput('#ps-surface-table'),
        '#attributes' => ['class' => ['btn', 'btn-outline-primary', 'ps-offer-actions__surface']],
      ],
    ];

    if ($node->hasField('field_media_document') && !$node->get('field_media_document')->isEmpty()) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      $document_render = $view_builder->viewField($node->get('field_media_document'), [
        'type' => 'ps_media_documents_formatter',
        'label' => 'hidden',
        'settings' => [
          'hide_if_empty' => TRUE,
          'show_titles' => FALSE,
          'link_text' => (string) $this->t('Download the brochure'),
        ],
      ]);
      $build['brochure'] = $document_render;
      $build['brochure']['#attributes']['class'][] = 'ps-offer-actions__brochure';
    }

    $build['#cache']['tags'] = $node->getCacheTags();
    $build['#cache']['contexts'] = ['route'];

    return $build;
  }

}
