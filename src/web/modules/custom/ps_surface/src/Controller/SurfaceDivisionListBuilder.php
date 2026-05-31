<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the admin list for ps_surface_division entities.
 *
 * Columns: Reference · Label · Parent offer · Surfaces · Status
 */
final class SurfaceDivisionListBuilder extends EntityListBuilder {

  /**
   * The renderer service.
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    $instance = parent::createInstance($container, $entity_type);
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'reference' => $this->t('Reference'),
      'label' => $this->t('Label'),
      'surfaces' => $this->t('Surfaces'),
      'offers' => $this->t('Linked offers'),
      'status' => $this->t('Status'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_surface\Entity\SurfaceDivisionInterface $entity */
    return [
      'reference' => Markup::create('<strong>' . htmlspecialchars($entity->getDivisionReference() ?? '', ENT_QUOTES, 'UTF-8') . '</strong>'),
      'label' => $entity->label() ?? '',
      'surfaces' => $this->buildSurfacesSummary($entity),
      'offers' => $this->buildOfferLinks($entity),
      'status' => $this->buildStatusLabel($entity),
    ] + parent::buildRow($entity);
  }

  /**
   * Finds and renders links to all offers referencing this division.
   */
  private function buildOfferLinks(EntityInterface $entity): Markup {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'offer')
      ->condition('field_divisions', $entity->id())
      ->accessCheck(FALSE)
      ->range(0, 10);

    $offer_ids = $query->execute();

    if (empty($offer_ids)) {
      return Markup::create('—');
    }

    $offers = $storage->loadMultiple($offer_ids);
    $links = [];
    foreach ($offers as $offer) {
      $links[] = $offer->toLink($offer->label())->toRenderable();
    }

    $render_array = [
      '#theme' => 'item_list',
      '#items' => $links,
      '#attributes' => ['class' => ['compact-list']],
    ];

    return Markup::create($this->renderer->render($render_array));
  }


  /**
   * Renders a compact TOTAL / DISPO / ETREF summary from the surfaces field.
   */
  private function buildSurfacesSummary(EntityInterface $entity): Markup {
    if (!$entity->hasField('surfaces')) {
      return Markup::create('—');
    }

    $by_qual = [];
    foreach ($entity->get('surfaces')->getValue() as $row) {
      $qual = $row['qualification'] ?? '';
      $val = $row['value'] ?? NULL;
      if ($qual !== '' && $val !== NULL) {
        $unit = strtolower($row['unit_code'] ?? 'm2');
        $unit_label = $unit === 'ha' ? 'ha' : 'm²';
        $by_qual[$qual] = [
          'value' => number_format((float) $val, 2),
          'unit' => $unit_label,
        ];
      }
    }

    if (empty($by_qual)) {
      return Markup::create('—');
    }

    $parts = [];
    $colors = [
      'TOTAL' => '#0074bd',
      'DISPO' => '#28a745',
      'ETREF' => '#ffc107',
    ];

    foreach (['TOTAL', 'DISPO', 'ETREF'] as $qual) {
      if (isset($by_qual[$qual])) {
        $color = $colors[$qual] ?? '#666';
        $parts[] = sprintf(
          '<span style="color: %s; font-weight: 600;">%s:</span> %s&nbsp;%s',
          htmlspecialchars($color, ENT_QUOTES, 'UTF-8'),
          htmlspecialchars($qual, ENT_QUOTES, 'UTF-8'),
          htmlspecialchars($by_qual[$qual]['value'], ENT_QUOTES, 'UTF-8'),
          htmlspecialchars($by_qual[$qual]['unit'], ENT_QUOTES, 'UTF-8')
        );
      }
    }

    return Markup::create(implode(' <span style="color: #ccc;">·</span> ', $parts));
  }

  /**
   * Returns the human-readable status label for a division.
   */
  private function buildStatusLabel(EntityInterface $entity): Markup {
    if (!$entity->hasField('division_status')) {
      return Markup::create('—');
    }

    $value = (string) ($entity->get('division_status')->value ?? '');
    $labels = [
      'AVAILABLE' => (string) $this->t('Available'),
      'PARTIAL' => (string) $this->t('Partial'),
      'UNAVAILABLE' => (string) $this->t('Unavailable'),
      'UNKNOWN' => (string) $this->t('Unknown'),
    ];

    $colors = [
      'AVAILABLE' => '#28a745',
      'PARTIAL' => '#ffc107',
      'UNAVAILABLE' => '#dc3545',
      'UNKNOWN' => '#6c757d',
    ];

    $label = $labels[$value] ?? $value;
    $color = $colors[$value] ?? '#6c757d';

    return Markup::create(sprintf(
      '<span style="display: inline-block; padding: 0.25rem 0.75rem; background: %s; color: white; border-radius: 4px; font-size: 0.875rem; font-weight: 500;">%s</span>',
      htmlspecialchars($color, ENT_QUOTES, 'UTF-8'),
      htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
    ));
  }

}
