<?php

declare(strict_types=1);

namespace Drupal\ps_offer;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_offer\Entity\OfferReferenceSegmentInterface;

/**
 * Draggable list builder for Offer Reference Segment entities.
 */
final class OfferReferenceSegmentListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_reference_segment_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header = [];
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['segment_type'] = $this->t('Type');
    $header['source_field'] = $this->t('Source field');
    $header['length'] = $this->t('Length');
    $header['enabled'] = $this->t('Enabled');
    // 'weight' column is appended automatically by DraggableListBuilderTrait.
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_offer\Entity\OfferReferenceSegmentInterface $entity */
    $row = [];
    // 'label' MUST remain a plain string: DraggableListBuilderTrait::buildForm()
    // always wraps it as ['#plain_text' => $row['label']]. Pre-wrapping it here
    // would produce ['#plain_text' => ['#plain_text' => ...]] → PHP 8 TypeError
    // on Html::escape(). See core filter/language/search ListBuilder examples.
    $row['label'] = $entity->label();
    // All other custom columns must be render arrays (FormBuilder processes
    // every child of the row as a render element).
    $row['id'] = ['#plain_text' => $entity->id()];
    $row['segment_type'] = ['#plain_text' => $entity->getSegmentType()];
    $row['source_field'] = ['#plain_text' => $entity->getSourceField()];
    $row['length'] = ['#plain_text' => (string) $entity->getLength()];
    $row['enabled'] = ['#plain_text' => $entity->isEnabled() ? (string) $this->t('Yes') : (string) $this->t('No')];
    // 'weight' drag widget is appended automatically by DraggableListBuilderTrait.
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   *
   * Load all segments ordered by weight for drag-and-drop reordering.
   */
  public function load(): array {
    $entities = parent::load();
    uasort($entities, static function (EntityInterface $a, EntityInterface $b): int {
      $weight_a = $a instanceof OfferReferenceSegmentInterface ? $a->getWeight() : 0;
      $weight_b = $b instanceof OfferReferenceSegmentInterface ? $b->getWeight() : 0;
      return $weight_a <=> $weight_b;
    });
    return $entities;
  }

}
