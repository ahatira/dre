<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer detail location block (address + transport features).
 */
#[Block(
  id: 'ps_offer_detail_location',
  admin_label: new TranslatableMarkup('Offer detail location'),
  category: new TranslatableMarkup('Property Search'),
)]
final class OfferDetailLocationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RouteMatchInterface $routeMatch,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
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

    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section', 'ps-offer-section--location']],
      '#cache' => [
        'tags' => $node->getCacheTags(),
        'contexts' => ['route'],
      ],
    ];

    $build['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Location'),
      '#attributes' => ['class' => ['ps-offer-section__title']],
    ];

    $address = $this->viewField($view_builder, $node, 'field_address', 'address_plain');
    if ($address !== []) {
      $build['address'] = $address;
      $build['address']['#attributes']['class'][] = 'ps-offer-section--location__address';
    }

    $transport = $this->viewField($view_builder, $node, 'field_features', 'feature_default', [
      'show_label' => FALSE,
      'show_group' => TRUE,
      'format_style' => 'grouped',
      'hide_disabled_flags' => TRUE,
      'group_order' => 'acces_vehicules',
      'group_filter' => 'acces_vehicules',
    ]);
    if ($transport !== []) {
      $build['transport_label'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Transport :'),
        '#attributes' => ['class' => ['ps-offer-section--location__transport-label']],
      ];
      $build['transport'] = $transport;
      $build['transport']['#attributes']['class'][] = 'ps-offer-section--location__transport';
    }

    if (!isset($build['address']) && !isset($build['transport'])) {
      return [];
    }

    return $build;
  }

  /**
   * Builds a single field render array for the offer node.
   */
  private function viewField(
    object $view_builder,
    NodeInterface $node,
    string $field_name,
    string $formatter,
    array $settings = [],
    string $label = 'hidden',
  ): array {
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return [];
    }

    return $view_builder->viewField($node->get($field_name), [
      'type' => $formatter,
      'label' => $label,
      'settings' => $settings,
    ]);
  }

}
