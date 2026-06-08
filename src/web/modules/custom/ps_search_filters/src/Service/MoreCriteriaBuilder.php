<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds grouped "More criteria" checkbox options for the search filter bar.
 */
final class MoreCriteriaBuilder {

  use StringTranslationTrait;

  private const EQUIPMENT_GROUP = 'equipements';

  private const SERVICE_GROUP = 'prestations_de_service';

  private const BUILDING_TYPE_IDS = [
    'type_etat_du_batiment__tec_immeuble_coproprit',
    'type_etat_du_batiment__tec_immeuble_indpendant',
  ];

  private const ACCESSIBILITY_ID = 'amenagements__tec_accs_pers_mobilit_rduite';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Returns grouped filter options for the More criteria popin.
   *
   * @return array<string, array<string, mixed>>
   *   Groups keyed by machine name.
   */
  public function build(): array {
    $definitions = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->loadMultiple();

    $equipments = [];
    $services = [];
    $building_types = [];

    foreach ($definitions as $definition) {
      if (!$definition->status() || !$definition->isExposeAsFilter()) {
        continue;
      }

      $id = (string) $definition->id();
      $group = (string) $definition->getGroup();
      $type_driver = (string) $definition->getTypeDriver();

      if ($group === self::EQUIPMENT_GROUP && $type_driver === 'flag') {
        $equipments[] = [
          'id' => $id,
          'label' => $definition->label(),
        ];
      }

      if ($group === self::SERVICE_GROUP && $type_driver === 'flag') {
        $services[] = [
          'id' => $id,
          'label' => $definition->label(),
        ];
      }

      if (in_array($id, self::BUILDING_TYPE_IDS, TRUE)) {
        $building_types[] = [
          'id' => $id,
          'label' => $definition->label(),
        ];
      }
    }

    usort($equipments, static fn(array $a, array $b): int => strcasecmp($a['label'], $b['label']));
    usort($services, static fn(array $a, array $b): int => strcasecmp($a['label'], $b['label']));

    return [
      'accessibility' => [
        'label' => (string) $this->t('Accessibility'),
        'items' => [
          [
            'id' => self::ACCESSIBILITY_ID,
            'param' => 'feature_accessibility',
            'label' => $this->getDefinitionLabel($definitions, self::ACCESSIBILITY_ID)
              ?? (string) $this->t('Access for people with reduced mobility'),
            'type' => 'boolean',
          ],
        ],
      ],
      'equipments' => [
        'label' => (string) $this->t('Equipments'),
        'param' => 'feature_equipments',
        'items' => $equipments,
      ],
      'services' => [
        'label' => (string) $this->t('Services'),
        'param' => 'feature_services',
        'items' => $services,
      ],
      'building_types' => [
        'label' => (string) $this->t('Building type'),
        'param' => 'feature_building_type',
        'items' => $building_types,
      ],
    ];
  }

  /**
   * Loads a feature definition label when available.
   */
  private function getDefinitionLabel(array $definitions, string $id): ?string {
    if (!isset($definitions[$id])) {
      return NULL;
    }
    return (string) $definitions[$id]->label();
  }

}
