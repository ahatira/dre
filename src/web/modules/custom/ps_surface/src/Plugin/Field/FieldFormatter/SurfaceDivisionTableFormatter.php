<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_core\Service\OfferSectionHeadingBuilder;
use Drupal\ps_surface\Entity\SurfaceDivision;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders surface divisions as a data table.
 */
#[FieldFormatter(
  id: 'ps_surface_division_table',
  label: new TranslatableMarkup('Surface division table'),
  field_types: ['entity_reference'],
)]
final class SurfaceDivisionTableFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Asset types where surface divisions are not displayed on the offer page.
   */
  private const CAPACITY_DRIVEN_ASSET_TYPES = ['COW'];

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    mixed $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly OfferSectionHeadingBuilder $sectionHeadingBuilder,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_core.section_heading_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    if ($this->shouldHideForParentEntity($items->getEntity())) {
      return [];
    }

    $entities = $this->getEntitiesToView($items, $langcode);
    if ($entities === []) {
      return [];
    }

    $columns = [
      ['key' => 'lot', 'label' => (string) $this->t('Lot'), 'type' => 'text'],
      ['key' => 'floor', 'label' => (string) $this->t('Floor'), 'type' => 'text'],
      ['key' => 'nature', 'label' => (string) $this->t('Nature'), 'type' => 'text'],
      ['key' => 'surface', 'label' => (string) $this->t('Surface'), 'type' => 'number'],
      ['key' => 'availability', 'label' => (string) $this->t('Availability'), 'type' => 'text'],
    ];

    $rows = [];
    foreach ($entities as $entity) {
      if (!$entity instanceof SurfaceDivision) {
        continue;
      }

      $surface = $this->getSurfaceSortData($entity);
      $rows[] = [
        ['content' => $entity->getDivisionReference(), 'sort_value' => $entity->getDivisionReference()],
        ['content' => $entity->getFloorLabel(), 'sort_value' => $entity->getFloorLabel()],
        ['content' => $entity->getNatureLabel(), 'sort_value' => $entity->getNatureLabel()],
        ['content' => $surface['label'], 'sort_value' => (string) $surface['value']],
        ['content' => $this->formatAvailability($entity), 'sort_value' => $this->formatAvailability($entity)],
      ];
    }

    if ($rows === []) {
      return [];
    }

    $title = $this->sectionHeadingBuilder->buildTitle('surface_table');
    $title['#cache']['tags'] = array_merge(
      $title['#cache']['tags'] ?? [],
      $this->sectionHeadingBuilder->getCacheTags(),
    );

    return [
      0 => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-offer-section', 'ps-offer-section--surface-table'],
        ],
        '#attached' => [
          'library' => ['ps_surface/surface-division-table'],
        ],
        'title' => $title,
        'table_wrapper' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-surface-division-table__wrapper']],
          'table' => [
            '#theme' => 'ps_surface_division_table',
            '#columns' => $columns,
            '#rows' => $rows,
            '#default_sort' => ['column' => 'lot', 'direction' => 'asc'],
            '#table_id' => 'ps-surface-table',
          ],
        ],
      ],
    ];
  }

  /**
   * Whether the parent offer hides surface divisions (capacity-driven types).
   */
  private function shouldHideForParentEntity(EntityInterface $entity): bool {
    if (!$entity->hasField('field_asset_type') || $entity->get('field_asset_type')->isEmpty()) {
      return FALSE;
    }

    return in_array((string) $entity->get('field_asset_type')->value, self::CAPACITY_DRIVEN_ASSET_TYPES, TRUE);
  }

  /**
   * Returns display label and numeric sort value for the division surface.
   *
   * @return array{label: string, value: float}
   *   Surface display data.
   */
  private function getSurfaceSortData(SurfaceDivision $division): array {
    if (!$division->hasField('surfaces') || $division->get('surfaces')->isEmpty()) {
      return ['label' => '', 'value' => 0.0];
    }

    $fallback = ['label' => '', 'value' => 0.0];
    foreach ($division->get('surfaces') as $item) {
      $qualification = (string) ($item->qualification ?? '');
      $value = $item->value ?? NULL;
      if ($value === NULL || $value === '') {
        continue;
      }

      $numeric = (float) $value;
      $formatted = $this->formatSurfaceAmount($numeric, (string) ($item->unit_code ?? 'M2'));
      if ($qualification === 'DISPO' || $qualification === '') {
        return ['label' => $formatted, 'value' => $numeric];
      }
      if ($fallback['label'] === '' && ($qualification === 'TOTAL' || $qualification === 'ETREF')) {
        $fallback = ['label' => $formatted, 'value' => $numeric];
      }
    }

    return $fallback;
  }

  /**
   * Formats a surface amount for display (French grouping, m² or ha).
   */
  private function formatSurfaceAmount(float $value, string $unit_code): string {
    $unit = strtolower($unit_code) === 'ha' ? 'ha' : 'm²';
    $formatted = number_format($value, 1, ',', ' ');
    if (str_ends_with($formatted, ',0')) {
      $formatted = substr($formatted, 0, -2);
    }

    return $formatted . ' ' . $unit;
  }

  /**
   * Formats division availability for the table column.
   */
  private function formatAvailability(SurfaceDivision $division): string {
    if (!$division->get('availability_text')->isEmpty()) {
      return (string) $division->get('availability_text')->value;
    }

    if (!$division->get('division_status')->isEmpty()) {
      $value = (string) $division->get('division_status')->value;
      return match ($value) {
        'AVAILABLE' => (string) $this->t('Available'),
        'PARTIAL' => (string) $this->t('Partial'),
        'UNAVAILABLE' => (string) $this->t('Unavailable'),
        default => (string) $this->t('Unknown'),
      };
    }

    return '';
  }

}
