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
use Drupal\ps_core\Service\OfferSectionHeadingBuilder;
use Drupal\ps_core\Service\OfferSectionRegistry;
use Drupal\ps_offer\Service\OfferMapLocationBuilder;
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
    private readonly OfferSectionHeadingBuilder $sectionHeadingBuilder,
    private readonly OfferSectionRegistry $sectionRegistry,
    private readonly OfferMapLocationBuilder $mapLocationBuilder,
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
      $container->get('ps_core.section_heading_builder'),
      $container->get('ps_core.section_registry'),
      $container->get('ps_offer.map_location_builder'),
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
      '#attributes' => ['class' => ['ps-offer-section', 'ps-offer-section--location']],
      '#cache' => [
        'tags' => array_merge(
          $node->getCacheTags(),
          $this->sectionRegistry->getCacheTags(),
        ),
        'contexts' => ['route'],
      ],
    ];

    $build['title'] = $this->sectionHeadingBuilder->buildTitle('location');
    $build['title']['#cache']['tags'] = array_merge(
      $build['title']['#cache']['tags'] ?? [],
      $this->sectionHeadingBuilder->getCacheTags(),
    );

    $content = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section--location__content']],
    ];

    $address = $this->buildAddressLine($node);
    if ($address !== []) {
      $content['address'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-offer-section--location__address']],
        'line' => $address,
      ];
    }

    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $transport_group = $this->sectionRegistry->getLocationTransportGroup();
    $transport = $this->viewField($view_builder, $node, 'field_features', 'feature_default', [
      'show_label' => TRUE,
      'show_group' => FALSE,
      'format_style' => 'default',
      'hide_disabled_flags' => TRUE,
      'group_filter' => $transport_group,
    ]);
    if ($transport !== []) {
      $content['transport'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-offer-section--location__transport']],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Transport :'),
          '#attributes' => ['class' => ['ps-offer-section--location__transport-label']],
        ],
        'items' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-offer-section--location__transport-items']],
          'field' => $transport,
        ],
      ];
    }

    if (!isset($content['address']) && !isset($content['transport'])) {
      return [];
    }

    $build['content'] = $content;

    return $build;
  }

  /**
   * Builds a single-line address for the location block.
   */
  private function buildAddressLine(NodeInterface $node): array {
    $text = $this->mapLocationBuilder->buildLocationLine($node);
    if ($text === '') {
      return [];
    }

    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $text,
      '#attributes' => ['class' => ['ps-offer-section--location__address-line']],
    ];
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
