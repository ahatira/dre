<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\ViewExecutable;

/**
 * Views integration for feature definition admin listing.
 */
final class FeatureDefinitionViewsHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_views_data().
   */
  #[Hook('views_data')]
  public function viewsData(): array {
    $data = [];

    $data['fb_feature_definition']['table'] = [
      'group' => $this->t('Feature definition'),
      'entity type' => 'fb_feature_definition',
      'entity revision' => FALSE,
      'base' => [
        'field' => 'id',
        'title' => $this->t('Feature definitions'),
        'help' => $this->t('Catalogue of feature definitions used on offers.'),
        'query_id' => 'ps_feature_definition_entity',
      ],
    ];

    $string_fields = [
      'id' => $this->t('Identifier'),
      'label' => $this->t('Feature name'),
      'code' => $this->t('Code'),
      'group' => $this->t('Group ID'),
      'type_driver' => $this->t('Type driver'),
      'source' => $this->t('Source'),
    ];

    foreach ($string_fields as $field => $title) {
      $data['fb_feature_definition'][$field] = [
        'title' => $title,
        'help' => $title,
        'field' => [
          'id' => 'ps_feature_definition_property',
        ],
        'filter' => [
          'id' => 'ps_feature_definition_string',
        ],
        'sort' => [
          'id' => 'standard',
        ],
      ];
    }

    $data['fb_feature_definition']['label']['field']['id'] = 'ps_feature_definition_label';

    $data['fb_feature_definition']['group']['field']['id'] = 'ps_feature_definition_group';
    $data['fb_feature_definition']['group']['filter']['id'] = 'ps_feature_definition_group';
    $data['fb_feature_definition']['type_driver']['field']['id'] = 'ps_feature_definition_type_driver';
    $data['fb_feature_definition']['type_driver']['filter']['id'] = 'ps_feature_definition_type_driver';
    $data['fb_feature_definition']['source']['field']['id'] = 'ps_feature_definition_source';
    $data['fb_feature_definition']['source']['filter']['id'] = 'ps_feature_definition_source';

    $data['fb_feature_definition']['required_asset_types'] = [
      'title' => $this->t('Required asset types'),
      'help' => $this->t('Required asset types'),
      'field' => [
        'id' => 'ps_feature_definition_asset_types',
      ],
    ];

    foreach (['status', 'expose_as_filter', 'type_locked'] as $boolean_field) {
      $data['fb_feature_definition'][$boolean_field] = [
        'title' => match ($boolean_field) {
          'status' => $this->t('Active'),
          'expose_as_filter' => $this->t('Expose as search filter'),
          'type_locked' => $this->t('Lock value type'),
        },
        'help' => match ($boolean_field) {
          'status' => $this->t('Whether the feature definition is active.'),
          'expose_as_filter' => $this->t('Whether the feature is exposed as a search filter.'),
          'type_locked' => $this->t('Whether the value type is locked against imports.'),
        },
        'field' => [
          'id' => 'ps_feature_definition_property',
        ],
        'filter' => [
          'id' => 'ps_feature_definition_boolean',
        ],
        'sort' => [
          'id' => 'standard',
        ],
      ];
    }

    $data['fb_feature_definition']['weight'] = [
      'title' => $this->t('Weight'),
      'help' => $this->t('Weight'),
      'field' => [
        'id' => 'ps_feature_definition_property',
      ],
      'filter' => [
        'id' => 'numeric',
      ],
      'sort' => [
        'id' => 'standard',
      ],
    ];

    $data['fb_feature_definition']['search'] = [
      'title' => $this->t('Search'),
      'help' => $this->t('Search feature name or code.'),
      'filter' => [
        'id' => 'ps_feature_definition_search',
        'real field' => 'label',
      ],
    ];

    $data['fb_feature_definition']['operations'] = [
      'title' => $this->t('Operations'),
      'help' => $this->t('Operations'),
      'field' => [
        'id' => 'ps_feature_definition_operations',
      ],
    ];

    $data['fb_feature_definition']['fb_feature_definition_bulk_form'] = [
      'title' => $this->t('Bulk operations'),
      'help' => $this->t('Allows selection of multiple feature definitions for bulk actions.'),
      'field' => [
        'id' => 'ps_feature_definition_bulk_form',
      ],
    ];

    return $data;
  }

  /**
   * Implements hook_views_pre_view().
   */
  #[Hook('views_pre_view')]
  public function viewsPreView(ViewExecutable $view, string $display_id, array &$args): void {
    if ($view->id() !== 'ps_feature_definitions_admin') {
      return;
    }

    $input = $view->getExposedInput();
    foreach (['group', 'type_driver', 'source'] as $identifier) {
      if (!array_key_exists($identifier, $input)) {
        continue;
      }
      if ($input[$identifier] === '' || $input[$identifier] === []) {
        unset($input[$identifier]);
      }
    }
    $view->setExposedInput($input);
  }

  /**
   * Implements hook_views_pre_render().
   */
  #[Hook('views_pre_render')]
  public function viewsPreRender(ViewExecutable $view): void {
    if ($view->id() !== 'ps_feature_definitions_admin') {
      return;
    }
    $view->element['#attributes']['class'][] = 'view-ps-feature-definitions-admin';
    $view->element['#attached']['library'][] = 'ps_feature/admin';
  }

}
