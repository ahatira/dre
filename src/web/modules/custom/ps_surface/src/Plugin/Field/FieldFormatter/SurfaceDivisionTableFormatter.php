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

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    if ($this->shouldHideForParentEntity($items->getEntity())) {
      return [];
    }

    $entities = $this->getEntitiesToView($items, $langcode);
    if ($entities === []) {
      return [];
    }

    $header = [
      $this->t('Lot'),
      $this->t('Floor / nature'),
      $this->t('Surface'),
      $this->t('Availability'),
    ];

    $rows = [];
    foreach ($entities as $entity) {
      if (!$entity instanceof SurfaceDivision) {
        continue;
      }
      $rows[] = [
        $entity->getDivisionReference(),
        $entity->label(),
        $this->formatSurfaceValue($entity),
        $this->formatAvailability($entity),
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
        'title' => $title,
        'table_wrapper' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-surface-division-table__wrapper']],
          'table' => [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
              'class' => ['ps-surface-division-table', 'table'],
              'id' => 'ps-surface-table',
            ],
          ],
        ],
      ],
    ];
  }

  private function shouldHideForParentEntity(EntityInterface $entity): bool {
    if (!$entity->hasField('field_asset_type') || $entity->get('field_asset_type')->isEmpty()) {
      return FALSE;
    }

    return in_array((string) $entity->get('field_asset_type')->value, self::CAPACITY_DRIVEN_ASSET_TYPES, TRUE);
  }

  private function formatSurfaceValue(SurfaceDivision $division): string {
    if (!$division->hasField('surfaces') || $division->get('surfaces')->isEmpty()) {
      return '';
    }

    $fallback = '';
    foreach ($division->get('surfaces') as $item) {
      $qualification = (string) ($item->qualification ?? '');
      $value = $item->value ?? NULL;
      if ($value === NULL || $value === '') {
        continue;
      }

      $formatted = $this->formatSurfaceAmount((float) $value, (string) ($item->unit_code ?? 'M2'));
      if ($qualification === 'DISPO' || $qualification === '') {
        return $formatted;
      }
      if ($fallback === '' && ($qualification === 'TOTAL' || $qualification === 'ETREF')) {
        $fallback = $formatted;
      }
    }

    return $fallback;
  }

  private function formatSurfaceAmount(float $value, string $unit_code): string {
    $unit = strtolower($unit_code) === 'ha' ? 'ha' : 'm²';
    return number_format($value, 1, ',', ' ') . ' ' . $unit;
  }

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
