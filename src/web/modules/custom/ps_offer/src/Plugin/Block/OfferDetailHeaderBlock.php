<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_offer\Service\OfferDetailActionsBuilder;
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
    private readonly DictionaryResolver $dictionaryResolver,
    private readonly OfferDetailActionsBuilder $actionsBuilder,
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
      $container->get('ps_dictionary.resolver'),
      $container->get('ps_offer.detail_actions_builder'),
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

    $zone = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__zone']],
    ];

    if ($node->hasField('field_reference') && !$node->get('field_reference')->isEmpty()) {
      $zone['reference'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Reference : @ref', [
          '@ref' => $node->get('field_reference')->value,
        ]),
        '#attributes' => ['class' => ['ps-offer-detail-header__reference']],
      ];
    }

    $body = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__body']],
    ];
    $summary = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__summary']],
    ];

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
        '#weight' => 100,
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

    $budget = $this->viewField(
      $view_builder,
      $node,
      'field_budget_value',
      'ps_offer_budget_display',
      allow_empty: TRUE,
    );
    if ($budget !== []) {
      $title_row['budget'] = $budget;
    }

    if (isset($title_row['headline']) || isset($title_row['budget'])) {
      $heading['title_row'] = $title_row;
    }

    if (isset($heading['primary']) || isset($heading['title_row'])) {
      $summary['heading'] = $heading;
    }

    $location = $this->viewField($view_builder, $node, 'field_address', 'ps_offer_location_summary');
    if ($location !== []) {
      $summary['location'] = $location;
    }

    if (count($summary) > 2) {
      $body['summary'] = $summary;
    }

    $meta = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-detail-header__meta']],
    ];
    $has_meta = FALSE;

    if ($node->hasField('field_availability') && !$node->get('field_availability')->isEmpty()) {
      $meta['availability'] = $this->buildMetaLine(
        (string) $this->t('Available :'),
        trim((string) $node->get('field_availability')->value),
      );
      $has_meta = TRUE;
    }

    if ($node->hasField('field_mandate_type') && !$node->get('field_mandate_type')->isEmpty()) {
      $mandate_code = (string) $node->get('field_mandate_type')->value;
      $mandate_label = $this->dictionaryResolver->resolveLabel('mandate_type', $mandate_code) ?: $mandate_code;
      $meta['mandate'] = $this->buildMetaLine(
        (string) $this->t('Type of mandate :'),
        $mandate_label,
      );
      $has_meta = TRUE;
    }

    if ($has_meta) {
      $body['meta'] = $meta;
    }

    $actions = $this->actionsBuilder->build($node);
    if ($actions !== []) {
      $body['actions'] = $actions;
    }

    if (count($body) > 1) {
      $zone['body'] = $body;
    }

    if (isset($zone['reference']) || isset($zone['body'])) {
      $build['zone'] = $zone;
    }

    return $build;
  }

  /**
   * Builds a meta line with a bold value (Figma Frame 297).
   */
  private function buildMetaLine(string $label, string $value): array {
    return [
      '#markup' => '<p class="ps-offer-detail-header__meta-item">' . Html::escape($label) . ' <strong>' . Html::escape($value) . '</strong></p>',
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
    bool $allow_empty = FALSE,
  ): array {
    if (!$node->hasField($field_name)) {
      return [];
    }

    if (!$allow_empty && $node->get($field_name)->isEmpty()) {
      return [];
    }

    return $view_builder->viewField($node->get($field_name), [
      'type' => $formatter,
      'label' => $label,
      'settings' => $settings,
    ]);
  }

}
