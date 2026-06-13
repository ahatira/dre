<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\OfferContextResolverInterface;

/**
 * Builds offer detail CTA actions (surface table + brochure).
 */
final class OfferDetailActionsBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ?OfferContextResolverInterface $contextResolver = NULL,
  ) {}

  /**
   * Builds the actions render array for an offer node.
   */
  public function build(NodeInterface $node): array {
    if ($node->bundle() !== 'offer') {
      return [];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-actions']],
      '#cache' => [
        'tags' => $node->getCacheTags(),
        'contexts' => ['route'],
      ],
    ];

    if ($this->hasSurfaceTable($node)) {
      $build['surface_link'] = [
        '#type' => 'link',
        '#title' => $this->t('Access to the surface area table'),
        '#url' => Url::fromUserInput('#ps-surface-table'),
        '#attributes' => ['class' => ['ps-offer-actions__btn', 'ps-offer-actions__btn--secondary', 'ps-offer-actions__surface']],
      ];
    }

    if ($node->hasField('field_media_document') && !$node->get('field_media_document')->isEmpty()) {
      $view_builder = $this->entityTypeManager->getViewBuilder('node');
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

    if (!isset($build['surface_link']) && !isset($build['brochure'])) {
      return [];
    }

    return $build;
  }

  /**
   * Whether the offer exposes a surface division table.
   */
  private function hasSurfaceTable(NodeInterface $node): bool {
    if (!$node->hasField('field_divisions') || $node->get('field_divisions')->isEmpty()) {
      return FALSE;
    }

    if ($this->contextResolver !== NULL && $this->contextResolver->isCapacityDriven($node)) {
      return FALSE;
    }

    return TRUE;
  }

}
