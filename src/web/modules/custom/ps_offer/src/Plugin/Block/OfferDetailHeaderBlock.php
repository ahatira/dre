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
 * Offer detail header (reference, title, budget, location, meta).
 */
#[Block(
  id: 'ps_offer_detail_header',
  admin_label: new TranslatableMarkup('Offer detail header'),
  category: new TranslatableMarkup('Property Search'),
)]
final class OfferDetailHeaderBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      '#attributes' => ['class' => ['ps-offer-detail-header']],
      '#cache' => [
        'tags' => $node->getCacheTags(),
        'contexts' => ['route'],
      ],
    ];

    if ($node->hasField('field_reference') && !$node->get('field_reference')->isEmpty()) {
      $build['reference'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Reference : @ref', [
          '@ref' => $node->get('field_reference')->value,
        ]),
        '#attributes' => ['class' => ['ps-offer-detail-header__reference']],
      ];
    }

    $primary_title = $node->hasField('field_commercial_title')
      ? trim((string) ($node->get('field_commercial_title')->value ?? ''))
      : '';
    $secondary_title = trim((string) ($node->label() ?? ''));
    if ($secondary_title !== '' && strcasecmp($secondary_title, $primary_title) === 0) {
      $secondary_title = '';
    }

    $visible_title_parts = array_values(array_filter([$primary_title, $secondary_title]));
    if ($visible_title_parts !== []) {
      $build['seo_title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => implode(' — ', $visible_title_parts),
        '#attributes' => ['class' => ['visually-hidden']],
      ];
    }

    $heading = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__heading']],
    ];

    if ($primary_title !== '' && $secondary_title !== '') {
      $heading['primary'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $primary_title,
        '#attributes' => ['class' => ['ps-offer-detail-header__title-primary']],
      ];
    }

    $title_row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__title-row']],
    ];

    $headline = match (TRUE) {
      $secondary_title !== '' => $secondary_title,
      $primary_title !== '' => $primary_title,
      default => '',
    };
    if ($headline !== '') {
      $title_row['headline'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $headline,
        '#attributes' => ['class' => ['ps-offer-detail-header__title-secondary']],
      ];
    }

    $budget = $this->viewField($view_builder, $node, 'field_budget_value', 'ps_offer_budget_display');
    if ($budget !== []) {
      $title_row['budget'] = $budget;
    }

    if (isset($title_row['headline']) || isset($title_row['budget'])) {
      $heading['title_row'] = $title_row;
    }

    if (isset($heading['primary']) || isset($heading['title_row'])) {
      $build['heading'] = $heading;
    }

    $location = $this->viewField($view_builder, $node, 'field_address', 'ps_offer_location_summary');
    if ($location !== []) {
      $build['location'] = $location;
    }

    $meta = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__meta']],
    ];
    $has_meta = FALSE;

    $availability = $this->viewField($view_builder, $node, 'field_availability', 'basic_string', [], 'inline');
    if ($availability !== []) {
      $meta['availability'] = $availability;
      $has_meta = TRUE;
    }

    $mandate = $this->viewField($view_builder, $node, 'field_mandate_type', 'ps_dictionary_formatter', [
      'display_mode' => 'label',
    ], 'inline');
    if ($mandate !== []) {
      $meta['mandate'] = $mandate;
      $has_meta = TRUE;
    }

    if ($has_meta) {
      $build['meta'] = $meta;
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
